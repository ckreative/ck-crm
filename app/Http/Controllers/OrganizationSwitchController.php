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
        // Check if user has access to this organization
        if (!auth()->user()->canAccessOrganization($organization)) {
            abort(403, 'You do not have access to this organization.');
        }

        // Update user's current organization
        auth()->user()->setCurrentOrganization($organization);

        // Update session
        session(['current_organization_id' => $organization->id]);

        // Get the current route name without organization parameter
        $currentRoute = $request->route()->getName();
        $intendedUrl = session('url.intended');
        
        // If we have an intended URL, check if it needs organization slug
        if ($intendedUrl) {
            // Parse the URL and inject the organization slug if needed
            $path = parse_url($intendedUrl, PHP_URL_PATH);
            
            // Check if path doesn't already have an organization slug
            if ($path && !preg_match('/^\/[^\/]+\/(dashboard|app-settings)/', $path)) {
                // For organization-scoped routes, prepend the slug
                if (preg_match('/^\/(dashboard|app-settings)/', $path)) {
                    $intendedUrl = url('/' . $organization->slug . $path);
                }
            }
            
            return redirect($intendedUrl);
        }
        
        // Default to organization dashboard
        return redirect()->route('dashboard', ['organization' => $organization->slug]);
    }
}