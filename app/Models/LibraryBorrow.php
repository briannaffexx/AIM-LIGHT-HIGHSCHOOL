<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LibraryBorrow extends Model
{
    protected $fillable = [
        'book_id', 'student_id', 'borrowed_at',
        'due_at', 'returned_at', 'fine_amount', 'status',
    ];

    protected $casts = [
        'borrowed_at'  => 'datetime',
        'due_at'       => 'datetime',
        'returned_at'  => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return is_null($this->returned_at) && $this->due_at && $this->due_at->isPast();
    }

    public function getCalculatedFineAttribute(): float
    {
        if ($this->isOverdue) {
            $days = (int) $this->due_at->diffInDays(now());
            return max(1, $days) * 500.00; // MWK 500 per day overdue
        }
        return 0.00;
    }
}
