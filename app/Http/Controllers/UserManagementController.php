<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInvitation;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display users list with active users and pending invitations.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        $invitations = UserInvitation::with('invitedBy')
            ->whereNull('accepted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('app-settings.users.index', compact('users', 'invitations'));
    }

    /**
     * Send new invitation.
     */
    public function invite(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
                Rule::unique('user_invitations', 'email')->where(function ($query) {
                    $query->whereNull('accepted_at');
                }),
            ],
        ]);

        $invitation = UserInvitation::create([
            'email' => $validated['email'],
            'token' => Str::random(64),
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return back()
            ->with('success', 'Invitation sent successfully to ' . $invitation->email)
            ->with('active_tab', 'invitations');
    }

    /**
     * Resend invitation email.
     */
    public function resend(Request $request, UserInvitation $invitation)
    {
        if ($invitation->isAccepted()) {
            return back()->with('error', 'This invitation has already been accepted.');
        }

        if ($invitation->isExpired()) {
            return back()->with('error', 'This invitation has expired.');
        }

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        // Preserve the active tab when redirecting back
        $activeTab = $request->input('active_tab', 'invitations');
        
        return back()
            ->with('success', 'Invitation resent successfully to ' . $invitation->email)
            ->with('active_tab', $activeTab);
    }

    /**
     * Cancel pending invitation.
     */
    public function cancel(Request $request, UserInvitation $invitation)
    {
        if ($invitation->isAccepted()) {
            return back()->with('error', 'Cannot cancel an accepted invitation.');
        }

        $invitation->delete();

        // Preserve the active tab when redirecting back
        $activeTab = $request->input('active_tab', 'invitations');
        
        return back()
            ->with('success', 'Invitation cancelled successfully.')
            ->with('active_tab', $activeTab);
    }
}
