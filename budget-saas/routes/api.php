<?php

use App\Http\Controllers\Api\AnalyticsApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Analytics API Routes
Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
    Route::get('/overview', [AnalyticsApiController::class, 'overview']);
    Route::get('/spending-trends', [AnalyticsApiController::class, 'spendingTrends']);
    Route::get('/category-breakdown', [AnalyticsApiController::class, 'categoryBreakdown']);
    Route::get('/budget-performance', [AnalyticsApiController::class, 'budgetPerformance']);
    Route::get('/predictions', [AnalyticsApiController::class, 'predictions']);
    Route::get('/expense-trends', [AnalyticsApiController::class, 'expenseTrendsByCategory']);
    Route::get('/subscription-analysis', [AnalyticsApiController::class, 'subscriptionAnalysis']);
});
