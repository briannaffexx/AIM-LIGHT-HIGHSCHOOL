<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingAttendance extends Model
{
    use HasFactory;

    protected $table = 'boarding_attendance';

    // Roll call type constants
    const ROLL_CALL_MORNING = 'morning';
    const ROLL_CALL_EVENING = 'evening';

    // Attendance status constants
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT  = 'absent';
    const STATUS_EXCUSED = 'excused';

    protected $fillable = [
        'student_id',
        'date',
        'roll_call_type',
        'status',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope a query to only include attendance for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope a query to only include attendance for a specific roll call type.
     */
    public function scopeForRollCallType($query, $rollCallType)
    {
        return $query->where('roll_call_type', $rollCallType);
    }

    /**
     * Scope a query to only include present students.
     */
    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }
}