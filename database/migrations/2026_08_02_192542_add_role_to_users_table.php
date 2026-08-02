<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'rider', 'cashier'])->default('customer')->after('password');
            $table->string('contact_no')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('contact_no');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'contact_no', 'is_active']);
        });
    }
};