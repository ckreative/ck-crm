<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = auth()->user()) {
            // Set current organization from session or user default
            $currentOrgId = session('current_organization_id', $user->current_organization_id);
            
            if ($currentOrgId) {
                // Verify user has access to this organization
                if ($user->canAccessOrganization(Organization::find($currentOrgId))) {
                    // Set the current organization in the app container for global access
                    app()->instance('current_organization', Organization::find($currentOrgId));
                    
                    // Update session if different from stored value
                    if (session('current_organization_id') !== $currentOrgId) {
                        session(['current_organization_id' => $currentOrgId]);
                    }
                } else {
                    // User doesn't have access to this organization, clear it
                    session()->forget('current_organization_id');
                    // Don't auto-select - let EnsureHasSelectedOrganization handle redirection
                }
            } else {
                // No current organization set
                // Don't auto-select - let EnsureHasSelectedOrganization handle redirection
            }
        }

        return $next($request);
    }
}