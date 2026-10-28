<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add new columns
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
        });

        // 2. Backfill data from existing `name` column
        $users = DB::table('users')->select('id', 'name')->get();
        foreach ($users as $user) {
            $raw = trim($user->name ?? '');
            $parts = array_values(array_filter(preg_split('/\s+/', $raw)));
            $count = count($parts);

            $first = $count >= 1 ? $parts[0] : '';
            $last = '';
            $middle = '';

            if ($count === 2) {
                $last = $parts[1];
            } elseif ($count >= 3) {
                $last = $parts[$count - 1];
                $middle = implode(' ', array_slice($parts, 1, $count - 2));
            }

            DB::table('users')->where('id', $user->id)->update([
                'first_name' => $first ?: null,
                'middle_name' => $middle ?: null,
                'last_name' => $last ?: null,
            ]);
        }

        // 3. Drop the old `name` column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        $users = DB::table('users')->select('id', 'first_name', 'middle_name', 'last_name')->get();
        foreach ($users as $user) {
            $parts = array_filter([$user->first_name, $user->middle_name, $user->last_name]);
            DB::table('users')->where('id', $user->id)->update([
                'name' => implode(' ', $parts),
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name']);
        });
    }
};