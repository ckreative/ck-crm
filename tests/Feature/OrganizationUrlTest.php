<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_url_routing()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'slug' => 'test-org',
        ]);
        
        $organization->addUser($user, 'org_owner');
        
        $response = $this->actingAs($user)
            ->get('/test-org/dashboard');
            
        $response->assertStatus(200);
        $this->assertEquals($organization->id, app('current_organization')->id);
    }
    
    public function test_organization_switch_redirects_to_org_url()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $org1 = Organization::factory()->create(['slug' => 'org-one']);
        $org2 = Organization::factory()->create(['slug' => 'org-two']);
        
        $org1->addUser($user, 'org_member');
        $org2->addUser($user, 'org_member');
        
        $this->actingAs($user);
        
        // Switch to org2
        $response = $this->post('/organization/switch/' . $org2->id);
        
        $response->assertRedirect('/org-two/dashboard');
    }
    
    public function test_reserved_slugs_cannot_be_used()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The slug 'dashboard' is reserved");
        
        Organization::create([
            'name' => 'Dashboard Org',
            'slug' => 'dashboard',
        ]);
    }
    
    public function test_unauthorized_access_to_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['slug' => 'private-org']);
        
        // User is not a member of this organization
        $response = $this->actingAs($user)
            ->get('/private-org/dashboard');
            
        $response->assertStatus(403);
    }
}