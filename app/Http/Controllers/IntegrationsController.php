<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IntegrationsController extends Controller
{
    /**
     * Display the integrations settings page.
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
        
        return view('app-settings.integrations', compact('organization'));
    }
}