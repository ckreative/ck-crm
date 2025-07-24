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
            // Check if the UUID migration has run by looking for uuid column type
            $columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'id'");
            $isUuid = !empty($columns) && in_array($columns[0]->data_type, ['uuid', 'character varying', 'varchar', 'text']);
            
            $userData = [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Only add UUID if column is UUID type
            if ($isUuid) {
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