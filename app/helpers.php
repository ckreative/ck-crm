<?php

use App\Models\Organization;

if (!function_exists('current_organization')) {
    /**
     * Get the current organization for the authenticated user.
     *
     * @return Organization|null
     */
    function current_organization(): ?Organization
    {
        if (!auth()->check()) {
            return null;
        }

        // Try to get from app container first (set by middleware)
        if (app()->has('current_organization')) {
            return app('current_organization');
        }

        // Otherwise, get from user's current organization
        return auth()->user()->currentOrganization;
    }
}

if (!function_exists('user_organizations')) {
    /**
     * Get all organizations for the authenticated user.
     *
     * @return \Illuminate\Support\Collection
     */
    function user_organizations()
    {
        if (!auth()->check()) {
            return collect();
        }

        return auth()->user()->organizations()
            ->orderBy('name')
            ->get();
    }
}