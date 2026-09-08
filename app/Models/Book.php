<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title', 'author', 'isbn', 'category',
        'total_copies', 'available_copies',
    ];

    public function borrows()
    {
        return $this->hasMany(LibraryBorrow::class);
    }

    public function activeBorrows()
    {
        return $this->hasMany(LibraryBorrow::class)->whereNull('returned_at');
    }
}
