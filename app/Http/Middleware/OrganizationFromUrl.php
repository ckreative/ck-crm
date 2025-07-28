<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationFromUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $organizationSlug = $request->route('organization');
        
        if ($organizationSlug) {
            $organization = Organization::where('slug', $organizationSlug)->first();
            
            if (!$organization) {
                abort(404, 'Organization not found.');
            }
            
            // Check if user has access to this organization
            if (!auth()->user()->canAccessOrganization($organization)) {
                abort(403, 'You do not have access to this organization.');
            }
            
            // Set the current organization in the app container for global access
            app()->instance('current_organization', $organization);
            
            // Update session with current organization
            session(['current_organization_id' => $organization->id]);
            
            // Update user's current organization if different
            if (auth()->user()->current_organization_id !== $organization->id) {
                auth()->user()->setCurrentOrganization($organization);
            }
        }

        return $next($request);
    }
}