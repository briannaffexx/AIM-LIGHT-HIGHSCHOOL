<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'name', 'category', 'total_quantity',
        'assigned_quantity', 'status', 'condition_notes',
    ];

    public function getAvailableAttribute(): int
    {
        return $this->total_quantity - $this->assigned_quantity;
    }
}
