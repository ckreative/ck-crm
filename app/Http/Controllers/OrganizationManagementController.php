<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Models\UserInvitation;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrganizationManagementController extends Controller
{
    /**
     * Display a listing of all organizations (admin only).
     */
    public function index()
    {
        $organizations = Organization::withCount('users')
            ->orderBy('name')
            ->paginate(20);

        // Add upcoming appointments count to each organization
        $organizations->getCollection()->transform(function ($organization) {
            $organization->upcoming_appointments_count = $organization->getUpcomingAppointmentsCount();
            return $organization;
        });

        return view('app-settings.organizations.index', compact('organizations'));
    }

    /**
     * Display user's organizations (for non-admin users).
     */
    public function userIndex(Request $request)
    {
        $user = auth()->user();
        
        // Clear current organization selection
        session()->forget('current_organization_id');
        
        // Get sort parameter
        $sort = $request->get('sort', 'name_asc');
        
        // Build base query
        if ($user->isAdmin()) {
            $query = Organization::with(['users' => function ($query) {
                $query->orderBy('name');
            }])->withCount('users');
        } else {
            $query = $user->organizations()
                ->with(['users' => function ($query) {
                    $query->orderBy('name');
                }])
                ->withCount('users');
        }
        
        // Apply sorting
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }
        
        $organizations = $query->paginate(12)->appends(['sort' => $sort]);

        // Add upcoming appointments count to each organization
        $organizations->getCollection()->transform(function ($organization) {
            $organization->upcoming_appointments_count = $organization->getUpcomingAppointmentsCount();
            return $organization;
        });
        
        // Sort by appointments if needed (after fetching appointment counts)
        if ($sort === 'appointments_desc' || $sort === 'appointments_asc') {
            $sorted = $organizations->getCollection()->sortBy(function ($org) {
                return $org->upcoming_appointments_count ?? 0;
            }, SORT_REGULAR, $sort === 'appointments_desc');
            
            $organizations->setCollection($sorted);
        }

        return view('organizations.index', compact('organizations', 'sort'));
    }

    /**
     * Search organizations for AJAX requests.
     */
    public function search(Request $request)
    {
        $user = auth()->user();
        $searchQuery = $request->get('search', '');
        
        // Build base query
        $query = null;
        if ($user->isAdmin()) {
            $query = Organization::with(['users' => function ($q) {
                $q->orderBy('name');
            }])->withCount('users');
        } else {
            $query = $user->organizations()
                ->with(['users' => function ($q) {
                    $q->orderBy('name');
                }])
                ->withCount('users');
        }
        
        // Apply search filter if provided
        if (!empty($searchQuery)) {
            $query->where('name', 'LIKE', '%' . $searchQuery . '%');
        }
        
        // Get paginated results
        $organizations = $query->orderBy('name')->paginate(12);
        
        // Add upcoming appointments count to each organization
        $organizations->getCollection()->transform(function ($organization) {
            $organization->upcoming_appointments_count = $organization->getUpcomingAppointmentsCount();
            return $organization;
        });

        return response()->json([
            'organizations' => $organizations->items(),
            'pagination' => [
                'current_page' => $organizations->currentPage(),
                'last_page' => $organizations->lastPage(),
                'total' => $organizations->total(),
                'has_more' => $organizations->hasMorePages(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create()
    {
        return view('app-settings.organizations.create');
    }

    /**
     * Store a newly created organization.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner_email' => 'required|email',
        ]);

        // Check if owner exists
        $owner = User::where('email', $validated['owner_email'])->first();
        
        // If owner doesn't exist, we need to send an invitation
        if (!$owner) {
            // Check if email is properly configured
            $mailDriver = config('mail.default');
            $resendKey = config('services.resend.key');
            $emailConfigured = $mailDriver === 'log' || 
                             ($mailDriver === 'resend' && $resendKey && $resendKey !== 'your-resend-api-key');
            
            if (!$emailConfigured) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Email service is not configured. Please configure email settings or use the log driver before creating organizations with new users.');
            }
            
            // Check if there's an existing invitation
            $existingInvitation = UserInvitation::where('email', $validated['owner_email'])->first();
            
            if ($existingInvitation) {
                if ($existingInvitation->isAccepted()) {
                    // Invitation was accepted but user doesn't exist (edge case)
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'This invitation has been accepted but the user account was not created properly. Please contact support.');
                }
                
                // Pending invitation exists
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'This email address already has a pending invitation. Please wait for them to accept it or cancel the existing invitation first.');
            }
        }

        // Generate slug from name
        $slug = Str::slug($validated['name']);
        
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (Organization::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Create organization
        $organization = Organization::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'settings' => [],
        ]);
        
        if ($owner) {
            // Add existing user as owner
            $organization->addUser($owner, 'org_owner');
            $owner->setCurrentOrganization($organization);
        } else {
            // We already checked for existing invitations and email config above
            // Create new invitation
            $invitation = UserInvitation::create([
                'email' => $validated['owner_email'],
                'token' => Str::random(64),
                'invited_by' => auth()->id(),
                'expires_at' => now()->addDays(7),
                'organization_id' => $organization->id,
                'organization_role' => 'org_owner',
            ]);

            // Send invitation email
            Mail::to($invitation->email)->send(new InvitationMail($invitation));
        }

        // If created from drawer, redirect back to organizations list
        if ($request->input('from_drawer')) {
            return redirect()
                ->route('organizations.index')
                ->with('success', $owner 
                    ? 'Organization created successfully.' 
                    : 'Organization created successfully. An invitation has been sent to ' . $validated['owner_email']);
        }

        return redirect()
            ->route('app-settings.organizations.details', $organization)
            ->with('success', $owner 
                ? 'Organization created successfully.' 
                : 'Organization created successfully. An invitation has been sent to ' . $validated['owner_email']);
    }

    /**
     * Show the organization details page.
     */
    public function details(Organization $organization)
    {
        $organization->load('users');
        return view('app-settings.organizations.details', compact('organization'));
    }

    /**
     * Update the specified organization.
     */
    public function update(Request $request, Organization $organization)
    {
        // Check if this is a Cal.com settings update
        if ($request->has('update_calcom')) {
            $validated = $request->validate([
                'calcom_enabled' => 'required|boolean',
                'calcom_api_key' => 'nullable|string|min:20',
            ]);

            // Prepare Cal.com settings update
            $calcomSettings = [
                'enabled' => $validated['calcom_enabled'],
                'sync_enabled' => true, // Always enabled
                'sync_days' => 30, // Default to 30 days
            ];

            // Only update API key if provided (not masked value)
            if (!empty($validated['calcom_api_key']) && !str_contains($validated['calcom_api_key'], '*')) {
                $calcomSettings['api_key'] = $validated['calcom_api_key'];
            }

            $organization->updateCalcomSettings($calcomSettings);

            return redirect()
                ->route('app-settings.organizations.details', $organization)
                ->with('success', 'Cal.com settings updated successfully.');
        }

        // Regular organization update
        $rules = [
            'name' => 'required|string|max:255',
        ];

        // Add logo validation if file is uploaded
        if ($request->hasFile('logo')) {
            $rules['logo'] = 'image|mimes:jpg,jpeg,png,svg|max:2048';
        }

        if ($request->has('remove_logo')) {
            $rules['remove_logo'] = 'boolean';
        }

        $validated = $request->validate($rules);

        // Handle logo removal
        if ($request->input('remove_logo')) {
            if ($organization->logo_path) {
                Storage::delete($organization->logo_path);
                $organization->logo_path = null;
            }
        }

        // Handle logo upload
        if ($request->hasFile('logo') && !$request->input('remove_logo')) {
            // Delete old logo if exists
            if ($organization->logo_path) {
                Storage::delete($organization->logo_path);
            }
            
            // Store new logo using default disk (public for local, r2 for production)
            $file = $request->file('logo');
            $filename = Str::slug($organization->slug) . '_' . time() . '.' . $file->extension();
            $path = $file->storeAs('logos', $filename);
            $organization->logo_path = $path;
        }

        // Update organization
        $organization->name = $validated['name'];
        $organization->save();

        return redirect()
            ->route('app-settings.index')
            ->with('success', 'Organization updated successfully.');
    }

    /**
     * Remove the specified organization.
     */
    public function destroy(Organization $organization)
    {
        // Check if this is the last organization
        if (Organization::count() === 1) {
            return back()->with('error', 'Cannot delete the last organization.');
        }

        // Remove all users from organization
        $organization->users()->detach();

        // Delete the organization
        $organization->delete();

        return redirect()
            ->route('organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }

    /**
     * Show organization members.
     */
    public function members(Organization $organization)
    {
        $members = $organization->users()
            ->orderBy('name')
            ->paginate(10);

        return view('app-settings.organizations.members', compact('organization', 'members'));
    }

    /**
     * Add a member to the organization.
     */
    public function addMember(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'user_email' => 'required|email|exists:users,email',
            'role' => 'required|in:org_owner,org_admin,org_manager,org_member,org_guest',
        ]);

        $user = User::where('email', $validated['user_email'])->first();

        // Check if user is already a member
        if ($organization->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User is already a member of this organization.');
        }

        // Add user to organization
        $organization->addUser($user, $validated['role']);

        return back()->with('success', 'Member added successfully.');
    }

    /**
     * Update a member's role in the organization.
     */
    public function updateMemberRole(Request $request, Organization $organization, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:org_owner,org_admin,org_manager,org_member,org_guest',
        ]);

        // Ensure at least one owner remains
        if ($user->pivot->role === 'org_owner' && $validated['role'] !== 'org_owner') {
            $ownerCount = $organization->users()->wherePivot('role', 'org_owner')->count();
            if ($ownerCount <= 1) {
                return back()->with('error', 'Organization must have at least one owner.');
            }
        }

        $organization->updateUserRole($user, $validated['role']);

        return back()->with('success', 'Member role updated successfully.');
    }

    /**
     * Remove a member from the organization.
     */
    public function removeMember(Organization $organization, User $user)
    {
        // Ensure at least one owner remains
        if ($user->pivot->role === 'org_owner') {
            $ownerCount = $organization->users()->wherePivot('role', 'org_owner')->count();
            if ($ownerCount <= 1) {
                return back()->with('error', 'Organization must have at least one owner.');
            }
        }

        $organization->removeUser($user);

        // If this was the user's current organization, clear it
        if ($user->current_organization_id === $organization->id) {
            $user->current_organization_id = null;
            $user->save();
        }

        return back()->with('success', 'Member removed successfully.');
    }
}