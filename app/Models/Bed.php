<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';

    protected $fillable = [
        'room_id',
        'bed_number',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function allocations()
    {
        return $this->hasMany(BoardingAllocation::class);
    }

    /**
     * Scope a query to only include available beds.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }
}
