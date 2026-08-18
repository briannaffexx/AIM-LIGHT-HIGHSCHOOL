<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingResource extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_USE = 'in_use';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_DAMAGED = 'damaged';

    protected $fillable = [
        'name',
        'category',
        'status',
        'notes',
    ];

    /**
     * Scope a query to only include available resources.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope a query to only include resources under maintenance.
     */
    public function scopeInMaintenance($query)
    {
        return $query->where('status', self::STATUS_MAINTENANCE);
    }
}
