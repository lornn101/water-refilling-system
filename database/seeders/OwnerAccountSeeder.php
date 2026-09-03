<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OwnerAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Create owner account if it doesn't exist
        User::firstOrCreate(
            ['email' => 'owner@owner.com'],
            [
                'name' => 'System Owner',
                'contact_no' => '09123456789',
                'role' => 'cashier',
                'status' => 'approved', // ✅ Approved by default
                'password' => Hash::make('owner123'),
            ]
        );

        $this->command->info('Owner account created: owner@owner.com / password: owner123');
    }
}