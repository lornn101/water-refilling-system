<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_type'); // e.g., Motorcycle, Tricycle
            $table->string('plate_number')->nullable();
            $table->string('valid_id_path')->nullable(); // For school project verification
            $table->enum('availability_status', ['available', 'on_delivery', 'offline'])->default('available');
            $table->string('current_lat_long')->nullable(); // Optional future tracking
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_profiles');
    }
};