<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ORDERED = 'ordered';
    const STATUS_RECEIVED = 'received';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'purchase_request_id',
        'supplier_id',
        'order_number',
        'order_date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Scope a query to only include purchase orders for a given supplier.
     */
    public function scopeForSupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Scope a query to only include purchase orders with a given status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include purchase orders between two dates.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }
}
