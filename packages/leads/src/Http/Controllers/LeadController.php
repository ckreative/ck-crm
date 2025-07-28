<?php

namespace CkCrm\Leads\Http\Controllers;

use CkCrm\Leads\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controller;

class LeadController extends Controller
{
    /**
     * Display a listing of the leads.
     */
    public function index(Request $request)
    {
        $organization = app('current_organization');
        $query = Lead::where('organization_id', $organization->id);

        // Filter by archived status
        if ($request->has('archived') && config('leads.features.archive', true)) {
            if ($request->boolean('archived')) {
                $query->withArchived()->onlyArchived();
            }
        }

        // Filter by time period
        $period = $request->get('period', 'all');
        switch ($period) {
            case '7days':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case '30days':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            // 'all' shows everything, no additional filter needed
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Pass additional data that might be needed
        $sort = $request->get('sort', 'created_at');

        return view('leads::index', compact('leads', 'sort'));
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create()
    {
        if (config('leads.authorization.enabled', true)) {
            Gate::authorize('create', Lead::class);
        }

        return view('leads::create');
    }

    /**
     * Store a newly created lead in storage.
     */
    public function store(Request $request)
    {
        if (config('leads.authorization.enabled', true)) {
            Gate::authorize('create', Lead::class);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $organization = app('current_organization');
        $validated['organization_id'] = $organization->id;

        Lead::create($validated);

        return redirect()->route(config('leads.routes.as', 'leads.') . 'index', ['organization' => $organization->slug])
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        return view('leads::show', compact('lead'));
    }

    /**
     * Show the form for editing the specified lead.
     */
    public function edit(Lead $lead)
    {
        if (config('leads.authorization.enabled', true)) {
            Gate::authorize('update', $lead);
        }

        return view('leads::edit', compact('lead'));
    }

    /**
     * Update the specified lead in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        if (config('leads.authorization.enabled', true)) {
            Gate::authorize('update', $lead);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $lead->update($validated);

        return redirect()->route(config('leads.routes.as', 'leads.') . 'index')
            ->with('success', 'Lead updated successfully.');
    }

    /**
     * Archive the specified lead.
     */
    public function archive(Lead $lead)
    {
        if (!config('leads.features.archive', true)) {
            abort(404);
        }

        if (config('leads.authorization.enabled', true)) {
            Gate::authorize('archive', $lead);
        }

        $lead->archive();

        return back()->with('success', 'Lead archived successfully.');
    }

    /**
     * Bulk archive multiple leads.
     */
    public function bulkArchive(Request $request)
    {
        if (!config('leads.features.bulk_actions', true)) {
            abort(404);
        }

        if (config('leads.authorization.enabled', true)) {
            Gate::authorize('create', Lead::class); // Using create permission for bulk actions
        }

        $validated = $request->validate([
            'lead_ids' => ['required', 'array'],
            'lead_ids.*' => ['exists:' . config('leads.database.table_name', 'leads') . ',id'],
        ]);

        Lead::whereIn('id', $validated['lead_ids'])->update(['archived_at' => now()]);

        return back()->with('success', count($validated['lead_ids']) . ' leads archived successfully.');
    }
}