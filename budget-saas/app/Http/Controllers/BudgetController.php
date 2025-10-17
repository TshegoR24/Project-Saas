<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = Auth::user()->budgets()
            ->active()
            ->currentPeriod()
            ->with(['user'])
            ->latest()
            ->paginate(15);

        // Update spent amounts for all budgets
        foreach ($budgets as $budget) {
            $budget->updateSpentAmount();
        }

        return view('budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('budgets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'limit_amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:monthly,yearly',
        ]);

        $now = Carbon::now();
        $startDate = $request->period === 'monthly' 
            ? $now->startOfMonth() 
            : $now->startOfYear();
        
        $endDate = $request->period === 'monthly' 
            ? $now->endOfMonth() 
            : $now->endOfYear();

        $budgetData = $request->all();
        $budgetData['user_id'] = Auth::id();
        $budgetData['start_date'] = $startDate;
        $budgetData['end_date'] = $endDate;
        $budgetData['spent_amount'] = 0;

        Budget::create($budgetData);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);
        
        // Update spent amount
        $budget->updateSpentAmount();
        
        // Get expenses for this budget period
        $expenses = Auth::user()->expenses()
            ->where('category', $budget->category)
            ->whereBetween('date', [$budget->start_date, $budget->end_date])
            ->latest()
            ->paginate(10);

        return view('budgets.show', compact('budget', 'expenses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        return view('budgets.edit', compact('budget'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'limit_amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:monthly,yearly',
            'is_active' => 'boolean',
        ]);

        $budget->update($request->all());

        return redirect()->route('budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }

    /**
     * Toggle budget active status
     */
    public function toggle(Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $budget->update(['is_active' => !$budget->is_active]);
        
        $status = $budget->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Budget {$status} successfully.");
    }
}
