<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifications') }}
            </h2>
            <div class="flex space-x-2">
                <button onclick="markAllAsRead()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Mark All as Read
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($notifications->count() > 0)
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="border rounded-lg p-4 {{ $notification->is_read ? 'bg-gray-50' : 'bg-white border-l-4 border-l-blue-500' }}" 
                                     id="notification-{{ $notification->id }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="font-semibold text-lg {{ $notification->is_read ? 'text-gray-600' : 'text-gray-900' }}">
                                                    {{ $notification->title }}
                                                </h3>
                                                @if(!$notification->is_read)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        New
                                                    </span>
                                                @endif
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($notification->type === 'budget_alert') bg-red-100 text-red-800
                                                    @elseif($notification->type === 'payment_reminder') bg-yellow-100 text-yellow-800
                                                    @elseif($notification->type === 'spending_warning') bg-orange-100 text-orange-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                                </span>
                                            </div>
                                            <p class="text-gray-600 mt-1">{{ $notification->message }}</p>
                                            <p class="text-sm text-gray-500 mt-2">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex space-x-2 ml-4">
                                            @if(!$notification->is_read)
                                                <button onclick="markAsRead({{ $notification->id }})" 
                                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    Mark as Read
                                                </button>
                                            @endif
                                            <button onclick="deleteNotification({{ $notification->id }})" 
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto h-12 w-12 text-gray-400">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 0 0-15 0v5h5l-5 5-5-5h5v-5a7.5 7.5 0 0 0 15 0v5z"></path>
                                </svg>
                            </div>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                            <p class="mt-1 text-sm text-gray-500">You're all caught up! No new notifications.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notification = document.getElementById(`notification-${notificationId}`);
                    notification.classList.remove('border-l-blue-500', 'bg-white');
                    notification.classList.add('bg-gray-50');
                    
                    const markAsReadBtn = notification.querySelector('button[onclick*="markAsRead"]');
                    if (markAsReadBtn) {
                        markAsReadBtn.remove();
                    }
                    
                    const newBadge = notification.querySelector('.bg-blue-100');
                    if (newBadge) {
                        newBadge.remove();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function markAllAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function deleteNotification(notificationId) {
            if (confirm('Are you sure you want to delete this notification?')) {
                fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`notification-${notificationId}`).remove();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</x-app-layout>
