<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationSwitchController extends Controller
{
    /**
     * Switch to a different organization.
     */
    public function switch(Request $request, Organization $organization)
    {
        $user = auth()->user();
        
        // Check if user has access to this organization
        if (!$user->canAccessOrganization($organization)) {
            abort(403, 'You do not have access to this organization.');
        }

        // Super admins work in session context only
        if ($user->isSuperAdmin()) {
            // Just set the session, don't update the user record
            session(['current_organization_id' => $organization->id]);
        } else {
            // Regular users: update their current organization
            $user->setCurrentOrganization($organization);
            // Update session
            session(['current_organization_id' => $organization->id]);
        }

        // Get the current route name without organization parameter
        $currentRoute = $request->route()->getName();
        $intendedUrl = session('url.intended');
        
        // If we have an intended URL, check if it needs organization slug
        if ($intendedUrl) {
            // Parse the URL and inject the organization slug if needed
            $path = parse_url($intendedUrl, PHP_URL_PATH);
            
            // Check if path doesn't already have an organization slug
            if ($path && !preg_match('/^\/[^\/]+\/(dashboard|app-settings|leads)/', $path)) {
                // For organization-scoped routes, prepend the slug
                if (preg_match('/^\/(dashboard|app-settings|leads)/', $path)) {
                    $intendedUrl = url('/' . $organization->slug . $path);
                }
            }
            
            return redirect($intendedUrl);
        }
        
        // Default to organization dashboard
        return redirect()->route('dashboard', ['organization' => $organization->slug]);
    }
}