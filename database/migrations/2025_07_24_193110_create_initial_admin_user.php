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
            // Check if id column is uuid or bigint
            $idColumnType = DB::connection()->getDoctrineColumn('users', 'id')->getType()->getName();
            
            $userData = [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Only add UUID if column type is UUID/string
            if (in_array($idColumnType, ['uuid', 'string', 'guid'])) {
                $userData['id'] = Str::uuid()->toString();
            }
            
            DB::table('users')->insert($userData);
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