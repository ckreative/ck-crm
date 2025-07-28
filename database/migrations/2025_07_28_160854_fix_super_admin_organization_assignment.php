<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove super admins from all organizations
        DB::table('organization_user')
            ->whereIn('user_id', function ($query) {
                $query->select('id')
                    ->from('users')
                    ->where('role', 'super_admin');
            })
            ->delete();
        
        // Clear current_organization_id for super admins
        DB::table('users')
            ->where('role', 'super_admin')
            ->update(['current_organization_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a fix migration, no need to reverse
    }
};