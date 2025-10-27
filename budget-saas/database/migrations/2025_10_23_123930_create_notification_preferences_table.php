<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('budget_alerts')->default(true);
            $table->boolean('payment_reminders')->default(true);
            $table->boolean('spending_warnings')->default(true);
            $table->boolean('weekly_summaries')->default(true);
            $table->boolean('monthly_reports')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('in_app_notifications')->default(true);
            $table->integer('budget_alert_threshold')->default(80); // Alert when 80% of budget is used
            $table->integer('payment_reminder_days')->default(3); // Remind 3 days before due
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
