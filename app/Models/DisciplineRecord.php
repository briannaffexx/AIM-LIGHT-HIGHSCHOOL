<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplineRecord extends Model
{
    protected $fillable = [
        'student_id', 'incident_type', 'details',
        'action_taken', 'warnings_issued', 'recorded_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder()
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }
}
