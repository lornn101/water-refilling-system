<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed default accounts + settings
        $this->call([
            OwnerAccountSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}