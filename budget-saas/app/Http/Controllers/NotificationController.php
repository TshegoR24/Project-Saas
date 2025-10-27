<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function unread(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = $this->notificationService->getUnreadNotifications($user);
        $count = $this->notificationService->getNotificationCount($user);

        return response()->json([
            'notifications' => $notifications,
            'count' => $count,
        ]);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        $this->authorize('update', $notification);
        
        $this->notificationService->markNotificationAsRead($notification);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Notification $notification): JsonResponse
    {
        $this->authorize('delete', $notification);
        
        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function preferences(Request $request)
    {
        $user = $request->user();
        $preferences = $user->getNotificationPreferences();

        return view('notifications.preferences', compact('preferences'));
    }

    public function updatePreferences(Request $request)
    {
        $user = $request->user();
        $preferences = $user->getNotificationPreferences();

        $validated = $request->validate([
            'budget_alerts' => 'boolean',
            'payment_reminders' => 'boolean',
            'spending_warnings' => 'boolean',
            'weekly_summaries' => 'boolean',
            'monthly_reports' => 'boolean',
            'email_notifications' => 'boolean',
            'in_app_notifications' => 'boolean',
            'budget_alert_threshold' => 'integer|min:1|max:100',
            'payment_reminder_days' => 'integer|min:1|max:30',
        ]);

        $preferences->update($validated);

        return redirect()->route('notifications.preferences')
            ->with('success', 'Notification preferences updated successfully.');
    }
}
