<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    /**
     * Scope a query to search subjects by name or code.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('code', 'like', '%' . $search . '%');
    }
}