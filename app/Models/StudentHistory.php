<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'action',
        'details',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope a query to only include history records for a given student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to order history records by most recent first.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('recorded_at', 'desc');
    }

    /**
     * Scope a query to search history records by action or details.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('action', 'like', '%' . $search . '%')
                     ->orWhere('details', 'like', '%' . $search . '%');
    }
}
