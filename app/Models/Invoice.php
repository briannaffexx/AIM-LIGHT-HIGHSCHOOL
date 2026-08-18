<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    // Invoice status constants
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PARTIALLY_PAID = 'partially_paid';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'student_id',
        'term_id',
        'description',
        'amount_due',
        'status',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the total amount paid for this invoice.
     */
    public function getAmountPaidAttribute(): float
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Get the outstanding balance for this invoice.
     */
    public function getOutstandingBalanceAttribute(): float
    {
        return $this->amount_due - $this->amount_paid;
    }

    /**
     * Scope a query to only include invoices for a given student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include invoices for a given term.
     */
    public function scopeForTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * Scope a query to only include unpaid invoices.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    /**
     * Scope a query to only include partially paid invoices.
     */
    public function scopePartiallyPaid($query)
    {
        return $query->where('status', self::STATUS_PARTIALLY_PAID);
    }

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE);
    }
}
