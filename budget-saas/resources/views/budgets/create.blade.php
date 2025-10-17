<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Budget') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('budgets.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Budget Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Budget Name *
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}"
                                       placeholder="e.g., Monthly Food Budget"
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
                                    <option value="Food & Dining" {{ old('category') == 'Food & Dining' ? 'selected' : '' }}>Food & Dining</option>
                                    <option value="Transportation" {{ old('category') == 'Transportation' ? 'selected' : '' }}>Transportation</option>
                                    <option value="Shopping" {{ old('category') == 'Shopping' ? 'selected' : '' }}>Shopping</option>
                                    <option value="Entertainment" {{ old('category') == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                                    <option value="Bills & Utilities" {{ old('category') == 'Bills & Utilities' ? 'selected' : '' }}>Bills & Utilities</option>
                                    <option value="Healthcare" {{ old('category') == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                    <option value="Education" {{ old('category') == 'Education' ? 'selected' : '' }}>Education</option>
                                    <option value="Travel" {{ old('category') == 'Travel' ? 'selected' : '' }}>Travel</option>
                                    <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
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
                                           value="{{ old('limit_amount') }}"
                                           placeholder="500.00"
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
                                    <option value="monthly" {{ old('period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ old('period') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                                @error('period')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                        Budget Information
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>The budget will automatically track expenses in the selected category</li>
                                            <li>Monthly budgets reset at the beginning of each month</li>
                                            <li>Yearly budgets reset at the beginning of each year</li>
                                            <li>You'll receive alerts when approaching your budget limit</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ route('budgets.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                Create Budget
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
