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
        'delivery_date' => 'datetime',
        'assigned_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relationship: Customer who placed the order
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Relationship: Rider assigned to the order
    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    // Helper methods for status
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAssigned()
    {
        return $this->status === 'assigned';
    }

    public function isOnDelivery()
    {
        return $this->status === 'on_delivery';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    // Get status badge color
    public function getStatusBadgeColor()
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'assigned' => 'bg-blue-100 text-blue-800',
            'on_delivery' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Get status icon
    public function getStatusIcon()
    {
        return match ($this->status) {
            'pending' => '⏳',
            'assigned' => '📋',
            'on_delivery' => '🚚',
            'delivered' => '✅',
            'cancelled' => '❌',
            default => '📦',
        };
    }
}