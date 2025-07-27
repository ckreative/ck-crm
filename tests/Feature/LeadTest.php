<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_leads_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get(route('app-settings.leads.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('app-settings.leads.index');
    }

    public function test_non_admin_cannot_view_leads_index(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->get(route('app-settings.leads.index'));
        
        $response->assertStatus(403);
    }

    public function test_admin_can_create_lead(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'company' => 'Example Corp',
            'notes' => 'Test lead',
        ];
        
        $response = $this->actingAs($admin)->post(route('app-settings.leads.store'), $leadData);
        
        $response->assertRedirect(route('app-settings.leads.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('leads', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_non_admin_cannot_create_lead(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];
        
        $response = $this->actingAs($user)->post(route('app-settings.leads.store'), $leadData);
        
        $response->assertStatus(403);
        $this->assertDatabaseMissing('leads', ['email' => 'john@example.com']);
    }

    public function test_admin_can_update_lead(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lead = Lead::factory()->create();
        
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '+9876543210',
            'company' => 'Updated Corp',
            'notes' => 'Updated notes',
        ];
        
        $response = $this->actingAs($admin)->put(route('app-settings.leads.update', $lead), $updateData);
        
        $response->assertRedirect(route('app-settings.leads.index'));
        $response->assertSessionHas('success');
        
        $lead->refresh();
        $this->assertEquals('Updated Name', $lead->name);
        $this->assertEquals('updated@example.com', $lead->email);
    }

    public function test_non_admin_cannot_update_lead(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $lead = Lead::factory()->create();
        
        $response = $this->actingAs($user)->put(route('app-settings.leads.update', $lead), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
        
        $response->assertStatus(403);
    }

    public function test_admin_can_archive_lead(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lead = Lead::factory()->create();
        
        $response = $this->actingAs($admin)->post(route('app-settings.leads.archive', $lead));
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $lead->refresh();
        $this->assertNotNull($lead->archived_at);
    }

    public function test_non_admin_cannot_archive_lead(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $lead = Lead::factory()->create();
        
        $response = $this->actingAs($user)->post(route('app-settings.leads.archive', $lead));
        
        $response->assertStatus(403);
        
        $lead->refresh();
        $this->assertNull($lead->archived_at);
    }

    public function test_archived_leads_are_excluded_from_default_queries(): void
    {
        Lead::factory()->count(3)->create();
        Lead::factory()->create(['archived_at' => now()]);
        
        $leads = Lead::all();
        
        $this->assertCount(3, $leads);
    }

    public function test_archived_leads_can_be_included_with_scope(): void
    {
        Lead::factory()->count(3)->create();
        Lead::factory()->count(2)->create(['archived_at' => now()]);
        
        $allLeads = Lead::withArchived()->get();
        $onlyArchived = Lead::onlyArchived()->get();
        
        $this->assertCount(5, $allLeads);
        $this->assertCount(2, $onlyArchived);
    }

    public function test_leads_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lead = Lead::factory()->create();
        
        // Try to access a delete route (which shouldn't exist)
        $response = $this->actingAs($admin)->delete("/app-settings/leads/{$lead->id}");
        
        // Should return 405 Method Not Allowed or 404 Not Found
        $this->assertContains($response->status(), [404, 405]);
        
        // Lead should still exist
        $this->assertNotNull($lead->fresh());
    }

    public function test_bulk_archive_leads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $leads = Lead::factory()->count(3)->create();
        
        $response = $this->actingAs($admin)->post(route('app-settings.leads.bulk-archive'), [
            'lead_ids' => $leads->pluck('id')->toArray(),
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success', '3 leads archived successfully.');
        
        foreach ($leads as $lead) {
            $this->assertNotNull($lead->fresh()->archived_at);
        }
    }

    public function test_calcom_sync_creates_lead_with_event_id(): void
    {
        // Test that leads created with calcom_event_id are unique
        $lead = Lead::factory()->create(['calcom_event_id' => 'evt_123']);
        
        $this->assertDatabaseHas('leads', [
            'calcom_event_id' => 'evt_123',
        ]);
        
        // Attempting to create another lead with same event ID should fail
        $this->expectException(\Illuminate\Database\QueryException::class);
        Lead::factory()->create(['calcom_event_id' => 'evt_123']);
    }
}
