<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $budget->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('budgets.edit', $budget) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit Budget
                </a>
                <a href="{{ route('budgets.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Budgets
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Budget Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Budget Progress Card -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Budget Progress</h3>
                        
                        <!-- Progress Bar -->
                        <div class="mb-6">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <span>Spent: ${{ number_format($budget->spent_amount, 2) }}</span>
                                <span>Limit: ${{ number_format($budget->limit_amount, 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="h-4 rounded-full {{ $budget->is_exceeded ? 'bg-red-500' : ($budget->is_near_limit ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                     style="width: {{ min($budget->utilization_percentage, 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span>{{ number_format($budget->utilization_percentage, 1) }}% used</span>
                                <span>{{ $budget->period }} budget</span>
                            </div>
                        </div>

                        <!-- Budget Stats -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold {{ $budget->is_exceeded ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                    ${{ number_format($budget->remaining_amount, 2) }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $budget->is_exceeded ? 'Over budget' : 'Remaining' }}
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $budget->category }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Category
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget Info Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Budget Details</h3>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Status
                                </label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $budget->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                    {{ $budget->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Period
                                </label>
                                <span class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ ucfirst($budget->period) }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Period Range
                                </label>
                                <span class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $budget->start_date->format('M d, Y') }} - {{ $budget->end_date->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Created
                                </label>
                                <span class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $budget->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Recent Expenses ({{ $budget->category }})
                        </h3>
                        <a href="{{ route('expenses.create') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            Add Expense
                        </a>
                    </div>
                    
                    @if($expenses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Amount
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Category
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $expense->date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                                -${{ number_format($expense->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    {{ $expense->category }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $expenses->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No expenses in this category</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start tracking expenses in the {{ $budget->category }} category.</p>
                            <div class="mt-6">
                                <a href="{{ route('expenses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add Expense
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
