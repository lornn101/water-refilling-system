<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'plate_number',
        'valid_id_path',
        'availability_status',
        'current_lat_long',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}