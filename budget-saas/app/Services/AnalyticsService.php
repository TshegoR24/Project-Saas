<?php

namespace App\Services;

use App\Models\User;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Subscription;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    /**
     * Get overview data for analytics dashboard
     */
    public function getOverviewData(User $user, $period = 'current_month'): array
    {
        $dateRange = $this->getDateRange($period);
        
        // Total expenses for period
        $totalExpenses = $user->expenses()
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');

        // Total subscriptions cost
        $totalSubscriptions = $user->subscriptions()
            ->where('is_active', true)
            ->sum('amount');

        // Budget performance
        $budgets = $user->budgets()
            ->whereBetween('start_date', [$dateRange['start'], $dateRange['end']])
            ->orWhereBetween('end_date', [$dateRange['start'], $dateRange['end']])
            ->get();

        $budgetPerformance = $this->calculateBudgetPerformance($budgets, $dateRange);

        // Previous period comparison
        $previousPeriod = $this->getPreviousPeriod($dateRange);
        $previousExpenses = $user->expenses()
            ->whereBetween('date', [$previousPeriod['start'], $previousPeriod['end']])
            ->sum('amount');

        $expenseChange = $previousExpenses > 0 
            ? (($totalExpenses - $previousExpenses) / $previousExpenses) * 100 
            : 0;

        return [
            'total_expenses' => $totalExpenses,
            'total_subscriptions' => $totalSubscriptions,
            'budget_performance' => $budgetPerformance,
            'expense_change_percentage' => round($expenseChange, 2),
            'period' => $period,
            'date_range' => $dateRange
        ];
    }

    /**
     * Get spending trends over time
     */
    public function getSpendingTrends(User $user, int $months = 12): array
    {
        $trends = [];
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        for ($i = 0; $i < $months; $i++) {
            $month = $startDate->copy()->addMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $expenses = $user->expenses()
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');

            $trends[] = [
                'month' => $month->format('M Y'),
                'amount' => $expenses,
                'date' => $month->format('Y-m-01')
            ];
        }

        return $trends;
    }

    /**
     * Get category breakdown
     */
    public function getCategoryBreakdown(User $user, $period = 'current_month'): array
    {
        $dateRange = $this->getDateRange($period);
        
        $categories = $user->expenses()
            ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
            ->selectRaw('category, SUM(amount) as total_amount, COUNT(*) as transaction_count')
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        $totalAmount = $categories->sum('total_amount');

        return $categories->map(function ($category) use ($totalAmount) {
            return [
                'category' => $category->category,
                'amount' => $category->total_amount,
                'percentage' => $totalAmount > 0 ? round(($category->total_amount / $totalAmount) * 100, 2) : 0,
                'transaction_count' => $category->transaction_count
            ];
        })->toArray();
    }

    /**
     * Get budget performance analytics
     */
    public function getBudgetPerformance(User $user, $period = 'current_month'): array
    {
        $dateRange = $this->getDateRange($period);
        
        $budgets = $user->budgets()
            ->whereBetween('start_date', [$dateRange['start'], $dateRange['end']])
            ->orWhereBetween('end_date', [$dateRange['start'], $dateRange['end']])
            ->get();

        $performance = $this->calculateBudgetPerformance($budgets, $dateRange);

        return [
            'total_budgets' => $budgets->count(),
            'active_budgets' => $budgets->where('is_active', true)->count(),
            'budgets_on_track' => $performance['on_track'],
            'budgets_exceeded' => $performance['exceeded'],
            'budgets_near_limit' => $performance['near_limit'],
            'average_utilization' => $performance['average_utilization'],
            'total_budget_amount' => $budgets->sum('limit_amount'),
            'total_spent_amount' => $performance['total_spent'],
            'savings_achieved' => $performance['savings_achieved']
        ];
    }

    /**
     * Get spending predictions based on historical data
     */
    public function getSpendingPredictions(User $user): array
    {
        // Get last 6 months of data
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();
        $now = Carbon::now();

        $historicalData = $user->expenses()
            ->whereBetween('date', [$sixMonthsAgo, $now])
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        if ($historicalData->count() < 3) {
            return [
                'next_month_prediction' => 0,
                'confidence_level' => 'low',
                'trend' => 'insufficient_data'
            ];
        }

        // Calculate average monthly spending
        $averageSpending = $historicalData->avg('total');
        
        // Calculate trend (simple linear regression)
        $trend = $this->calculateTrend($historicalData);
        
        // Predict next month
        $nextMonthPrediction = $averageSpending + $trend;
        
        // Calculate confidence based on data consistency
        $variance = $this->calculateVariance($historicalData->pluck('total'));
        $confidence = $this->calculateConfidence($variance, $averageSpending);

        return [
            'next_month_prediction' => round($nextMonthPrediction, 2),
            'confidence_level' => $confidence,
            'trend' => $trend > 0 ? 'increasing' : ($trend < 0 ? 'decreasing' : 'stable'),
            'average_monthly_spending' => round($averageSpending, 2),
            'historical_data' => $historicalData
        ];
    }

    /**
     * Get expense trends by category
     */
    public function getExpenseTrendsByCategory(User $user, int $months = 6): array
    {
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $trends = $user->expenses()
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category, DATE_FORMAT(date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy(['category', 'month'])
            ->orderBy('month')
            ->get()
            ->groupBy('category');

        $result = [];
        foreach ($trends as $category => $data) {
            $result[] = [
                'category' => $category,
                'data' => $data->map(function ($item) {
                    return [
                        'month' => $item->month,
                        'amount' => $item->total
                    ];
                })->values()
            ];
        }

        return $result;
    }

    /**
     * Get subscription cost analysis
     */
    public function getSubscriptionAnalysis(User $user): array
    {
        $subscriptions = $user->subscriptions()->where('is_active', true)->get();
        
        $monthlyCost = $subscriptions->where('billing_cycle', 'monthly')->sum('amount');
        $yearlyCost = $subscriptions->where('billing_cycle', 'yearly')->sum('amount');
        $yearlyMonthlyEquivalent = $yearlyCost / 12;
        
        $totalMonthlyCost = $monthlyCost + $yearlyMonthlyEquivalent;
        $totalYearlyCost = ($monthlyCost * 12) + $yearlyCost;

        return [
            'total_subscriptions' => $subscriptions->count(),
            'monthly_cost' => $monthlyCost,
            'yearly_cost' => $yearlyCost,
            'total_monthly_equivalent' => $totalMonthlyCost,
            'total_yearly_cost' => $totalYearlyCost,
            'subscriptions' => $subscriptions->map(function ($subscription) {
                return [
                    'name' => $subscription->name,
                    'amount' => $subscription->amount,
                    'billing_cycle' => $subscription->billing_cycle,
                    'next_due_date' => $subscription->next_due_date
                ];
            })
        ];
    }

    /**
     * Calculate budget performance metrics
     */
    private function calculateBudgetPerformance(Collection $budgets, array $dateRange): array
    {
        $onTrack = 0;
        $exceeded = 0;
        $nearLimit = 0;
        $totalSpent = 0;
        $totalBudget = 0;
        $utilizations = [];

        foreach ($budgets as $budget) {
            // Update spent amount for current period
            $budget->updateSpentAmount();
            
            $totalBudget += $budget->limit_amount;
            $totalSpent += $budget->spent_amount;
            
            $utilization = $budget->utilization_percentage;
            $utilizations[] = $utilization;

            if ($budget->is_exceeded) {
                $exceeded++;
            } elseif ($budget->is_near_limit) {
                $nearLimit++;
            } else {
                $onTrack++;
            }
        }

        $averageUtilization = count($utilizations) > 0 ? array_sum($utilizations) / count($utilizations) : 0;
        $savingsAchieved = max(0, $totalBudget - $totalSpent);

        return [
            'on_track' => $onTrack,
            'exceeded' => $exceeded,
            'near_limit' => $nearLimit,
            'average_utilization' => round($averageUtilization, 2),
            'total_spent' => $totalSpent,
            'savings_achieved' => $savingsAchieved
        ];
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period): array
    {
        switch ($period) {
            case 'current_month':
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth()
                ];
            case 'current_year':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()->endOfYear()
                ];
            case 'last_month':
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth(),
                    'end' => Carbon::now()->subMonth()->endOfMonth()
                ];
            case 'last_year':
                return [
                    'start' => Carbon::now()->subYear()->startOfYear(),
                    'end' => Carbon::now()->subYear()->endOfYear()
                ];
            default:
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth()
                ];
        }
    }

    /**
     * Get previous period for comparison
     */
    private function getPreviousPeriod(array $currentRange): array
    {
        $start = $currentRange['start']->copy();
        $end = $currentRange['end']->copy();
        
        $duration = $start->diffInDays($end);
        
        return [
            'start' => $start->subDays($duration + 1),
            'end' => $end->subDays($duration + 1)
        ];
    }

    /**
     * Calculate trend using simple linear regression
     */
    private function calculateTrend(Collection $data): float
    {
        $n = $data->count();
        if ($n < 2) return 0;

        $x = $data->keys()->map(function ($key) { return $key + 1; });
        $y = $data->values();

        $sumX = $x->sum();
        $sumY = $y->sum();
        $sumXY = $x->zip($y)->map(function ($pair) { return $pair[0] * $pair[1]; })->sum();
        $sumXX = $x->map(function ($val) { return $val * $val; })->sum();

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumXX - $sumX * $sumX);
        
        return $slope;
    }

    /**
     * Calculate variance
     */
    private function calculateVariance(Collection $values): float
    {
        $mean = $values->avg();
        $variance = $values->map(function ($value) use ($mean) {
            return pow($value - $mean, 2);
        })->avg();
        
        return $variance;
    }

    /**
     * Calculate confidence level
     */
    private function calculateConfidence(float $variance, float $mean): string
    {
        if ($mean == 0) return 'low';
        
        $coefficientOfVariation = sqrt($variance) / $mean;
        
        if ($coefficientOfVariation < 0.1) return 'high';
        if ($coefficientOfVariation < 0.3) return 'medium';
        return 'low';
    }
}
