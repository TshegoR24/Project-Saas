<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Budget;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\AnalyticsService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    protected $analyticsService;
    protected $reportService;

    public function __construct(AnalyticsService $analyticsService, ReportService $reportService)
    {
        $this->analyticsService = $analyticsService;
        $this->reportService = $reportService;
    }

    /**
     * Display the analytics dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $currentMonth = Carbon::now();
        
        // Get overview data
        $overview = $this->analyticsService->getOverviewData($user, $currentMonth);
        
        // Get spending trends
        $spendingTrends = $this->analyticsService->getSpendingTrends($user, 12);
        
        // Get category breakdown
        $categoryBreakdown = $this->analyticsService->getCategoryBreakdown($user, $currentMonth);
        
        // Get budget performance
        $budgetPerformance = $this->analyticsService->getBudgetPerformance($user, $currentMonth);
        
        return view('analytics.index', compact(
            'overview', 
            'spendingTrends', 
            'categoryBreakdown', 
            'budgetPerformance'
        ));
    }

    /**
     * Get spending trends data for charts
     */
    public function getSpendingTrends(Request $request): JsonResponse
    {
        $months = $request->get('months', 12);
        $user = Auth::user();
        
        $trends = $this->analyticsService->getSpendingTrends($user, $months);
        
        return response()->json($trends);
    }

    /**
     * Get category breakdown data
     */
    public function getCategoryBreakdown(Request $request): JsonResponse
    {
        $period = $request->get('period', 'current_month');
        $user = Auth::user();
        
        $breakdown = $this->analyticsService->getCategoryBreakdown($user, $period);
        
        return response()->json($breakdown);
    }

    /**
     * Generate and download financial report
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:monthly,yearly,custom',
            'start_date' => 'required_if:type,custom|date',
            'end_date' => 'required_if:type,custom|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel,json'
        ]);

        $user = Auth::user();
        $type = $request->type;
        $format = $request->format;

        // Determine date range
        $dateRange = $this->getDateRange($type, $request);

        // Generate report data
        $reportData = $this->reportService->generateReport($user, $dateRange);

        // Generate file based on format
        switch ($format) {
            case 'pdf':
                return $this->reportService->generatePdfReport($reportData, $dateRange);
            case 'excel':
                return $this->reportService->generateExcelReport($reportData, $dateRange);
            case 'json':
                return response()->json($reportData);
        }
    }

    /**
     * Get budget performance analytics
     */
    public function getBudgetPerformance(Request $request): JsonResponse
    {
        $period = $request->get('period', 'current_month');
        $user = Auth::user();
        
        $performance = $this->analyticsService->getBudgetPerformance($user, $period);
        
        return response()->json($performance);
    }

    /**
     * Get spending predictions
     */
    public function getSpendingPredictions(): JsonResponse
    {
        $user = Auth::user();
        $predictions = $this->analyticsService->getSpendingPredictions($user);
        
        return response()->json($predictions);
    }

    /**
     * Get expense trends by category
     */
    public function getExpenseTrendsByCategory(Request $request): JsonResponse
    {
        $months = $request->get('months', 6);
        $user = Auth::user();
        
        $trends = $this->analyticsService->getExpenseTrendsByCategory($user, $months);
        
        return response()->json($trends);
    }

    /**
     * Get subscription cost analysis
     */
    public function getSubscriptionAnalysis(): JsonResponse
    {
        $user = Auth::user();
        $analysis = $this->analyticsService->getSubscriptionAnalysis($user);
        
        return response()->json($analysis);
    }

    /**
     * Helper method to determine date range
     */
    private function getDateRange(string $type, Request $request): array
    {
        switch ($type) {
            case 'monthly':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'yearly':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $start = Carbon::parse($request->start_date);
                $end = Carbon::parse($request->end_date);
                break;
            default:
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
        }

        return [
            'start' => $start,
            'end' => $end,
            'type' => $type
        ];
    }
}
