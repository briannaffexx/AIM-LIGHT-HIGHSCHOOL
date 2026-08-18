<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'student_id',
        'term_id',
        'fee_category_id',
        'payment_reference',
        'amount',
        'payment_date',
        'payment_method',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope a query to only include payments for a given student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include payments for a given term.
     */
    public function scopeForTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * Scope a query to only include payments for a given fee category.
     */
    public function scopeForFeeCategory($query, $feeCategoryId)
    {
        return $query->where('fee_category_id', $feeCategoryId);
    }

    /**
     * Scope a query to only include payments between two dates.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }
}