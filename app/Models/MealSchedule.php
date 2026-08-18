<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealSchedule extends Model
{
    use HasFactory;

    // Meal type constants
    const MEAL_BREAKFAST = 'breakfast';
    const MEAL_LUNCH = 'lunch';
    const MEAL_DINNER = 'dinner';

    protected $fillable = [
        'term_id',
        'day_of_week',
        'meal_type',
        'menu_item',
        'time',
    ];

    protected $casts = [
        'time' => 'datetime:H:i',
    ];

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Scope a query to only include meal schedules for a given term.
     */
    public function scopeForTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * Scope a query to only include meal schedules for a specific day.
     */
    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope a query to only include meal schedules for a specific meal type.
     */
    public function scopeForMealType($query, $mealType)
    {
        return $query->where('meal_type', $mealType);
    }

    /**
     * Scope a query to search meal schedules by menu item.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('menu_item', 'like', '%' . $search . '%');
    }
}
