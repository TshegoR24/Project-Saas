<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Subscription;
use App\Models\Payment;
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
            ->whereBetween('expense_date', [$currentMonth, $currentMonthEnd])
            ->sum('amount');
        
        // Get active subscriptions
        $activeSubscriptions = Subscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
        
        // Calculate monthly subscription cost
        $monthlySubscriptionCost = 0;
        foreach ($activeSubscriptions as $subscription) {
            switch ($subscription->frequency) {
                case 'monthly':
                    $monthlySubscriptionCost += $subscription->amount;
                    break;
                case 'quarterly':
                    $monthlySubscriptionCost += $subscription->amount / 3;
                    break;
                case 'yearly':
                    $monthlySubscriptionCost += $subscription->amount / 12;
                    break;
            }
        }
        
        // Get recent expenses
        $recentExpenses = Expense::where('user_id', $user->id)
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();
        
        // Get upcoming payments
        $upcomingPayments = Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('payment_date', '>=', Carbon::now())
            ->orderBy('payment_date', 'asc')
            ->limit(5)
            ->get();
        
        // Get expense categories for chart
        $expenseCategories = Expense::where('user_id', $user->id)
            ->whereBetween('expense_date', [$currentMonth, $currentMonthEnd])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        return view('dashboard', compact(
            'monthlyExpenses',
            'activeSubscriptions',
            'monthlySubscriptionCost',
            'recentExpenses',
            'upcomingPayments',
            'expenseCategories'
        ));
    }
}
