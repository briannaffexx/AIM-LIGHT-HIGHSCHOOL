<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    /**
     * Scope a query to only include active academic years.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
