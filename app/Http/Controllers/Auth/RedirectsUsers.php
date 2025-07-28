<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

trait RedirectsUsers
{
    /**
     * Get the post-authentication redirect path for a user.
     */
    protected function redirectTo(User $user): RedirectResponse
    {
        // Super admins always go to organizations page
        if ($user->isSuperAdmin()) {
            return redirect()->route('organizations.index');
        }
        
        // Regular users: check if they have a current organization
        if ($user->current_organization_id && $user->currentOrganization) {
            return redirect()->intended(route('dashboard', ['organization' => $user->currentOrganization->slug], absolute: false));
        }
        
        // Otherwise, redirect to organization selection
        return redirect()->route('organizations.select');
    }
}