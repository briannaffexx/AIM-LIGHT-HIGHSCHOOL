<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'dormitory_id',
        'name',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Get the number of beds currently occupied in this room.
     */
    public function getOccupiedBedsCountAttribute(): int
    {
        return $this->beds()->where('status', Bed::STATUS_OCCUPIED)->count();
    }

    /**
     * Scope a query to only include rooms for a given dormitory.
     */
    public function scopeForDormitory($query, $dormitoryId)
    {
        return $query->where('dormitory_id', $dormitoryId);
    }

    /**
     * Scope a query to search rooms by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }
}