<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AnalyticsApiController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get overview analytics data
     */
    public function overview(Request $request): JsonResponse
    {
        $user = Auth::user();
        $period = $request->get('period', 'current_month');
        
        $overview = $this->analyticsService->getOverviewData($user, $period);
        
        return response()->json([
            'success' => true,
            'data' => $overview
        ]);
    }

    /**
     * Get spending trends
     */
    public function spendingTrends(Request $request): JsonResponse
    {
        $months = $request->get('months', 12);
        $user = Auth::user();
        
        $trends = $this->analyticsService->getSpendingTrends($user, $months);
        
        return response()->json([
            'success' => true,
            'data' => $trends
        ]);
    }

    /**
     * Get category breakdown
     */
    public function categoryBreakdown(Request $request): JsonResponse
    {
        $period = $request->get('period', 'current_month');
        $user = Auth::user();
        
        $breakdown = $this->analyticsService->getCategoryBreakdown($user, $period);
        
        return response()->json([
            'success' => true,
            'data' => $breakdown
        ]);
    }

    /**
     * Get budget performance
     */
    public function budgetPerformance(Request $request): JsonResponse
    {
        $period = $request->get('period', 'current_month');
        $user = Auth::user();
        
        $performance = $this->analyticsService->getBudgetPerformance($user, $period);
        
        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }

    /**
     * Get spending predictions
     */
    public function predictions(): JsonResponse
    {
        $user = Auth::user();
        $predictions = $this->analyticsService->getSpendingPredictions($user);
        
        return response()->json([
            'success' => true,
            'data' => $predictions
        ]);
    }

    /**
     * Get expense trends by category
     */
    public function expenseTrendsByCategory(Request $request): JsonResponse
    {
        $months = $request->get('months', 6);
        $user = Auth::user();
        
        $trends = $this->analyticsService->getExpenseTrendsByCategory($user, $months);
        
        return response()->json([
            'success' => true,
            'data' => $trends
        ]);
    }

    /**
     * Get subscription analysis
     */
    public function subscriptionAnalysis(): JsonResponse
    {
        $user = Auth::user();
        $analysis = $this->analyticsService->getSubscriptionAnalysis($user);
        
        return response()->json([
            'success' => true,
            'data' => $analysis
        ]);
    }
}
