<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Customer who placed the order
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            
            // Rider assigned to deliver (nullable until assigned)
            $table->foreignId('rider_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Order details
            $table->integer('quantity')->default(1); // number of gallons
            $table->text('delivery_address');
            $table->text('delivery_notes')->nullable();
            $table->string('contact_number');
            
            // Status: pending, assigned, on_delivery, delivered, cancelled
            $table->enum('status', ['pending', 'assigned', 'on_delivery', 'delivered', 'cancelled'])->default('pending');
            
            // Timestamps
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};