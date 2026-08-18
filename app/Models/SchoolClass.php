<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'level',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'class_id');
    }

    /**
     * Scope a query to search classes by name or level.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('level', 'like', '%' . $search . '%');
    }

    /**
     * Scope a query to only include classes of a given level.
     */
    public function scopeForLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}