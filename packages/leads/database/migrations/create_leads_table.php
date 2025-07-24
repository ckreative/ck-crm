<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = config('leads.database.table_name', 'leads');
        
        Schema::create($tableName, function (Blueprint $table) {
            if (config('leads.database.use_uuid', true)) {
                $table->uuid('id')->primary();
            } else {
                $table->id();
            }
            
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('notes')->nullable();
            $table->string('calcom_event_id')->unique()->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            
            $table->index('email');
            $table->index('archived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('leads.database.table_name', 'leads'));
    }
};