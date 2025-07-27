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
        Schema::table('leads', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Generate UUIDs for existing records
        DB::table('leads')->orderBy('id')->chunk(100, function ($leads) {
            foreach ($leads as $lead) {
                DB::table('leads')
                    ->where('id', $lead->id)
                    ->update(['uuid' => Str::uuid()->toString()]);
            }
        });

        // No foreign keys reference leads.id currently, so we can proceed directly
        // Drop old column and rename new one
        Schema::table('leads', function (Blueprint $table) {
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
