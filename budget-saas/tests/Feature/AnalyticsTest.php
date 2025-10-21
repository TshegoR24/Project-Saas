<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Subscription;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $analyticsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->analyticsService = new AnalyticsService();
    }

    /** @test */
    public function it_can_get_overview_data()
    {
        // Create test expenses
        Expense::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'date' => Carbon::now(),
            'category' => 'Food'
        ]);

        Expense::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 50.00,
            'date' => Carbon::now(),
            'category' => 'Transport'
        ]);

        // Create test budget
        Budget::factory()->create([
            'user_id' => $this->user->id,
            'limit_amount' => 200.00,
            'spent_amount' => 150.00,
            'is_active' => true
        ]);

        $overview = $this->analyticsService->getOverviewData($this->user, 'current_month');

        $this->assertArrayHasKey('total_expenses', $overview);
        $this->assertArrayHasKey('total_subscriptions', $overview);
        $this->assertArrayHasKey('budget_performance', $overview);
        $this->assertEquals(150.00, $overview['total_expenses']);
    }

    /** @test */
    public function it_can_get_spending_trends()
    {
        // Create expenses for the last 3 months
        for ($i = 2; $i >= 0; $i--) {
            Expense::factory()->create([
                'user_id' => $this->user->id,
                'amount' => 100.00 * ($i + 1),
                'date' => Carbon::now()->subMonths($i),
                'category' => 'Food'
            ]);
        }

        $trends = $this->analyticsService->getSpendingTrends($this->user, 3);

        $this->assertCount(3, $trends);
        $this->assertArrayHasKey('month', $trends[0]);
        $this->assertArrayHasKey('amount', $trends[0]);
    }

    /** @test */
    public function it_can_get_category_breakdown()
    {
        // Create expenses in different categories
        Expense::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'date' => Carbon::now(),
            'category' => 'Food'
        ]);

        Expense::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 50.00,
            'date' => Carbon::now(),
            'category' => 'Transport'
        ]);

        $breakdown = $this->analyticsService->getCategoryBreakdown($this->user, 'current_month');

        $this->assertCount(2, $breakdown);
        $this->assertEquals('Food', $breakdown[0]['category']);
        $this->assertEquals(100.00, $breakdown[0]['amount']);
        $this->assertEquals(66.67, $breakdown[0]['percentage'], 1);
    }

    /** @test */
    public function it_can_get_budget_performance()
    {
        // Create budgets with different performance
        Budget::factory()->create([
            'user_id' => $this->user->id,
            'limit_amount' => 200.00,
            'spent_amount' => 150.00,
            'is_active' => true
        ]);

        Budget::factory()->create([
            'user_id' => $this->user->id,
            'limit_amount' => 100.00,
            'spent_amount' => 120.00,
            'is_active' => true
        ]);

        $performance = $this->analyticsService->getBudgetPerformance($this->user, 'current_month');

        $this->assertArrayHasKey('total_budgets', $performance);
        $this->assertArrayHasKey('budgets_on_track', $performance);
        $this->assertArrayHasKey('budgets_exceeded', $performance);
        $this->assertEquals(2, $performance['total_budgets']);
        $this->assertEquals(1, $performance['budgets_on_track']);
        $this->assertEquals(1, $performance['budgets_exceeded']);
    }

    /** @test */
    public function it_can_get_spending_predictions()
    {
        // Create historical spending data
        for ($i = 5; $i >= 0; $i--) {
            Expense::factory()->create([
                'user_id' => $this->user->id,
                'amount' => 100.00 + ($i * 10), // Increasing trend
                'date' => Carbon::now()->subMonths($i),
                'category' => 'Food'
            ]);
        }

        $predictions = $this->analyticsService->getSpendingPredictions($this->user);

        $this->assertArrayHasKey('next_month_prediction', $predictions);
        $this->assertArrayHasKey('confidence_level', $predictions);
        $this->assertArrayHasKey('trend', $predictions);
        $this->assertGreaterThan(0, $predictions['next_month_prediction']);
    }

    /** @test */
    public function it_can_get_subscription_analysis()
    {
        // Create subscriptions
        Subscription::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 10.00,
            'billing_cycle' => 'monthly',
            'is_active' => true
        ]);

        Subscription::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 120.00,
            'billing_cycle' => 'yearly',
            'is_active' => true
        ]);

        $analysis = $this->analyticsService->getSubscriptionAnalysis($this->user);

        $this->assertArrayHasKey('total_subscriptions', $analysis);
        $this->assertArrayHasKey('monthly_cost', $analysis);
        $this->assertArrayHasKey('yearly_cost', $analysis);
        $this->assertEquals(2, $analysis['total_subscriptions']);
        $this->assertEquals(10.00, $analysis['monthly_cost']);
        $this->assertEquals(120.00, $analysis['yearly_cost']);
        $this->assertEquals(20.00, $analysis['total_monthly_equivalent']);
    }

    /** @test */
    public function analytics_page_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get('/analytics');

        $response->assertStatus(200);
        $response->assertViewIs('analytics.index');
    }

    /** @test */
    public function analytics_api_endpoints_require_authentication()
    {
        $response = $this->get('/api/analytics/overview');
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_access_analytics_api()
    {
        $response = $this->actingAs($this->user)->get('/api/analytics/overview');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'total_expenses',
                'total_subscriptions',
                'budget_performance',
                'expense_change_percentage'
            ]
        ]);
    }
}
