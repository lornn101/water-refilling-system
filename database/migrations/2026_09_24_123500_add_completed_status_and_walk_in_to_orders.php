<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add walk-in fields only if they don't exist yet
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'is_walk_in')) {
                $table->boolean('is_walk_in')->default(false)->after('rider_id');
            }
            if (!Schema::hasColumn('orders', 'walk_in_customer_name')) {
                $table->string('walk_in_customer_name')->nullable()->after('is_walk_in');
            }
            if (!Schema::hasColumn('orders', 'walk_in_contact')) {
                $table->string('walk_in_contact')->nullable()->after('walk_in_customer_name');
            }
        });

        // 2. Make customer_id nullable for walk-in orders
        DB::statement("ALTER TABLE orders MODIFY customer_id BIGINT UNSIGNED NULL");

        // 3. Add 'completed' to the status enum
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'assigned', 'on_delivery', 'delivered', 'cancelled', 'completed') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'assigned', 'on_delivery', 'delivered', 'cancelled') DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders MODIFY customer_id BIGINT UNSIGNED NOT NULL");

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'walk_in_contact')) {
                $table->dropColumn('walk_in_contact');
            }
            if (Schema::hasColumn('orders', 'walk_in_customer_name')) {
                $table->dropColumn('walk_in_customer_name');
            }
            if (Schema::hasColumn('orders', 'is_walk_in')) {
                $table->dropColumn('is_walk_in');
            }
        });
    }
};