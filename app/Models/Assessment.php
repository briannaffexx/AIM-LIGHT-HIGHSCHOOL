<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_subject_id',
        'term_id',
        'name',
        'max_marks',
        'weight',
    ];

    protected $casts = [
        'max_marks' => 'integer',
        'weight' => 'decimal:2',
    ];

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function results()
    {
        return $this->hasMany(StudentResult::class);
    }

    /**
     * Scope a query to only include assessments for a given term.
     */
    public function scopeForTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }
}
