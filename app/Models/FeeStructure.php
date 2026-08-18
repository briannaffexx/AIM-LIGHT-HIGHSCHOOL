<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    // Classification constants
    const CLASSIFICATION_DAY = 'day';
    const CLASSIFICATION_BOARDING = 'boarding';

    protected $fillable = [
        'classification',
        'fee_category_id',
        'term_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Scope a query to only include fee structures for a given classification.
     */
    public function scopeForClassification($query, $classification)
    {
        return $query->where('classification', $classification);
    }

    /**
     * Scope a query to only include fee structures for a given term.
     */
    public function scopeForTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * Scope a query to only include fee structures for a given fee category.
     */
    public function scopeForCategory($query, $feeCategoryId)
    {
        return $query->where('fee_category_id', $feeCategoryId);
    }
}
