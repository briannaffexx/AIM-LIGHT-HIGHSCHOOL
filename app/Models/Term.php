<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function mealSchedules()
    {
        return $this->hasMany(MealSchedule::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function otherIncomes()
    {
        return $this->hasMany(OtherIncome::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include active terms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include terms for a given academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }
}