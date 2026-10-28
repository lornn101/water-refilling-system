<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'unit_price',
        'delivery_fee',
        'total_amount',
        'delivery_address',
        'delivery_notes',
        'contact_number',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'delivery_date',
        'assigned_at',
        'delivered_at',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'is_walk_in' => 'boolean',
        'unit_price' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivery_date' => 'datetime',
        'assigned_at' => 'datetime',
        'delivered_at' => 'datetime',
        'paid_at' => 'datetime',
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

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at', 'desc');
    }

    // ============================================================
    // STATUS HELPERS
    // ============================================================
    public function isPending()    { return $this->status === 'pending'; }
    public function isAssigned()   { return $this->status === 'assigned'; }
    public function isOnDelivery() { return $this->status === 'on_delivery'; }
    public function isDelivered()  { return $this->status === 'delivered'; }
    public function isCancelled()  { return $this->status === 'cancelled'; }
    public function isCompleted()  { return $this->status === 'completed'; }

    public function isPaid()       { return $this->payment_status === 'paid'; }
    public function isUnpaid()     { return $this->payment_status === 'unpaid'; }
    public function isRefunded()   { return $this->payment_status === 'refunded'; }

    // ============================================================
    // BUSINESS LOGIC
    // ============================================================
    public function canBeModified()
    {
        return !$this->is_walk_in && $this->status === 'pending';
    }

    public function wasModified()
    {
        return $this->updated_at && $this->updated_at->gt($this->created_at->addSecond());
    }

    public function isWalkInRefill()
    {
        return $this->is_walk_in && $this->status === 'completed';
    }

    public function recalculateTotal()
    {
        $this->total_amount = ($this->quantity * $this->unit_price) + $this->delivery_fee;
        return $this;
    }

    /**
     * Log a status change for this order.
     */
    public function logStatusChange($oldStatus, $newStatus, $changedBy = null, $notes = null)
    {
        return OrderStatusLog::create([
            'order_id' => $this->id,
            'status' => $newStatus,
            'previous_status' => $oldStatus,
            'changed_by' => $changedBy ?? Auth::id(),
            'notes' => $notes,
        ]);
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

    public function getPaymentStatusLabel()
    {
        return match ($this->payment_status) {
            'paid' => 'Paid',
            'unpaid' => 'Unpaid',
            'refunded' => 'Refunded',
            default => 'Unknown',
        };
    }

    public function getPaymentStatusBadgeColor()
    {
        return match ($this->payment_status) {
            'paid' => 'bg-green-100 text-green-800',
            'unpaid' => 'bg-orange-100 text-orange-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentMethodLabel()
    {
        return match ($this->payment_method) {
            'cash' => '💵 Cash',
            'gcash' => '📱 GCash',
            'bank_transfer' => '🏦 Bank Transfer',
            'none' => '—',
            default => '—',
        };
    }

    /**
     * Get the ordered progression steps for the timeline.
     */
    public function getTimelineSteps()
    {
        if ($this->status === 'cancelled') {
            return ['pending', 'cancelled'];
        }
        if ($this->is_walk_in && $this->status === 'completed') {
            return ['completed'];
        }

        return ['pending', 'assigned', 'on_delivery', 'delivered'];
    }

    /**
     * Get the current step index (0-based) in the timeline.
     */
    public function getCurrentStepIndex()
    {
        $steps = $this->getTimelineSteps();
        $index = array_search($this->status, $steps);
        return $index === false ? 0 : $index;
    }
}