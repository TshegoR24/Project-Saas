<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckBudgetAlerts extends Command
{
    protected $signature = 'notifications:check-alerts';
    protected $description = 'Check and send budget alerts and payment reminders';

    public function __construct(
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking budget alerts and payment reminders...');

        $users = User::with(['budgets', 'subscriptions', 'notificationPreferences'])->get();
        $alertsSent = 0;
        $remindersSent = 0;

        foreach ($users as $user) {
            try {
                // Check budget alerts
                $this->notificationService->checkBudgetAlerts($user);
                $alertsSent++;

                // Check payment reminders
                $this->notificationService->checkPaymentReminders($user);
                $remindersSent++;

            } catch (\Exception $e) {
                $this->error("Failed to process notifications for user {$user->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed notifications for {$users->count()} users");
        $this->info("Budget alerts checked: {$alertsSent}");
        $this->info("Payment reminders checked: {$remindersSent}");

        return Command::SUCCESS;
    }
}
