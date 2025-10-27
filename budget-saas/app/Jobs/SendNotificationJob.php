<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Notification;
use App\Mail\BudgetAlertMail;
use App\Mail\PaymentReminderMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $type,
        public string $title,
        public string $message,
        public array $data = [],
        public bool $sendEmail = true
    ) {}

    public function handle(): void
    {
        try {
            // Create in-app notification
            $notification = Notification::create([
                'user_id' => $this->user->id,
                'type' => $this->type,
                'title' => $this->title,
                'message' => $this->message,
                'data' => $this->data,
            ]);

            // Send email if enabled and user preferences allow
            if ($this->sendEmail && $this->shouldSendEmail()) {
                $this->sendEmailNotification($notification);
            }

            $notification->markAsSent();
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage(), [
                'user_id' => $this->user->id,
                'type' => $this->type,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function shouldSendEmail(): bool
    {
        $preferences = $this->user->getNotificationPreferences();
        
        if (!$preferences->email_notifications) {
            return false;
        }

        return match($this->type) {
            'budget_alert' => $preferences->budget_alerts,
            'payment_reminder' => $preferences->payment_reminders,
            'spending_warning' => $preferences->spending_warnings,
            'weekly_summary' => $preferences->weekly_summaries,
            'monthly_report' => $preferences->monthly_reports,
            default => true,
        };
    }

    private function sendEmailNotification(Notification $notification): void
    {
        switch ($this->type) {
            case 'budget_alert':
                if (isset($this->data['budget_id'])) {
                    $budget = $this->user->budgets()->find($this->data['budget_id']);
                    if ($budget) {
                        Mail::to($this->user->email)->send(
                            new BudgetAlertMail(
                                $this->user,
                                $budget,
                                $this->data['current_spending'] ?? 0,
                                $this->data['percentage'] ?? 0
                            )
                        );
                    }
                }
                break;
                
            case 'payment_reminder':
                if (isset($this->data['subscription_id'])) {
                    $subscription = $this->user->subscriptions()->find($this->data['subscription_id']);
                    if ($subscription) {
                        Mail::to($this->user->email)->send(
                            new PaymentReminderMail(
                                $this->user,
                                $subscription,
                                $this->data['days_until_due'] ?? 0
                            )
                        );
                    }
                }
                break;
                
            default:
                // Generic email notification
                Mail::to($this->user->email)->send(
                    new \App\Mail\GenericNotificationMail($this->user, $notification)
                );
                break;
        }
    }
}
