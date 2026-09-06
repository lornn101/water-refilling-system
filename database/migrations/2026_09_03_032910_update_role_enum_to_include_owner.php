<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Temporarily change the column to allow 'owner'
        DB::statement("ALTER TABLE users MODIFY role ENUM('customer', 'rider', 'cashier', 'owner') DEFAULT 'customer'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('customer', 'rider', 'cashier') DEFAULT 'customer'");
    }
};