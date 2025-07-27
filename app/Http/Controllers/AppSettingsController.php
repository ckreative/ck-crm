<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppSettingsController extends Controller
{
    /**
     * Display the app settings main page.
     * Shows general settings.
     */
    public function index()
    {
        $user = auth()->user();
        $organization = $user->currentOrganization;
        
        // If no current organization, get the first one
        if (!$organization) {
            $organization = $user->organizations()->first();
        }
        
        // If still no organization, redirect to organization selection
        if (!$organization) {
            return redirect()->route('organizations.index');
        }
        
        return view('app-settings.general', compact('organization'));
    }
}
