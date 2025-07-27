<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add temporary UUID column
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Generate UUIDs for existing records
        DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['uuid' => Str::uuid()->toString()]);
            }
        });

        // Drop foreign key constraints that reference users.id
        // Note: sessions.user_id doesn't have a foreign key constraint in the default Laravel setup
        
        // Check if user_invitations table has the foreign key before dropping
        if (Schema::hasTable('user_invitations')) {
            $constraintExists = DB::select("
                SELECT 1 
                FROM information_schema.table_constraints 
                WHERE constraint_type = 'FOREIGN KEY' 
                AND table_name = 'user_invitations' 
                AND constraint_name = 'user_invitations_invited_by_foreign'
            ");
            
            if ($constraintExists) {
                Schema::table('user_invitations', function (Blueprint $table) {
                    $table->dropForeign(['invited_by']);
                });
            }
        }

        // Add new UUID columns for foreign keys
        Schema::table('sessions', function (Blueprint $table) {
            $table->uuid('user_uuid')->nullable()->after('user_id');
        });

        Schema::table('user_invitations', function (Blueprint $table) {
            $table->uuid('invited_by_uuid')->nullable()->after('invited_by');
        });

        // Populate the new foreign key columns (PostgreSQL syntax)
        DB::statement('UPDATE sessions SET user_uuid = (SELECT uuid FROM users WHERE users.id = sessions.user_id) WHERE user_id IS NOT NULL');
        
        if (Schema::hasTable('user_invitations')) {
            DB::statement('UPDATE user_invitations SET invited_by_uuid = (SELECT uuid FROM users WHERE users.id = user_invitations.invited_by)');
        }

        // Drop old columns and rename new ones
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
            $table->renameColumn('uuid', 'id');
            $table->primary('id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->renameColumn('user_uuid', 'user_id');
            // Laravel sessions table doesn't typically have foreign key constraints
        });

        Schema::table('user_invitations', function (Blueprint $table) {
            $table->dropColumn('invited_by');
            $table->renameColumn('invited_by_uuid', 'invited_by');
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a complex migration that should not be reversed in production
        // If needed, create a separate migration to convert back to integers
        throw new Exception('This migration cannot be reversed. Create a new migration to convert back to integer IDs if needed.');
    }
};
