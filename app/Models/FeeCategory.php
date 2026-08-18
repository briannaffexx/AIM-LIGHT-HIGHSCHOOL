<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function structures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    /**
     * Scope a query to search fee categories by name or code.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
                     ->orWhere('code', 'like', '%' . $search . '%');
    }
}
