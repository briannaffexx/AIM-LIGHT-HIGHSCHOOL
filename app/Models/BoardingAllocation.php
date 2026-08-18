<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'bed_id',
        'allocated_at',
        'vacated_at',
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'vacated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * Scope a query to only include active allocations (not vacated).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('vacated_at');
    }

    /**
     * Check if this allocation is currently active.
     */
    public function isActive(): bool
    {
        return is_null($this->vacated_at);
    }
}
