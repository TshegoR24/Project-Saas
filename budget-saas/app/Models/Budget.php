<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'limit_amount',
        'spent_amount',
        'start_date',
        'end_date',
        'period',
        'is_active',
    ];

    protected $casts = [
        'limit_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Calculate remaining budget amount
    public function getRemainingAmountAttribute()
    {
        return $this->limit_amount - $this->spent_amount;
    }

    // Calculate budget utilization percentage
    public function getUtilizationPercentageAttribute()
    {
        if ($this->limit_amount == 0) return 0;
        return ($this->spent_amount / $this->limit_amount) * 100;
    }

    // Check if budget is exceeded
    public function getIsExceededAttribute()
    {
        return $this->spent_amount > $this->limit_amount;
    }

    // Check if budget is close to limit (80%+ used)
    public function getIsNearLimitAttribute()
    {
        return $this->utilization_percentage >= 80;
    }

    // Scope for active budgets
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for current period budgets
    public function scopeCurrentPeriod($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    // Scope by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Update spent amount based on expenses
    public function updateSpentAmount()
    {
        $expenses = Expense::where('user_id', $this->user_id)
                          ->where('category', $this->category)
                          ->whereBetween('date', [$this->start_date, $this->end_date])
                          ->sum('amount');
        
        $this->update(['spent_amount' => $expenses]);
    }
}
