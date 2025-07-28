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
        // Check if we have any existing data
        $hasUsers = DB::table('users')->exists();
        
        if ($hasUsers) {
            // Create a default organization for existing data
            $orgId = (string) Str::uuid();
            $now = now();
            
            DB::table('organizations')->insert([
                'id' => $orgId,
                'name' => 'Default Organization',
                'slug' => 'default-organization',
                'settings' => json_encode([]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
            // Convert existing 'admin' users to 'super_admin'
            DB::table('users')
                ->where('role', 'admin')
                ->update(['role' => 'super_admin']);
            
            // Get all non-super-admin users
            $users = DB::table('users')->where('role', '!=', 'super_admin')->get();
            
            foreach ($users as $user) {
                // Add non-super-admin users to the default organization
                $role = 'org_member';
                
                DB::table('organization_user')->insert([
                    'organization_id' => $orgId,
                    'user_id' => $user->id,
                    'role' => $role,
                    'joined_at' => $user->created_at ?? $now,
                ]);
                
                // Set current organization for non-super-admin users
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['current_organization_id' => $orgId]);
            }
            
            // Super admins should never have a current organization
            DB::table('users')
                ->where('role', 'super_admin')
                ->update(['current_organization_id' => null]);
            
            // Update all existing leads to belong to the default organization
            DB::table('leads')->update(['organization_id' => $orgId]);
            
            // Update all existing user invitations to belong to the default organization
            DB::table('user_invitations')->update([
                'organization_id' => $orgId,
                'organization_role' => 'org_member'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove users from organizations
        DB::table('organization_user')->truncate();
        
        // Clear current_organization_id from users
        DB::table('users')->update(['current_organization_id' => null]);
        
        // Convert super_admin back to admin
        DB::table('users')
            ->where('role', 'super_admin')
            ->update(['role' => 'admin']);
        
        // Clear organization_id from leads
        DB::table('leads')->update(['organization_id' => null]);
        
        // Clear organization fields from user_invitations
        DB::table('user_invitations')->update([
            'organization_id' => null,
            'organization_role' => 'org_member'
        ]);
        
        // Delete the default organization
        DB::table('organizations')
            ->where('slug', 'default-organization')
            ->delete();
    }
};