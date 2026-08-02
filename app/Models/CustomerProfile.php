<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'street_address',
        'barangay',
        'delivery_notes',
        'preferred_delivery_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}