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
        Schema::table('leads', function (Blueprint $table) {
            $table->timestamp('appointment_date')->nullable()->after('calcom_event_id');
            $table->string('appointment_status', 50)->nullable()->after('appointment_date');
            
            // Add index for querying upcoming appointments
            $table->index(['appointment_date', 'appointment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['appointment_date', 'appointment_status']);
            $table->dropColumn(['appointment_date', 'appointment_status']);
        });
    }
};
