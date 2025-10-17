<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SearchController extends Controller
{
    /**
     * Global search across all user data
     */
    public function global(Request $request)
    {
        $query = $request->get('q', '');
        $user = Auth::user();
        
        if (empty($query)) {
            return view('search.results', [
                'query' => $query,
                'expenses' => collect(),
                'subscriptions' => collect(),
                'payments' => collect(),
                'budgets' => collect(),
            ]);
        }

        // Search expenses
        $expenses = Expense::where('user_id', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('category', 'like', "%{$query}%")
                  ->orWhere('amount', 'like', "%{$query}%");
            })
            ->latest()
            ->limit(10)
            ->get();

        // Search subscriptions
        $subscriptions = Subscription::where('user_id', $user->id)
            ->where('name', 'like', "%{$query}%")
            ->latest()
            ->limit(10)
            ->get();

        // Search payments
        $payments = Payment::where('user_id', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('provider', 'like', "%{$query}%")
                  ->orWhere('amount', 'like', "%{$query}%");
            })
            ->latest()
            ->limit(10)
            ->get();

        // Search budgets
        $budgets = Budget::where('user_id', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            })
            ->latest()
            ->limit(10)
            ->get();

        return view('search.results', compact(
            'query',
            'expenses',
            'subscriptions',
            'payments',
            'budgets'
        ));
    }

    /**
     * Advanced filtering for expenses
     */
    public function expenses(Request $request)
    {
        $user = Auth::user();
        $query = Expense::where('user_id', $user->id);

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Amount range filter
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'amount':
                $query->orderBy('amount', $sortOrder);
                break;
            case 'category':
                $query->orderBy('category', $sortOrder);
                break;
            default:
                $query->orderBy('date', $sortOrder);
        }

        $expenses = $query->paginate(20);

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Export expenses to CSV
     */
    public function exportExpenses(Request $request)
    {
        $user = Auth::user();
        $query = Expense::where('user_id', $user->id);

        // Apply same filters as search
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        $expenses = $query->orderBy('date', 'desc')->get();

        $filename = 'expenses_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['Date', 'Category', 'Amount', 'Created At']);

            // CSV data
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->date->format('Y-m-d'),
                    $expense->category,
                    $expense->amount,
                    $expense->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get expense categories for filtering
     */
    public function getCategories()
    {
        $user = Auth::user();
        $categories = Expense::where('user_id', $user->id)
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return response()->json($categories);
    }
}