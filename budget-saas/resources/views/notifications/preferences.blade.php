<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notification Preferences') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('notifications.preferences.update') }}">
                        @csrf
                        
                        <div class="space-y-8">
                            <!-- General Notification Settings -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Email Notifications</label>
                                            <p class="text-sm text-gray-500">Receive notifications via email</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="email_notifications" value="1" 
                                                   {{ $preferences->email_notifications ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">In-App Notifications</label>
                                            <p class="text-sm text-gray-500">Show notifications within the application</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="in_app_notifications" value="1" 
                                                   {{ $preferences->in_app_notifications ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget Alerts -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Budget Alerts</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Budget Alerts</label>
                                            <p class="text-sm text-gray-500">Get notified when approaching budget limits</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="budget_alerts" value="1" 
                                                   {{ $preferences->budget_alerts ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="budget_alert_threshold" class="block text-sm font-medium text-gray-700 mb-2">
                                                Alert Threshold (%)
                                            </label>
                                            <input type="number" id="budget_alert_threshold" name="budget_alert_threshold" 
                                                   value="{{ $preferences->budget_alert_threshold }}" 
                                                   min="1" max="100" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <p class="text-xs text-gray-500 mt-1">Alert when budget usage reaches this percentage</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Reminders -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Reminders</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Payment Reminders</label>
                                            <p class="text-sm text-gray-500">Get reminded about upcoming subscription payments</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="payment_reminders" value="1" 
                                                   {{ $preferences->payment_reminders ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="payment_reminder_days" class="block text-sm font-medium text-gray-700 mb-2">
                                                Reminder Days
                                            </label>
                                            <input type="number" id="payment_reminder_days" name="payment_reminder_days" 
                                                   value="{{ $preferences->payment_reminder_days }}" 
                                                   min="1" max="30" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <p class="text-xs text-gray-500 mt-1">Days before due date to send reminder</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Spending Warnings -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Spending Warnings</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Spending Warnings</label>
                                            <p class="text-sm text-gray-500">Get warned about unusual spending patterns</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="spending_warnings" value="1" 
                                                   {{ $preferences->spending_warnings ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Reports -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Summary Reports</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Weekly Summaries</label>
                                            <p class="text-sm text-gray-500">Receive weekly spending summaries</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="weekly_summaries" value="1" 
                                                   {{ $preferences->weekly_summaries ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Monthly Reports</label>
                                            <p class="text-sm text-gray-500">Receive monthly financial reports</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="monthly_reports" value="1" 
                                                   {{ $preferences->monthly_reports ? 'checked' : '' }}
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
