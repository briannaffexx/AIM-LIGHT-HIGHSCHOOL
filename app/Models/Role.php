<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // No users() relationship – Spatie handles it via HasRoles trait.

    /**
     * Scope a query to find a role by its slug.
     */
    public function scopeFindBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope a query to search roles by name or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%');
    }
}