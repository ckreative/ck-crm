<?php

namespace CkCrm\Leads\Console\Commands;

use App\Models\Organization;
use CkCrm\Leads\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncCalcomBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:sync-calcom 
                            {--organization= : Specific organization ID or slug to sync}
                            {--all : Sync all organizations with Cal.com enabled}
                            {--days= : Number of days to sync (past and future)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync bookings from Cal.com and create/update leads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Determine which organizations to sync
        $organizations = $this->getOrganizationsToSync();

        if ($organizations->isEmpty()) {
            $this->error('No organizations found to sync.');
            return 1;
        }

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($organizations as $organization) {
            $this->info("\nSyncing Cal.com bookings for organization: {$organization->name}");
            
            if (!$organization->hasCalcomEnabled()) {
                $this->warn("Cal.com is not enabled for {$organization->name}, skipping...");
                continue;
            }

            $apiKey = $organization->getCalcomApiKey();
            
            if (!$apiKey) {
                $this->warn("No Cal.com API key configured for {$organization->name}, skipping...");
                continue;
            }

            $settings = $organization->getCalcomSettings();
            $days = (int) ($this->option('days') ?: $settings['sync_days'] ?? 30);
            
            $result = $this->syncOrganizationBookings($organization, $apiKey, $days);
            
            $totalCreated += $result['created'];
            $totalUpdated += $result['updated'];
            $totalSkipped += $result['skipped'];
        }

        $this->info("\nTotal sync completed! Created: {$totalCreated}, Updated: {$totalUpdated}, Skipped: {$totalSkipped}");
        
        return 0;
    }

    /**
     * Get organizations to sync based on command options.
     */
    private function getOrganizationsToSync()
    {
        if ($this->option('all')) {
            // Get all organizations with Cal.com enabled
            return Organization::all()->filter(function ($org) {
                return $org->hasCalcomEnabled();
            });
        }

        if ($organizationId = $this->option('organization')) {
            // Find by slug first (more common use case)
            $organization = Organization::where('slug', $organizationId)->first();
            
            // If not found by slug and it looks like a UUID, try by ID
            if (!$organization && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $organizationId)) {
                $organization = Organization::where('id', $organizationId)->first();
            }

            if (!$organization) {
                $this->error("Organization '{$organizationId}' not found.");
                return collect();
            }

            return collect([$organization]);
        }

        // If no options provided, check if we're in single-tenant mode
        if (config('leads.calcom.api_key')) {
            $this->info('Using legacy single-tenant Cal.com configuration...');
            
            // Create a temporary organization object for backward compatibility
            $legacyOrg = new Organization();
            $legacyOrg->name = 'Legacy Configuration';
            $legacyOrg->id = null; // No organization context for legacy mode
            $legacyOrg->settings = [
                'calcom' => [
                    'enabled' => config('leads.calcom.enabled', false),
                    'api_key' => config('leads.calcom.api_key'),
                    'sync_days' => config('leads.calcom.sync_days', 30),
                ]
            ];
            
            return collect([$legacyOrg]);
        }

        $this->error('Please specify --organization=<id|slug> or use --all to sync all organizations.');
        return collect();
    }

    /**
     * Sync bookings for a specific organization.
     */
    private function syncOrganizationBookings(Organization $organization, string $apiKey, int $days): array
    {
        $startDate = now()->subDays($days)->toIso8601String();
        $endDate = now()->addDays($days)->toIso8601String();

        $this->info("Fetching bookings from {$startDate} to {$endDate}...");

        try {
            // Fetch bookings from Cal.com API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->get('https://api.cal.com/v1/bookings', [
                'apiKey' => $apiKey,
                'startTime' => $startDate,
                'endTime' => $endDate,
            ]);

            if (!$response->successful()) {
                $this->error('Failed to fetch bookings from Cal.com: ' . $response->body());
                return [
                    'created' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                ];
            }

            $data = $response->json();
            
            // Debug the response structure
            if (!is_array($data)) {
                $this->error('Unexpected response format from Cal.com API');
                return 1;
            }
            
            // Cal.com API might return bookings in different formats
            if (isset($data['data']) && is_array($data['data'])) {
                $bookings = $data['data'];
            } elseif (isset($data['bookings']) && is_array($data['bookings'])) {
                $bookings = $data['bookings'];
            } elseif (isset($data[0]) && is_array($data[0])) {
                // Direct array of bookings
                $bookings = $data;
            } else {
                $bookings = [];
            }
            
            $created = 0;
            $updated = 0;
            $skipped = 0;
            
            $this->info("Found " . count($bookings) . " bookings to process.");

            foreach ($bookings as $booking) {
                // Skip if booking is not an array
                if (!is_array($booking)) {
                    $this->warn("Invalid booking format, skipping...");
                    $skipped++;
                    continue;
                }

                // Skip cancelled bookings
                if (($booking['status'] ?? '') === 'CANCELLED') {
                    $skipped++;
                    continue;
                }

                $attendees = $booking['attendees'] ?? [];
                $attendee = is_array($attendees) && count($attendees) > 0 ? $attendees[0] : null;
                
                if (!$attendee || !is_array($attendee)) {
                    $bookingId = $booking['id'] ?? 'unknown';
                    $this->warn("Booking {$bookingId} has no attendees, skipping...");
                    $skipped++;
                    continue;
                }

                // Extract lead data
                $leadData = [
                    'name' => $attendee['name'] ?? 'Unknown',
                    'email' => $attendee['email'],
                    'phone' => $attendee['phone'] ?? null,
                    'notes' => $this->formatNotes($booking),
                    'appointment_date' => !empty($booking['startTime']) ? \Carbon\Carbon::parse($booking['startTime']) : null,
                    'appointment_status' => $this->mapBookingStatus($booking['status'] ?? 'unknown'),
                ];

                // Check if lead already exists by Cal.com event ID
                $bookingId = $booking['id'] ?? $booking['uid'] ?? null;
                
                if (!$bookingId) {
                    $this->warn("Booking has no ID, skipping...");
                    $skipped++;
                    continue;
                }
                
                // For multi-tenant, scope by organization
                $query = Lead::withArchived()->where('calcom_event_id', $bookingId);
                
                if ($organization->id) {
                    $query->where('organization_id', $organization->id);
                }
                
                $lead = $query->first();

                if ($lead) {
                    // Update existing lead (only if not archived)
                    if (!$lead->isArchived()) {
                        $lead->update($leadData);
                        $updated++;
                        $this->info("Updated lead for {$leadData['email']}");
                    } else {
                        $skipped++;
                        $this->info("Skipped archived lead for {$leadData['email']}");
                    }
                } else {
                    // Create new lead
                    $newLeadData = array_merge($leadData, [
                        'calcom_event_id' => $bookingId,
                    ]);
                    
                    // Add organization_id if we have one
                    if ($organization->id) {
                        $newLeadData['organization_id'] = $organization->id;
                    }
                    
                    Lead::create($newLeadData);
                    $created++;
                    $this->info("Created new lead for {$leadData['email']}");
                }
            }

            $this->info("Sync completed for {$organization->name}! Created: {$created}, Updated: {$updated}, Skipped: {$skipped}");
            
            // Log the sync
            Log::info('Cal.com sync completed', [
                'organization' => $organization->name,
                'organization_id' => $organization->id,
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'total_bookings' => count($bookings),
            ]);

            return [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ];

        } catch (\Exception $e) {
            $this->error("Error syncing Cal.com bookings for {$organization->name}: " . $e->getMessage());
            Log::error('Cal.com sync failed', [
                'organization' => $organization->name,
                'organization_id' => $organization->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
            ];
        }
    }

    /**
     * Map Cal.com booking status to local appointment status.
     */
    private function mapBookingStatus(string $calcomStatus): string
    {
        return match(strtoupper($calcomStatus)) {
            'ACCEPTED', 'CONFIRMED' => 'confirmed',
            'PENDING' => 'scheduled',
            'CANCELLED' => 'cancelled',
            'REJECTED' => 'cancelled',
            'RESCHEDULED' => 'rescheduled',
            default => 'scheduled'
        };
    }

    /**
     * Format booking notes from Cal.com data.
     */
    private function formatNotes(array $booking): ?string
    {
        $notes = [];

        // Add event type
        if (!empty($booking['title'])) {
            $notes[] = "Event: {$booking['title']}";
        }

        // Add booking date/time
        if (!empty($booking['startTime'])) {
            $date = \Carbon\Carbon::parse($booking['startTime'])->format('M d, Y g:i A');
            $notes[] = "Scheduled: {$date}";
        }

        // Add any custom responses
        if (!empty($booking['responses']) && is_array($booking['responses'])) {
            foreach ($booking['responses'] as $question => $answer) {
                if (!empty($answer) && (is_string($answer) || is_numeric($answer))) {
                    $notes[] = "{$question}: {$answer}";
                } elseif (is_array($answer)) {
                    $notes[] = "{$question}: " . json_encode($answer);
                }
            }
        }

        // Add description if available
        if (!empty($booking['description'])) {
            $notes[] = "Description: {$booking['description']}";
        }

        return !empty($notes) ? implode("\n", $notes) : null;
    }
}