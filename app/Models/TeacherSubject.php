<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'subject_id',
        'class_id',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Scope a query to only include teacher-subject assignments for a given staff member.
     */
    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    /**
     * Scope a query to only include teacher-subject assignments for a given subject.
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope a query to only include teacher-subject assignments for a given class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }
}