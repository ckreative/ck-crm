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
        // Get user's organizations
        DB::unprepared('
            CREATE OR REPLACE FUNCTION get_user_organizations(p_user_id UUID)
            RETURNS TABLE(
                organization_id UUID,
                name VARCHAR,
                slug VARCHAR,
                role VARCHAR,
                joined_at TIMESTAMP
            )
            AS $$
            BEGIN
                RETURN QUERY
                SELECT 
                    o.id as organization_id,
                    o.name,
                    o.slug,
                    ou.role,
                    ou.joined_at
                FROM organizations o
                JOIN organization_user ou ON o.id = ou.organization_id
                WHERE ou.user_id = p_user_id
                ORDER BY o.name;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Create organization
        DB::unprepared('
            CREATE OR REPLACE FUNCTION create_organization(
                p_name VARCHAR,
                p_slug VARCHAR,
                p_owner_id UUID
            )
            RETURNS UUID
            AS $$
            DECLARE
                v_org_id UUID;
            BEGIN
                -- Insert organization
                INSERT INTO organizations (id, name, slug, created_at, updated_at)
                VALUES (gen_random_uuid(), p_name, p_slug, NOW(), NOW())
                RETURNING id INTO v_org_id;
                
                -- Add owner to organization
                INSERT INTO organization_user (organization_id, user_id, role, joined_at)
                VALUES (v_org_id, p_owner_id, \'org_owner\', NOW());
                
                RETURN v_org_id;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Add user to organization
        DB::unprepared('
            CREATE OR REPLACE FUNCTION add_user_to_organization(
                p_user_id UUID,
                p_org_id UUID,
                p_role VARCHAR
            )
            RETURNS VOID
            AS $$
            BEGIN
                INSERT INTO organization_user (user_id, organization_id, role, joined_at)
                VALUES (p_user_id, p_org_id, p_role, NOW())
                ON CONFLICT (user_id, organization_id) 
                DO UPDATE SET role = p_role;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Get organization members
        DB::unprepared('
            CREATE OR REPLACE FUNCTION get_organization_members(p_org_id UUID)
            RETURNS TABLE(
                user_id UUID,
                name VARCHAR,
                email VARCHAR,
                role VARCHAR,
                joined_at TIMESTAMP
            )
            AS $$
            BEGIN
                RETURN QUERY
                SELECT 
                    u.id as user_id,
                    u.name,
                    u.email,
                    ou.role,
                    ou.joined_at
                FROM users u
                JOIN organization_user ou ON u.id = ou.user_id
                WHERE ou.organization_id = p_org_id
                ORDER BY u.name;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Update user organization role
        DB::unprepared('
            CREATE OR REPLACE FUNCTION update_user_organization_role(
                p_user_id UUID,
                p_org_id UUID,
                p_new_role VARCHAR
            )
            RETURNS VOID
            AS $$
            BEGIN
                UPDATE organization_user
                SET role = p_new_role
                WHERE user_id = p_user_id AND organization_id = p_org_id;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Remove user from organization
        DB::unprepared('
            CREATE OR REPLACE FUNCTION remove_user_from_organization(
                p_user_id UUID,
                p_org_id UUID
            )
            RETURNS VOID
            AS $$
            BEGIN
                DELETE FROM organization_user
                WHERE user_id = p_user_id AND organization_id = p_org_id;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Get organization by slug
        DB::unprepared('
            CREATE OR REPLACE FUNCTION get_organization_by_slug(p_slug VARCHAR)
            RETURNS TABLE(
                id UUID,
                name VARCHAR,
                slug VARCHAR,
                settings JSONB,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )
            AS $$
            BEGIN
                RETURN QUERY
                SELECT 
                    o.id,
                    o.name,
                    o.slug,
                    o.settings,
                    o.created_at,
                    o.updated_at
                FROM organizations o
                WHERE o.slug = p_slug
                LIMIT 1;
            END;
            $$ LANGUAGE plpgsql;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS get_user_organizations(UUID)');
        DB::unprepared('DROP FUNCTION IF EXISTS create_organization(VARCHAR, VARCHAR, UUID)');
        DB::unprepared('DROP FUNCTION IF EXISTS add_user_to_organization(UUID, UUID, VARCHAR)');
        DB::unprepared('DROP FUNCTION IF EXISTS get_organization_members(UUID)');
        DB::unprepared('DROP FUNCTION IF EXISTS update_user_organization_role(UUID, UUID, VARCHAR)');
        DB::unprepared('DROP FUNCTION IF EXISTS remove_user_from_organization(UUID, UUID)');
        DB::unprepared('DROP FUNCTION IF EXISTS get_organization_by_slug(VARCHAR)');
    }
};