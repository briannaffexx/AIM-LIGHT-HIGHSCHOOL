<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'incident_type',
        'details',
        'follow_up_actions',
        'reported_by',
        'reported_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reporter()
    {
        return $this->belongsTo(Staff::class, 'reported_by');
    }

    /**
     * Scope a query to only include incidents for a given student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to order incidents by most recent first.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('reported_at', 'desc');
    }
}
