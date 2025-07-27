<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class OrganizationSelectionController extends Controller
{
    /**
     * Show the organization selection page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Clear current organization selection
        session()->forget('current_organization_id');
        
        // Admins see all organizations
        if ($user->isAdmin()) {
            $organizations = Organization::withCount('users')->orderBy('name')->paginate(12);
        } else {
            $organizations = $user->organizations()->withCount('users')->orderBy('name')->paginate(12);
        }

        // Add upcoming appointments count to each organization
        $organizations->getCollection()->transform(function ($organization) {
            $organization->upcoming_appointments_count = $organization->getUpcomingAppointmentsCount();
            return $organization;
        });
        
        return view('organizations.select', compact('organizations'));
    }
    
    /**
     * Show the no access page for users without organizations.
     */
    public function noAccess()
    {
        return view('organizations.no-access');
    }
}