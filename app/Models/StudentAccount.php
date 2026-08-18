<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'balance',
        'total_invoiced',
        'total_paid',
    ];

    protected $casts = [
        'balance'        => 'decimal:2',
        'total_invoiced' => 'decimal:2',
        'total_paid'     => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope a query to only include the account for a given student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}