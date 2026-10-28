<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role',
        'contact_no',
        'status',
        'deleted_at',
    ];

    /**
     * Computed "full_name" accessor — combines first, middle, last.
     * Usage: $user->full_name
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(implode(' ', array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
            ], fn ($v) => !is_null($v) && $v !== '')))
        );
    }

    public function isApproved() { return $this->status === 'approved'; }
    public function isPending()  { return $this->status === 'pending'; }
    public function isRejected() { return $this->status === 'rejected'; }

    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function riderProfile()
    {
        return $this->hasOne(RiderProfile::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'rider_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}