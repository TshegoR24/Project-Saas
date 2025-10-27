<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Search Results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('search.global') }}" method="GET" class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" 
                                   name="q" 
                                   value="{{ $query }}"
                                   placeholder="Search expenses, subscriptions, payments, budgets..."
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            @if($query)
                <!-- Search Results Summary -->
                <div class="mb-6">
                    <p class="text-gray-600 dark:text-gray-400">
                        Search results for "<span class="font-semibold">{{ $query }}</span>"
                        ({{ $expenses->count() + $subscriptions->count() + $payments->count() + $budgets->count() }} results)
                    </p>
                </div>

                <!-- Expenses Results -->
                @if($expenses->count() > 0)
                <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Expenses ({{ $expenses->count() }})
                        </h3>
                        <div class="space-y-3">
                            @foreach($expenses as $expense)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $expense->category }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $expense->date->format('M d, Y') }}</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-lg font-semibold text-red-600">-${{ number_format($expense->amount, 2) }}</span>
                                        <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Subscriptions Results -->
                @if($subscriptions->count() > 0)
                <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Subscriptions ({{ $subscriptions->count() }})
                        </h3>
                        <div class="space-y-3">
                            @foreach($subscriptions as $subscription)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $subscription->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($subscription->billing_cycle) }} • Due {{ $subscription->next_due_date->format('M d, Y') }}</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-lg font-semibold text-blue-600">${{ number_format($subscription->amount, 2) }}</span>
                                        <a href="{{ route('subscriptions.show', $subscription) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Payments Results -->
                @if($payments->count() > 0)
                <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Payments ({{ $payments->count() }})
                        </h3>
                        <div class="space-y-3">
                            @foreach($payments as $payment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $payment->provider }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-lg font-semibold text-green-600">${{ number_format($payment->amount, 2) }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Budgets Results -->
                @if($budgets->count() > 0)
                <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Budgets ({{ $budgets->count() }})
                        </h3>
                        <div class="space-y-3">
                            @foreach($budgets as $budget)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $budget->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $budget->category }} • {{ ucfirst($budget->period) }}</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-lg font-semibold text-purple-600">${{ number_format($budget->limit_amount, 2) }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $budget->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                            {{ $budget->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <a href="{{ route('budgets.show', $budget) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- No Results -->
                @if($expenses->count() == 0 && $subscriptions->count() == 0 && $payments->count() == 0 && $budgets->count() == 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No results found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try searching with different keywords.</p>
                    </div>
                </div>
                @endif
            @else
                <!-- No Search Query -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Search your financial data</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter a search term above to find expenses, subscriptions, payments, and budgets.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>




