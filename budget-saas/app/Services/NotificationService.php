<?php

namespace App\Services;

use App\Models\User;
use App\Models\Budget;
use App\Models\Subscription;
use App\Models\Notification;
use App\Jobs\SendNotificationJob;
use Carbon\Carbon;

class NotificationService
{
    public function checkBudgetAlerts(User $user): void
    {
        $preferences = $user->getNotificationPreferences();
        
        if (!$preferences->budget_alerts) {
            return;
        }

        $budgets = $user->budgets()->where('is_active', true)->get();
        
        foreach ($budgets as $budget) {
            $currentSpending = $this->calculateCurrentSpending($budget);
            $percentage = ($currentSpending / $budget->amount) * 100;
            
            if ($percentage >= $preferences->budget_alert_threshold) {
                $this->sendBudgetAlert($user, $budget, $currentSpending, $percentage);
            }
        }
    }

    public function checkPaymentReminders(User $user): void
    {
        $preferences = $user->getNotificationPreferences();
        
        if (!$preferences->payment_reminders) {
            return;
        }

        $reminderDays = $preferences->payment_reminder_days;
        $dueDate = Carbon::now()->addDays($reminderDays);
        
        $subscriptions = $user->subscriptions()
            ->where('next_due_date', '<=', $dueDate)
            ->where('next_due_date', '>=', Carbon::now())
            ->get();
        
        foreach ($subscriptions as $subscription) {
            $daysUntilDue = Carbon::now()->diffInDays($subscription->next_due_date, false);
            $this->sendPaymentReminder($user, $subscription, $daysUntilDue);
        }
    }

    public function sendBudgetAlert(User $user, Budget $budget, float $currentSpending, float $percentage): void
    {
        $title = "Budget Alert: {$budget->name}";
        $message = $this->getBudgetAlertMessage($budget, $currentSpending, $percentage);
        
        $data = [
            'budget_id' => $budget->id,
            'current_spending' => $currentSpending,
            'percentage' => $percentage,
        ];

        SendNotificationJob::dispatch($user, 'budget_alert', $title, $message, $data);
    }

    public function sendPaymentReminder(User $user, Subscription $subscription, int $daysUntilDue): void
    {
        $title = "Payment Reminder: {$subscription->name}";
        $message = $this->getPaymentReminderMessage($subscription, $daysUntilDue);
        
        $data = [
            'subscription_id' => $subscription->id,
            'days_until_due' => $daysUntilDue,
        ];

        SendNotificationJob::dispatch($user, 'payment_reminder', $title, $message, $data);
    }

    public function sendSpendingWarning(User $user, string $category, float $amount, float $threshold): void
    {
        $title = "Spending Warning: {$category}";
        $message = "You've spent \${$amount} in {$category} this month, which exceeds your warning threshold of \${$threshold}.";
        
        $data = [
            'category' => $category,
            'amount' => $amount,
            'threshold' => $threshold,
        ];

        SendNotificationJob::dispatch($user, 'spending_warning', $title, $message, $data);
    }

    public function sendWeeklySummary(User $user): void
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $totalSpending = $user->expenses()
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->sum('amount');
        
        $title = "Weekly Spending Summary";
        $message = "You spent \${$totalSpending} this week. " . $this->getWeeklySummaryMessage($user, $totalSpending);
        
        $data = [
            'period' => 'weekly',
            'total_spending' => $totalSpending,
            'start_date' => $startOfWeek,
            'end_date' => $endOfWeek,
        ];

        SendNotificationJob::dispatch($user, 'weekly_summary', $title, $message, $data);
    }

    public function sendMonthlyReport(User $user): void
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $totalSpending = $user->expenses()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        $title = "Monthly Spending Report";
        $message = "Your monthly spending report is ready. You spent \${$totalSpending} this month.";
        
        $data = [
            'period' => 'monthly',
            'total_spending' => $totalSpending,
            'start_date' => $startOfMonth,
            'end_date' => $endOfMonth,
        ];

        SendNotificationJob::dispatch($user, 'monthly_report', $title, $message, $data);
    }

    private function calculateCurrentSpending(Budget $budget): float
    {
        $startDate = $budget->period_start ?? Carbon::now()->startOfMonth();
        $endDate = $budget->period_end ?? Carbon::now()->endOfMonth();
        
        return $budget->user->expenses()
            ->where('category', $budget->category)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }

    private function getBudgetAlertMessage(Budget $budget, float $currentSpending, float $percentage): string
    {
        if ($percentage >= 100) {
            return "You have exceeded your budget for {$budget->name} by \${" . number_format($currentSpending - $budget->amount, 2) . "}.";
        } elseif ($percentage >= 90) {
            return "You're very close to your budget limit for {$budget->name}. You've used {$percentage}% of your budget.";
        } else {
            return "You're approaching your budget limit for {$budget->name}. You've used {$percentage}% of your budget.";
        }
    }

    private function getPaymentReminderMessage(Subscription $subscription, int $daysUntilDue): string
    {
        if ($daysUntilDue <= 1) {
            return "Your subscription for {$subscription->name} is due today or tomorrow. Amount: \${$subscription->amount}";
        } else {
            return "Your subscription for {$subscription->name} is due in {$daysUntilDue} days. Amount: \${$subscription->amount}";
        }
    }

    private function getWeeklySummaryMessage(User $user, float $totalSpending): string
    {
        $averageDaily = $totalSpending / 7;
        
        if ($averageDaily > 50) {
            return "Your daily average spending is \${" . number_format($averageDaily, 2) . "}, which is quite high.";
        } elseif ($averageDaily > 25) {
            return "Your daily average spending is \${" . number_format($averageDaily, 2) . "}.";
        } else {
            return "Great job managing your spending! Your daily average is \${" . number_format($averageDaily, 2) . "}.";
        }
    }

    public function markNotificationAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    public function getUnreadNotifications(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $user->notifications()
            ->unread()
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getNotificationCount(User $user): int
    {
        return $user->notifications()->unread()->count();
    }
}
