<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_id',
        'category',
        'budgeted_amount',
        'actual_spent',
    ];

    protected $casts = [
        'budgeted_amount' => 'decimal:2',
        'actual_spent' => 'decimal:2',
    ];

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Scope a query to only include budgets for a given term.
     */
    public function scopeForTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * Get the remaining budget balance.
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->budgeted_amount - $this->actual_spent;
    }
}