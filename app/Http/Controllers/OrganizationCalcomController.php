<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrganizationCalcomController extends Controller
{
    /**
     * Show the Cal.com settings form for an organization.
     */
    public function edit(Organization $organization)
    {
        // Check if user can manage this organization
        Gate::authorize('manage', $organization);
        
        $calcomSettings = $organization->getCalcomSettings();
        
        // Decrypt API key for display (masked)
        if (!empty($calcomSettings['api_key'])) {
            $apiKey = $organization->getCalcomApiKey();
            $calcomSettings['api_key_masked'] = $apiKey ? substr($apiKey, 0, 10) . str_repeat('*', 20) : '';
        }
        
        return view('organizations.calcom-settings', [
            'organization' => $organization,
            'calcomSettings' => $calcomSettings,
        ]);
    }

    /**
     * Update the Cal.com settings for an organization.
     */
    public function update(Request $request, Organization $organization)
    {
        // Check if user can manage this organization
        Gate::authorize('manage', $organization);
        
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'api_key' => 'nullable|string|min:20',
            'sync_enabled' => 'required|boolean',
            'sync_days' => 'required|integer|min:1|max:365',
        ]);
        
        // Prepare settings update
        $calcomSettings = [
            'enabled' => $validated['enabled'],
            'sync_enabled' => $validated['sync_enabled'],
            'sync_days' => $validated['sync_days'],
        ];
        
        // Only update API key if provided (not masked value)
        if (!empty($validated['api_key']) && !str_contains($validated['api_key'], '*')) {
            $calcomSettings['api_key'] = $validated['api_key'];
        }
        
        $organization->updateCalcomSettings($calcomSettings);
        
        return redirect()
            ->route('app-settings.organizations.details', $organization)
            ->with('success', 'Cal.com settings updated successfully.');
    }

    /**
     * Test the Cal.com API connection.
     */
    public function test(Organization $organization)
    {
        // Check if user can manage this organization
        Gate::authorize('manage', $organization);
        
        if (!$organization->hasCalcomEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Cal.com is not enabled for this organization.',
            ], 400);
        }
        
        $apiKey = $organization->getCalcomApiKey();
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'No API key configured.',
            ], 400);
        }
        
        try {
            // Test the API connection
            $response = \Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->get('https://api.cal.com/v1/me', [
                'apiKey' => $apiKey,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful!',
                    'user' => $data['user']['username'] ?? 'Unknown',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API key or connection failed.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ], 500);
        }
    }
}