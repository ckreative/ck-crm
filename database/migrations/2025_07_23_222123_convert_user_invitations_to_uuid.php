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
        // Note: This migration depends on convert_users_to_uuid being run first
        // since it updates the invited_by foreign key

        // Add temporary UUID column
        Schema::table('user_invitations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Generate UUIDs for existing records
        DB::table('user_invitations')->orderBy('id')->chunk(100, function ($invitations) {
            foreach ($invitations as $invitation) {
                DB::table('user_invitations')
                    ->where('id', $invitation->id)
                    ->update(['uuid' => Str::uuid()->toString()]);
            }
        });

        // Drop old column and rename new one
        Schema::table('user_invitations', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
            $table->renameColumn('uuid', 'id');
            $table->primary('id');
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
