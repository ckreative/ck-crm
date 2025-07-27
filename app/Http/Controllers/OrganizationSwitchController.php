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

        // Redirect back to previous page or dashboard
        return redirect()->intended(route('dashboard'));
    }
}