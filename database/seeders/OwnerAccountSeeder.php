<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OwnerAccountSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Owner Account
        User::updateOrCreate(
            ['email' => 'owner@owner.com'],
            [
                'first_name' => 'System',
                'middle_name' => null,
                'last_name' => 'Owner',
                'contact_no' => '09123456789',
                'role' => 'owner',
                'status' => 'approved',
                'password' => Hash::make('owner123'),
            ]
        );

        // ✅ Cashier Account
        User::updateOrCreate(
            ['email' => 'cashier@cashier.com'],
            [
                'first_name' => 'Cashier',
                'middle_name' => null,
                'last_name' => 'User',
                'contact_no' => '09123456780',
                'role' => 'cashier',
                'status' => 'approved',
                'password' => Hash::make('cashier123'),
            ]
        );

        $this->command->info('✅ Owner: owner@owner.com / owner123 (role: owner)');
        $this->command->info('✅ Cashier: cashier@cashier.com / cashier123 (role: cashier)');
    }
}