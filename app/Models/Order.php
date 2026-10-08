<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'rider_id',
        'is_walk_in',
        'walk_in_customer_name',
        'walk_in_contact',
        'quantity',
        'delivery_address',
        'delivery_notes',
        'contact_number',
        'status',
        'delivery_date',
        'assigned_at',
        'delivered_at',
    ];

    protected $casts = [
        'is_walk_in' => 'boolean',
        'delivery_date' => 'datetime',
        'assigned_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    // ============================================================
    // STATUS HELPERS
    // ============================================================
    public function isPending()    { return $this->status === 'pending'; }
    public function isAssigned()   { return $this->status === 'assigned'; }
    public function isOnDelivery() { return $this->status === 'on_delivery'; }
    public function isDelivered()  { return $this->status === 'delivered'; }
    public function isCancelled()  { return $this->status === 'cancelled'; }
    public function isCompleted()  { return $this->status === 'completed'; } // ✅ NEW

    // ============================================================
    // BUSINESS LOGIC
    // ============================================================

    /**
     * Can a customer modify this order?
     * Only pending, non-walk-in orders can be modified by the customer.
     */
    public function canBeModified()
    {
        return !$this->is_walk_in && $this->status === 'pending';
    }

    /**
     * Was this order modified after creation?
     */
    public function wasModified()
    {
        return $this->updated_at && $this->updated_at->gt($this->created_at->addSecond());
    }

    /**
     * Is this order a walk-in refill (immediately completed at station)?
     */
    public function isWalkInRefill()
    {
        return $this->is_walk_in && $this->status === 'completed';
    }

    // ============================================================
    // DISPLAY HELPERS
    // ============================================================
    public function getCustomerName()
    {
        if ($this->is_walk_in) {
            return $this->walk_in_customer_name ?? 'Walk-in Customer';
        }
        return $this->customer->name ?? 'N/A';
    }

    public function getCustomerContact()
    {
        if ($this->is_walk_in) {
            return $this->walk_in_contact ?? $this->contact_number ?? 'N/A';
        }
        return $this->customer->contact_no ?? $this->contact_number;
    }

    /**
     * Human-readable status label
     */
    public function getStatusLabel()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'assigned' => 'Assigned',
            'on_delivery' => 'On Delivery',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeColor()
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'assigned' => 'bg-blue-100 text-blue-800',
            'on_delivery' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'completed' => 'bg-emerald-100 text-emerald-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusIcon()
    {
        return match ($this->status) {
            'pending' => '⏳',
            'assigned' => '📋',
            'on_delivery' => '🚚',
            'delivered' => '✅',
            'completed' => '🏪',
            'cancelled' => '❌',
            default => '📦',
        };
    }
}