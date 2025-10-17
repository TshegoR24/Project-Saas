<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Resource routes for budget management
    Route::resource('expenses', ExpenseController::class);
    Route::resource('subscriptions', SubscriptionController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('budgets', BudgetController::class);
    Route::post('budgets/{budget}/toggle', [BudgetController::class, 'toggle'])->name('budgets.toggle');
    
    // Search and filtering routes
    Route::get('search', [SearchController::class, 'global'])->name('search.global');
    Route::get('expenses/filter', [SearchController::class, 'expenses'])->name('expenses.filter');
    Route::get('expenses/export', [SearchController::class, 'exportExpenses'])->name('expenses.export');
    Route::get('api/categories', [SearchController::class, 'getCategories'])->name('api.categories');
});

require __DIR__.'/auth.php';
