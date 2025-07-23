<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    /**
     * Display invitation acceptance form.
     */
    public function show($token)
    {
        $invitation = UserInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            return view('invitations.already-accepted');
        }

        if ($invitation->isExpired()) {
            return view('invitations.expired');
        }

        return view('invitations.accept', compact('invitation'));
    }

    /**
     * Process invitation acceptance.
     */
    public function accept(Request $request, $token)
    {
        $invitation = UserInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isAccepted()) {
            return redirect()->route('login')->with('error', 'This invitation has already been accepted.');
        }

        if ($invitation->isExpired()) {
            return redirect()->route('login')->with('error', 'This invitation has expired.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => Hash::make($validated['password']),
            'role' => 'admin', // All invited users are admins
        ]);

        // Mark invitation as accepted
        $invitation->update([
            'accepted_at' => now(),
        ]);

        // Log the user in
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome to ' . config('app.name') . '!');
    }
}
