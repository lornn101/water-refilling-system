<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->default(0)->after('quantity');
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('unit_price');
            $table->decimal('total_amount', 10, 2)->default(0)->after('delivery_fee');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid')->after('status');
            $table->enum('payment_method', ['none', 'cash', 'gcash', 'bank_transfer'])->default('none')->after('payment_status');
            $table->string('payment_reference')->nullable()->after('payment_method');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('delivered_at');
            $table->foreignId('paid_by')->nullable()->after('paid_at')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['paid_by']);
            $table->dropColumn([
                'unit_price', 'delivery_fee', 'total_amount',
                'payment_status', 'payment_method', 'payment_reference',
                'paid_at', 'paid_by',
            ]);
        });
    }
};