<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_id',
        'marks_obtained',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Scope a query to only include results for a given student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include results for a given assessment.
     */
    public function scopeForAssessment($query, $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }
}
