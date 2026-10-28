<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'price_per_gallon'],
            [
                'value' => '25.00',
                'description' => 'Price per gallon in PHP',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'delivery_fee'],
            [
                'value' => '10.00',
                'description' => 'Standard delivery fee in PHP',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'business_name'],
            [
                'value' => 'Poblacion Water Refilling Station',
                'description' => 'Business name for receipts',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'business_address'],
            [
                'value' => 'Poblacion, Bilar, Bohol',
                'description' => 'Business address for receipts',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'business_contact'],
            [
                'value' => '0912-345-6789',
                'description' => 'Business contact for receipts',
            ]
        );

        $this->command->info('✅ Settings seeded: price ₱25/gallon, delivery fee ₱10');
    }
}