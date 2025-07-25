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
            // Add index on created_at for date filtering (7days, 30days)
            // This will speed up queries that filter by date
            $table->index('created_at', 'leads_created_at_index');
            
            // Add composite index for search functionality
            // This will speed up searches across name, email, and company
            $table->index(['name', 'email', 'company'], 'leads_search_index');
            
            // Add individual indexes for commonly searched fields
            $table->index('name', 'leads_name_index');
            $table->index('company', 'leads_company_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_created_at_index');
            $table->dropIndex('leads_search_index');
            $table->dropIndex('leads_name_index');
            $table->dropIndex('leads_company_index');
        });
    }
};