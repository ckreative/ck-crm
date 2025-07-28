<?php

namespace App\Jobs;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SyncCalcomForOrganization implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The organization instance.
     *
     * @var \App\Models\Organization
     */
    protected $organization;

    /**
     * The number of days to sync.
     *
     * @var int
     */
    protected $days;

    /**
     * Create a new job instance.
     */
    public function __construct(Organization $organization, ?int $days = null)
    {
        $this->organization = $organization;
        $this->days = $days;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting Cal.com sync job for organization', [
                'organization_id' => $this->organization->id,
                'organization_name' => $this->organization->name,
            ]);

            // Use the sync days from settings if not explicitly provided
            if ($this->days === null) {
                $settings = $this->organization->getCalcomSettings();
                $this->days = $settings['sync_days'] ?? 30;
            }

            // Run the sync command for this specific organization
            $exitCode = Artisan::call('leads:sync-calcom', [
                '--organization' => $this->organization->slug,
                '--days' => $this->days,
            ]);

            if ($exitCode === 0) {
                Log::info('Cal.com sync job completed successfully', [
                    'organization_id' => $this->organization->id,
                    'organization_name' => $this->organization->name,
                    'output' => Artisan::output(),
                ]);
            } else {
                Log::error('Cal.com sync job failed', [
                    'organization_id' => $this->organization->id,
                    'organization_name' => $this->organization->name,
                    'exit_code' => $exitCode,
                    'output' => Artisan::output(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Cal.com sync job encountered an exception', [
                'organization_id' => $this->organization->id,
                'organization_name' => $this->organization->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to mark job as failed for retry
            throw $e;
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 300, 900]; // 1 min, 5 min, 15 min
    }

    /**
     * Determine the number of times the job may be attempted.
     */
    public function tries(): int
    {
        return 3;
    }
}