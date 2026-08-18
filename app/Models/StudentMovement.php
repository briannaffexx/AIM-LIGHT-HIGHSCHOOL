<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMovement extends Model
{
    use HasFactory;

    // Leave type constants
    const LEAVE_REGULAR = 'regular';
    const LEAVE_EMERGENCY = 'emergency';
    const LEAVE_WEEKEND = 'weekend';

    // Movement status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DEPARTED = 'departed';
    const STATUS_RETURNED = 'returned';
    const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'student_id',
        'leave_type',
        'departure_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'departure_date' => 'datetime',
        'expected_return_date' => 'datetime',
        'actual_return_date' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function approver()
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }

    /**
     * Check if the student is overdue to return.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== self::STATUS_RETURNED
            && $this->expected_return_date
            && $this->expected_return_date->isPast();
    }

    /**
     * Scope a query to only include movements for a given student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include movements with a given status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include overdue movements.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE);
    }

    /**
     * Scope a query to search movements by leave type.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('leave_type', 'like', '%' . $search . '%');
    }
}
