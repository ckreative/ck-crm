<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only create admin user if no users exist
        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert([
                'id' => Str::uuid()->toString(),
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the initial admin user
        DB::table('users')
            ->where('email', 'admin@example.com')
            ->where('name', 'Admin User')
            ->delete();
    }
};