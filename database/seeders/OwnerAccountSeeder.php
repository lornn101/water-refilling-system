<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OwnerAccountSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ 1. Create or UPDATE the Owner Account (Force role to 'owner')
        User::updateOrCreate(
            ['email' => 'owner@owner.com'],
            [
                'name' => 'System Owner',
                'contact_no' => '09123456789',
                'role' => 'owner',          // ✅ Forces role to 'owner'
                'status' => 'approved',
                'password' => Hash::make('owner123'),
            ]
        );

        // ✅ 2. Create the Cashier Account
        User::updateOrCreate(
            ['email' => 'cashier@cashier.com'],
            [
                'name' => 'Cashier User',
                'contact_no' => '09123456780',
                'role' => 'cashier',         // ✅ Role is 'cashier'
                'status' => 'approved',
                'password' => Hash::make('cashier123'),
            ]
        );

        $this->command->info('✅ Owner account updated/created: owner@owner.com / password: owner123 (role: owner)');
        $this->command->info('✅ Cashier account created: cashier@cashier.com / password: cashier123 (role: cashier)');
    }
}