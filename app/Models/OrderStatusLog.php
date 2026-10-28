<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'previous_status',
        'changed_by',
        'notes',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // ============================================================
    // DISPLAY HELPERS
    // ============================================================
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

    public function getStatusColor()
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'assigned' => 'blue',
            'on_delivery' => 'purple',
            'delivered' => 'green',
            'completed' => 'emerald',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Human-readable description of who changed the status.
     */
    public function getChangedByLabel()
    {
        if (!$this->changedBy) {
            return 'System';
        }

        return match ($this->changedBy->role) {
            'owner' => '👑 ' . $this->changedBy->name,
            'cashier' => '💼 ' . $this->changedBy->name,
            'rider' => '🛵 ' . $this->changedBy->name,
            'customer' => '👤 ' . $this->changedBy->name,
            default => $this->changedBy->name,
        };
    }
}