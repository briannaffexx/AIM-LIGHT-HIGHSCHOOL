<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    // Employment status constants
    const STATUS_FULL_TIME  = 'full_time';
    const STATUS_PART_TIME  = 'part_time';
    const STATUS_CONTRACT   = 'contract';
    const STATUS_PROBATION  = 'probation';
    const STATUS_TERMINATED = 'terminated';
    const STATUS_RETIRED    = 'retired';

    protected $fillable = [
        'user_id',
        'staff_number',
        'position_id',
        'department_id',
        'employment_status',
        'attendance_status',
        'responsibilities',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    /**
     * Scope a query to only include staff in a given department.
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to only include staff with a given position.
     */
    public function scopeForPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    /**
     * Scope a query to only include staff with a specific employment status.
     */
    public function scopeWithEmploymentStatus($query, $status)
    {
        return $query->where('employment_status', $status);
    }

    /**
     * Scope a query to search staff by staff number or responsibilities.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('staff_number', 'like', '%' . $search . '%')
            ->orWhere('responsibilities', 'like', '%' . $search . '%');
    }
}