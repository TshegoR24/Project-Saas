<?php

namespace App\Services;

use App\Models\User;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Generate comprehensive financial report data
     */
    public function generateReport(User $user, array $dateRange): array
    {
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
        // Get expenses for the period
        $expenses = $user->expenses()
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        // Get budgets for the period
        $budgets = $user->budgets()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->get();

        // Get active subscriptions
        $subscriptions = $user->subscriptions()
            ->where('is_active', true)
            ->get();

        // Calculate summary statistics
        $summary = $this->calculateSummary($expenses, $budgets, $subscriptions, $dateRange);

        // Get category breakdown
        $categoryBreakdown = $this->getCategoryBreakdown($expenses);

        // Get monthly trends
        $monthlyTrends = $this->getMonthlyTrends($user, $startDate, $endDate);

        // Get budget performance
        $budgetPerformance = $this->getBudgetPerformance($budgets, $dateRange);

        return [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'report_generated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'type' => $dateRange['type'] ?? 'custom'
            ],
            'summary' => $summary,
            'expenses' => $expenses->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'description' => $expense->description,
                    'category' => $expense->category,
                    'amount' => $expense->amount,
                    'date' => $expense->date->format('Y-m-d'),
                    'created_at' => $expense->created_at->format('Y-m-d H:i:s')
                ];
            }),
            'category_breakdown' => $categoryBreakdown,
            'monthly_trends' => $monthlyTrends,
            'budget_performance' => $budgetPerformance,
            'subscriptions' => $subscriptions->map(function ($subscription) {
                return [
                    'name' => $subscription->name,
                    'amount' => $subscription->amount,
                    'billing_cycle' => $subscription->billing_cycle,
                    'next_due_date' => $subscription->next_due_date?->format('Y-m-d'),
                    'is_active' => $subscription->is_active
                ];
            })
        ];
    }

    /**
     * Generate PDF report
     */
    public function generatePdfReport(array $reportData, array $dateRange)
    {
        // For now, we'll return a JSON response
        // In a real implementation, you would use a PDF library like DomPDF or TCPDF
        return response()->json([
            'message' => 'PDF generation not implemented yet',
            'data' => $reportData
        ]);
    }

    /**
     * Generate Excel report
     */
    public function generateExcelReport(array $reportData, array $dateRange)
    {
        // For now, we'll return a JSON response
        // In a real implementation, you would use a library like Laravel Excel
        return response()->json([
            'message' => 'Excel generation not implemented yet',
            'data' => $reportData
        ]);
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary(Collection $expenses, Collection $budgets, Collection $subscriptions, array $dateRange): array
    {
        $totalExpenses = $expenses->sum('amount');
        $expenseCount = $expenses->count();
        $averageExpense = $expenseCount > 0 ? $totalExpenses / $expenseCount : 0;

        // Calculate subscription costs
        $monthlySubscriptions = $subscriptions->where('billing_cycle', 'monthly')->sum('amount');
        $yearlySubscriptions = $subscriptions->where('billing_cycle', 'yearly')->sum('amount');
        $totalSubscriptionCost = $monthlySubscriptions + ($yearlySubscriptions / 12);

        // Calculate budget performance
        $totalBudgetAmount = $budgets->sum('limit_amount');
        $totalSpent = $budgets->sum('spent_amount');
        $budgetUtilization = $totalBudgetAmount > 0 ? ($totalSpent / $totalBudgetAmount) * 100 : 0;

        // Get top spending categories
        $topCategories = $expenses->groupBy('category')
            ->map(function ($categoryExpenses) {
                return $categoryExpenses->sum('amount');
            })
            ->sortDesc()
            ->take(5);

        return [
            'total_expenses' => round($totalExpenses, 2),
            'expense_count' => $expenseCount,
            'average_expense' => round($averageExpense, 2),
            'total_subscription_cost' => round($totalSubscriptionCost, 2),
            'total_budget_amount' => round($totalBudgetAmount, 2),
            'total_spent_amount' => round($totalSpent, 2),
            'budget_utilization_percentage' => round($budgetUtilization, 2),
            'budget_savings' => round(max(0, $totalBudgetAmount - $totalSpent), 2),
            'top_categories' => $topCategories->map(function ($amount, $category) {
                return [
                    'category' => $category,
                    'amount' => round($amount, 2)
                ];
            })->values()
        ];
    }

    /**
     * Get category breakdown
     */
    private function getCategoryBreakdown(Collection $expenses): array
    {
        $breakdown = $expenses->groupBy('category')
            ->map(function ($categoryExpenses, $category) {
                $amount = $categoryExpenses->sum('amount');
                $count = $categoryExpenses->count();
                $average = $count > 0 ? $amount / $count : 0;

                return [
                    'category' => $category,
                    'total_amount' => round($amount, 2),
                    'transaction_count' => $count,
                    'average_transaction' => round($average, 2)
                ];
            })
            ->sortDesc()
            ->values();

        $totalAmount = $expenses->sum('amount');

        return $breakdown->map(function ($category) use ($totalAmount) {
            $category['percentage'] = $totalAmount > 0 
                ? round(($category['total_amount'] / $totalAmount) * 100, 2) 
                : 0;
            return $category;
        })->toArray();
    }

    /**
     * Get monthly trends
     */
    private function getMonthlyTrends(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $trends = [];
        $current = $startDate->copy()->startOfMonth();

        while ($current->lte($endDate)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthlyExpenses = $user->expenses()
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');

            $trends[] = [
                'month' => $current->format('M Y'),
                'amount' => round($monthlyExpenses, 2),
                'date' => $current->format('Y-m-01')
            ];

            $current->addMonth();
        }

        return $trends;
    }

    /**
     * Get budget performance
     */
    private function getBudgetPerformance(Collection $budgets, array $dateRange): array
    {
        $performance = [
            'total_budgets' => $budgets->count(),
            'active_budgets' => $budgets->where('is_active', true)->count(),
            'budgets_on_track' => 0,
            'budgets_exceeded' => 0,
            'budgets_near_limit' => 0,
            'total_budget_amount' => 0,
            'total_spent_amount' => 0,
            'average_utilization' => 0
        ];

        if ($budgets->count() > 0) {
            $utilizations = [];
            
            foreach ($budgets as $budget) {
                $budget->updateSpentAmount();
                
                $performance['total_budget_amount'] += $budget->limit_amount;
                $performance['total_spent_amount'] += $budget->spent_amount;
                
                $utilization = $budget->utilization_percentage;
                $utilizations[] = $utilization;

                if ($budget->is_exceeded) {
                    $performance['budgets_exceeded']++;
                } elseif ($budget->is_near_limit) {
                    $performance['budgets_near_limit']++;
                } else {
                    $performance['budgets_on_track']++;
                }
            }

            $performance['average_utilization'] = count($utilizations) > 0 
                ? round(array_sum($utilizations) / count($utilizations), 2) 
                : 0;
        }

        return $performance;
    }
}
