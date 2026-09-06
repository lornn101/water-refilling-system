<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OwnerAccountSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Create Owner Account
        User::firstOrCreate(
            ['email' => 'owner@owner.com'],
            [
                'name' => 'System Owner',
                'contact_no' => '09123456789',
                'role' => 'owner',      // ✅ Now 'owner' role
                'status' => 'approved',
                'password' => Hash::make('owner123'),
            ]
        );

        // ✅ Create Cashier Account
        User::firstOrCreate(
            ['email' => 'cashier@cashier.com'],
            [
                'name' => 'Cashier User',
                'contact_no' => '09123456780',
                'role' => 'cashier',    // ✅ 'cashier' role
                'status' => 'approved',
                'password' => Hash::make('cashier123'),
            ]
        );

        $this->command->info('✅ Owner account created: owner@owner.com / password: owner123');
        $this->command->info('✅ Cashier account created: cashier@cashier.com / password: cashier123');
    }
}