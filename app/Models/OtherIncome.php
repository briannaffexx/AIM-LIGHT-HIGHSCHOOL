<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherIncome extends Model
{
    use HasFactory;

    protected $table = 'other_income';

    protected $fillable = [
        'term_id',
        'category',
        'amount',
        'date',
        'source',
        'description',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date',
    ];

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope a query to only include other income for a given term.
     */
    public function scopeForTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * Scope a query to only include other income for a given category.
     */
    public function scopeForCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include other income between two dates.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}