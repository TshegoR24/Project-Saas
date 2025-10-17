<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get current month data
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Get expenses for current month
        $monthlyExpenses = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$currentMonth, $currentMonthEnd])
            ->sum('amount');
        
        // Get all subscriptions (simplified - no is_active field)
        $activeSubscriptions = Subscription::where('user_id', $user->id)->get();
        
        // Calculate monthly subscription cost
        $monthlySubscriptionCost = 0;
        foreach ($activeSubscriptions as $subscription) {
            switch ($subscription->billing_cycle) {
                case 'monthly':
                    $monthlySubscriptionCost += $subscription->amount;
                    break;
                case 'yearly':
                    $monthlySubscriptionCost += $subscription->amount / 12;
                    break;
            }
        }
        
        // Get recent expenses
        $recentExpenses = Expense::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        
        // Get recent payments (simplified - no payment_date field)
        $upcomingPayments = Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();
        
        // Get expense categories for chart
        $expenseCategories = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$currentMonth, $currentMonthEnd])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();
        
        // Get monthly spending trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $monthExpenses = Expense::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $monthlyTrend[] = [
                'month' => $monthStart->format('M Y'),
                'amount' => $monthExpenses
            ];
        }
        
        // Get active budgets
        $activeBudgets = Budget::where('user_id', $user->id)
            ->active()
            ->currentPeriod()
            ->get();
        
        // Update spent amounts for budgets
        foreach ($activeBudgets as $budget) {
            $budget->updateSpentAmount();
        }
        
        // Get budget alerts (budgets near limit or exceeded)
        $budgetAlerts = $activeBudgets->filter(function ($budget) {
            return $budget->is_exceeded || $budget->is_near_limit;
        });
        
        return view('dashboard', compact(
            'monthlyExpenses',
            'activeSubscriptions',
            'monthlySubscriptionCost',
            'recentExpenses',
            'upcomingPayments',
            'expenseCategories',
            'monthlyTrend',
            'activeBudgets',
            'budgetAlerts'
        ));
    }
}
