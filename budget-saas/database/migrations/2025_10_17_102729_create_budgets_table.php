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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Budget name (e.g., "Monthly Food Budget")
            $table->string('category'); // Category (e.g., "Food & Dining")
            $table->decimal('limit_amount', 10, 2); // Budget limit
            $table->decimal('spent_amount', 10, 2)->default(0); // Amount spent
            $table->date('start_date'); // Budget period start
            $table->date('end_date'); // Budget period end
            $table->enum('period', ['monthly', 'yearly'])->default('monthly');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
