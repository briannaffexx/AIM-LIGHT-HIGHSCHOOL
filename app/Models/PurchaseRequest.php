<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ORDERED = 'ordered';

    protected $fillable = [
        'requested_by',
        'item_name',
        'quantity',
        'estimated_cost',
        'status',
        'approved_by',
        'request_date',
        'approval_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'estimated_cost' => 'decimal:2',
        'request_date' => 'date',
        'approval_date' => 'date',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    /**
     * Scope a query to only include purchase requests with a given status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include purchase requests made by a specific user.
     */
    public function scopeForRequester($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    /**
     * Scope a query to search purchase requests by item name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('item_name', 'like', '%' . $search . '%');
    }
}
