<?php

namespace CkCrm\Leads\Models;

use App\Traits\BelongsToOrganization;
use CkCrm\Leads\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CkCrm\Leads\Database\Factories\LeadFactory;

class Lead extends Model
{
    use HasFactory, HasUuid, BelongsToOrganization;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'notes',
        'calcom_event_id',
        'appointment_date',
        'appointment_status',
        'archived_at',
        'organization_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
            'appointment_date' => 'datetime',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        if (config('leads.features.archive', true)) {
            static::addGlobalScope('notArchived', function (Builder $builder) {
                $builder->whereNull('archived_at');
            });
        }
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return LeadFactory::new();
    }

    /**
     * Archive the lead.
     */
    public function archive(): void
    {
        $this->update(['archived_at' => now()]);
    }

    /**
     * Check if the lead is archived.
     */
    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * Scope a query to include archived leads.
     */
    public function scopeWithArchived(Builder $query): Builder
    {
        return $query->withoutGlobalScope('notArchived');
    }

    /**
     * Scope a query to only include archived leads.
     */
    public function scopeOnlyArchived(Builder $query): Builder
    {
        return $query->withoutGlobalScope('notArchived')
            ->whereNotNull('archived_at');
    }

    /**
     * Scope a query to include leads with upcoming appointments.
     */
    public function scopeUpcomingAppointments(Builder $query): Builder
    {
        return $query->whereNotNull('appointment_date')
            ->where('appointment_date', '>', now())
            ->whereIn('appointment_status', ['scheduled', 'confirmed']);
    }

    /**
     * Scope a query to include leads with appointments in a date range.
     */
    public function scopeAppointmentsBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereNotNull('appointment_date')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->whereIn('appointment_status', ['scheduled', 'confirmed']);
    }

    /**
     * Get the table associated with the model.
     */
    public function getTable()
    {
        return config('leads.database.table_name', parent::getTable());
    }
}