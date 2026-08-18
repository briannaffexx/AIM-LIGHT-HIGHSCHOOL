<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dormitory extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_id',
        'name',
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Scope a query to only include dormitories for a given house.
     */
    public function scopeForHouse($query, $houseId)
    {
        return $query->where('house_id', $houseId);
    }

    /**
     * Scope a query to search dormitories by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }
}
