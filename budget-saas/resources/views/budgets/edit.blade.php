<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Budget') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('budgets.update', $budget) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Budget Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Budget Name *
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $budget->name) }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Category *
                                </label>
                                <select name="category" 
                                        id="category" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                        required>
                                    <option value="">Select a category</option>
                                    <option value="Food & Dining" {{ old('category', $budget->category) == 'Food & Dining' ? 'selected' : '' }}>Food & Dining</option>
                                    <option value="Transportation" {{ old('category', $budget->category) == 'Transportation' ? 'selected' : '' }}>Transportation</option>
                                    <option value="Shopping" {{ old('category', $budget->category) == 'Shopping' ? 'selected' : '' }}>Shopping</option>
                                    <option value="Entertainment" {{ old('category', $budget->category) == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                                    <option value="Bills & Utilities" {{ old('category', $budget->category) == 'Bills & Utilities' ? 'selected' : '' }}>Bills & Utilities</option>
                                    <option value="Healthcare" {{ old('category', $budget->category) == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                    <option value="Education" {{ old('category', $budget->category) == 'Education' ? 'selected' : '' }}>Education</option>
                                    <option value="Travel" {{ old('category', $budget->category) == 'Travel' ? 'selected' : '' }}>Travel</option>
                                    <option value="Other" {{ old('category', $budget->category) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Budget Amount -->
                            <div>
                                <label for="limit_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Budget Limit *
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" 
                                           name="limit_amount" 
                                           id="limit_amount" 
                                           step="0.01"
                                           min="0.01"
                                           value="{{ old('limit_amount', $budget->limit_amount) }}"
                                           class="pl-7 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                           required>
                                </div>
                                @error('limit_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Budget Period -->
                            <div>
                                <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Budget Period *
                                </label>
                                <select name="period" 
                                        id="period" 
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                                        required>
                                    <option value="">Select period</option>
                                    <option value="monthly" {{ old('period', $budget->period) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ old('period', $budget->period) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                                @error('period')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Active Status -->
                            <div>
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           id="is_active" 
                                           value="1"
                                           {{ old('is_active', $budget->is_active) ? 'checked' : '' }}
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                        Active Budget
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Inactive budgets won't track expenses or send alerts
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ route('budgets.show', $budget) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                Update Budget
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
