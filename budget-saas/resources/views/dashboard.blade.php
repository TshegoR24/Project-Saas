<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Budget Alerts -->
            @if($budgetAlerts->count() > 0)
            <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Budget Alerts
                    </h3>
                    <div class="space-y-3">
                        @foreach($budgetAlerts as $budget)
                            <div class="flex items-center justify-between p-3 {{ $budget->is_exceeded ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' }} rounded-lg">
                                <div>
                                    <p class="font-medium {{ $budget->is_exceeded ? 'text-red-800 dark:text-red-200' : 'text-yellow-800 dark:text-yellow-200' }}">
                                        {{ $budget->name }}
                                    </p>
                                    <p class="text-sm {{ $budget->is_exceeded ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                        {{ $budget->is_exceeded ? 'Budget exceeded by $' . number_format(abs($budget->remaining_amount), 2) : 'Near budget limit (' . number_format($budget->utilization_percentage, 1) . '% used)' }}
                                    </p>
                                </div>
                                <a href="{{ route('budgets.show', $budget) }}" class="text-sm font-medium {{ $budget->is_exceeded ? 'text-red-600 hover:text-red-800 dark:text-red-400' : 'text-yellow-600 hover:text-yellow-800 dark:text-yellow-400' }}">
                                    View Details
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Monthly Expenses
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        ${{ number_format($monthlyExpenses, 2) }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Active Subscriptions
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $activeSubscriptions->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Monthly Subscription Cost
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        ${{ number_format($monthlySubscriptionCost, 2) }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Budgets -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                        Active Budgets
                                    </dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $activeBudgets->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Analytics Widget -->
            <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Quick Analytics
                        </h3>
                        <a href="{{ route('analytics.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm font-medium">
                            View Full Analytics →
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Budget Performance -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $activeBudgets->where('is_active', true)->count() > 0 ? 
                                    round($activeBudgets->where('is_active', true)->avg('utilization_percentage'), 1) : 0 }}%
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Avg Budget Usage</div>
                        </div>
                        
                        <!-- Spending Trend -->
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $monthlyTrend && count($monthlyTrend) >= 2 ? 
                                ($monthlyTrend[count($monthlyTrend)-1]['amount'] > $monthlyTrend[count($monthlyTrend)-2]['amount'] ? 'text-red-600' : 'text-green-600') : 'text-gray-600' }}">
                                @if($monthlyTrend && count($monthlyTrend) >= 2)
                                    {{ $monthlyTrend[count($monthlyTrend)-1]['amount'] > $monthlyTrend[count($monthlyTrend)-2]['amount'] ? '↗' : '↘' }}
                                @else
                                    —
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Spending Trend</div>
                        </div>
                        
                        <!-- Top Category -->
                        <div class="text-center">
                            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $expenseCategories->count() > 0 ? $expenseCategories->first()->category : 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Top Spending Category</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense Categories Chart -->
            @if($expenseCategories->count() > 0)
            <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Expense Categories (Current Month)</h3>
                    <div class="relative h-64">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            <!-- Monthly Spending Trend -->
            @if($monthlyTrend && count($monthlyTrend) > 1)
            <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Monthly Spending Trend (Last 6 Months)</h3>
                    <div class="relative h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Expenses -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Recent Expenses
                            </h3>
                            <a href="{{ route('expenses.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                View All
                            </a>
                        </div>
                        <div class="space-y-3">
                            @forelse($recentExpenses as $expense)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $expense->category }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $expense->date->format('M d, Y') }}</p>
                                    </div>
                                    <span class="text-lg font-semibold text-red-600">-${{ number_format($expense->amount, 2) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No expenses recorded yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Upcoming Payments -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Upcoming Payments
                            </h3>
                            <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                View All
                            </a>
                        </div>
                        <div class="space-y-3">
                            @forelse($upcomingPayments as $payment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $payment->provider }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <span class="text-lg font-semibold text-blue-600">${{ number_format($payment->amount, 2) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No upcoming payments.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <a href="{{ route('expenses.create') }}" class="flex items-center justify-center p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                            <div class="text-center">
                                <svg class="h-8 w-8 text-red-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="text-sm font-medium text-red-700 dark:text-red-300">Add Expense</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('subscriptions.create') }}" class="flex items-center justify-center p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <div class="text-center">
                                <svg class="h-8 w-8 text-blue-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Add Subscription</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('payments.create') }}" class="flex items-center justify-center p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                            <div class="text-center">
                                <svg class="h-8 w-8 text-green-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="text-sm font-medium text-green-700 dark:text-green-300">Record Payment</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('budgets.create') }}" class="flex items-center justify-center p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                            <div class="text-center">
                                <svg class="h-8 w-8 text-purple-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm font-medium text-purple-700 dark:text-purple-300">Create Budget</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($expenseCategories->count() > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('expenseChart').getContext('2d');
            const categories = @json($expenseCategories->pluck('category'));
            const totals = @json($expenseCategories->pluck('total'));
            
            const colors = [
                '#EF4444', '#F97316', '#F59E0B', '#10B981', 
                '#06B6D4', '#8B5CF6', '#EC4899', '#6B7280'
            ];
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categories,
                    datasets: [{
                        data: totals,
                        backgroundColor: colors.slice(0, categories.length),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return `${context.label}: $${context.parsed.toFixed(2)} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endif

    @if($monthlyTrend && count($monthlyTrend) > 1)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const trendData = @json($monthlyTrend);
            
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendData.map(item => item.month),
                    datasets: [{
                        label: 'Monthly Spending',
                        data: trendData.map(item => item.amount),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#EF4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Spending: $${context.parsed.y.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toFixed(0);
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endif
</x-app-layout>