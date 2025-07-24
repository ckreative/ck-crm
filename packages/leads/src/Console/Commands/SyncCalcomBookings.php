<?php

namespace CkCrm\Leads\Console\Commands;

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
    protected $signature = 'leads:sync-calcom {--days= : Number of days to sync (past and future)}';

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
        if (!config('leads.calcom.enabled', false)) {
            $this->error('Cal.com integration is disabled. Enable it in the leads config.');
            return 1;
        }

        $apiKey = config('leads.calcom.api_key');
        
        if (!$apiKey) {
            $this->error('Cal.com API key not configured. Please set CALCOM_API_KEY in your .env file.');
            return 1;
        }

        $days = (int) ($this->option('days') ?: config('leads.calcom.sync_days', 30));
        $startDate = now()->subDays($days)->toIso8601String();
        $endDate = now()->addDays($days)->toIso8601String();

        $this->info("Syncing Cal.com bookings from {$startDate} to {$endDate}...");

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
                return 1;
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
                ];

                // Check if lead already exists by Cal.com event ID
                $bookingId = $booking['id'] ?? $booking['uid'] ?? null;
                
                if (!$bookingId) {
                    $this->warn("Booking has no ID, skipping...");
                    $skipped++;
                    continue;
                }
                
                $lead = Lead::withArchived()->where('calcom_event_id', $bookingId)->first();

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
                    Lead::create(array_merge($leadData, [
                        'calcom_event_id' => $bookingId,
                    ]));
                    $created++;
                    $this->info("Created new lead for {$leadData['email']}");
                }
            }

            $this->info("Sync completed! Created: {$created}, Updated: {$updated}, Skipped: {$skipped}");
            
            // Log the sync
            Log::info('Cal.com sync completed', [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'total_bookings' => count($bookings),
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Error syncing Cal.com bookings: ' . $e->getMessage());
            Log::error('Cal.com sync failed', ['error' => $e->getMessage()]);
            return 1;
        }
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