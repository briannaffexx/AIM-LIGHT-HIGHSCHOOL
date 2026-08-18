<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // Classification constants
    const CLASSIFICATION_DAY = 'day';
    const CLASSIFICATION_BOARDING = 'boarding';

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_GRADUATED = 'graduated';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_TRANSFERRED = 'transferred';

    protected $fillable = [
        'user_id',
        'admission_number',
        'first_name',
        'last_name',
        'class_id',
        'classification',
        'status',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function histories()
    {
        return $this->hasMany(StudentHistory::class);
    }

    public function allocations()
    {
        return $this->hasMany(BoardingAllocation::class);
    }

    public function activeAllocation()
    {
        return $this->hasOne(BoardingAllocation::class)->whereNull('vacated_at');
    }

    public function attendance()
    {
        return $this->hasMany(BoardingAttendance::class);
    }

    public function movements()
    {
        return $this->hasMany(StudentMovement::class);
    }

    public function incidents()
    {
        return $this->hasMany(BoardingIncident::class);
    }

    public function results()
    {
        return $this->hasMany(StudentResult::class);
    }

    public function account()
    {
        return $this->hasOne(StudentAccount::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Scope a query to only include day scholars.
     */
    public function scopeDay($query)
    {
        return $query->where('classification', self::CLASSIFICATION_DAY);
    }

    /**
     * Scope a query to only include boarding students.
     */
    public function scopeBoarding($query)
    {
        return $query->where('classification', self::CLASSIFICATION_BOARDING);
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include students in a given class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope a query to search students by first name, last name, or admission number.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('first_name', 'like', '%' . $search . '%')
                     ->orWhere('last_name', 'like', '%' . $search . '%')
                     ->orWhere('admission_number', 'like', '%' . $search . '%');
    }
}
