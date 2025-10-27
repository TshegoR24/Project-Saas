<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'budget_alerts',
        'payment_reminders',
        'spending_warnings',
        'weekly_summaries',
        'monthly_reports',
        'email_notifications',
        'in_app_notifications',
        'budget_alert_threshold',
        'payment_reminder_days',
    ];

    protected $casts = [
        'budget_alerts' => 'boolean',
        'payment_reminders' => 'boolean',
        'spending_warnings' => 'boolean',
        'weekly_summaries' => 'boolean',
        'monthly_reports' => 'boolean',
        'email_notifications' => 'boolean',
        'in_app_notifications' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaults(): array
    {
        return [
            'budget_alerts' => true,
            'payment_reminders' => true,
            'spending_warnings' => true,
            'weekly_summaries' => true,
            'monthly_reports' => true,
            'email_notifications' => true,
            'in_app_notifications' => true,
            'budget_alert_threshold' => 80,
            'payment_reminder_days' => 3,
        ];
    }
}
