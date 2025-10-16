<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Subscription Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('subscriptions.edit', $subscription) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('subscriptions.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subscription Name
                            </label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $subscription->name }}
                                </span>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Amount
                            </label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-2xl font-bold text-blue-600">
                                    ${{ number_format($subscription->amount, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Billing Cycle -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Billing Cycle
                            </label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $subscription->billing_cycle === 'monthly' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ ucfirst($subscription->billing_cycle) }}
                                </span>
                            </div>
                        </div>

                        <!-- Next Due Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Next Due Date
                            </label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-lg text-gray-900 dark:text-gray-100">
                                    {{ $subscription->next_due_date->format('F d, Y') }}
                                </span>
                                @if($subscription->next_due_date->diffInDays(now()) <= 7)
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Due Soon
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Monthly Cost -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Monthly Cost
                            </label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    ${{ number_format($subscription->billing_cycle === 'yearly' ? $subscription->amount / 12 : $subscription->amount, 2) }}
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">per month</span>
                            </div>
                        </div>

                        <!-- Created At -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Created On
                            </label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-lg text-gray-900 dark:text-gray-100">
                                    {{ $subscription->created_at->format('F d, Y \a\t g:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-3">
                                <a href="{{ route('subscriptions.edit', $subscription) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Subscription
                                </a>
                                
                                <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            onclick="return confirm('Are you sure you want to delete this subscription?')">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete Subscription
                                    </button>
                                </form>
                            </div>
                            
                            <a href="{{ route('subscriptions.index') }}" 
                               class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                                ← Back to Subscriptions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
