<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasSelectedOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for organization selection routes
        if ($request->routeIs('organizations.select', 'organizations.index', 'organization.switch')) {
            return $next($request);
        }

        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }

        // Check if user has a current organization in session
        $currentOrgId = session('current_organization_id');
        
        if (!$currentOrgId) {
            // Store the intended URL
            session(['url.intended' => $request->fullUrl()]);
            
            // Check if user has any organizations
            $hasOrganizations = $user->isAdmin() 
                ? \App\Models\Organization::exists()
                : $user->organizations()->exists();
                
            if (!$hasOrganizations) {
                // User has no organizations - show appropriate message
                return redirect()->route('organizations.no-access');
            }
            
            // Redirect to organization selection
            return redirect()->route('organizations.select');
        }

        // Verify the user still has access to the selected organization
        $hasAccess = $user->isAdmin() 
            ? \App\Models\Organization::where('id', $currentOrgId)->exists()
            : $user->organizations()->where('id', $currentOrgId)->exists();

        if (!$hasAccess) {
            // Clear invalid organization
            session()->forget('current_organization_id');
            
            // Store the intended URL
            session(['url.intended' => $request->fullUrl()]);
            
            return redirect()->route('organizations.select');
        }

        return $next($request);
    }
}