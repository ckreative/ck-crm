<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
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

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
