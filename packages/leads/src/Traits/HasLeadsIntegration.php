<?php

namespace CkCrm\Leads\Traits;

use CkCrm\Leads\Models\Lead;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasLeadsIntegration
{
    /**
     * Get all leads created by the user.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    /**
     * Get active leads created by the user.
     */
    public function activeLeads(): HasMany
    {
        return $this->leads()->whereNull('archived_at');
    }

    /**
     * Get archived leads created by the user.
     */
    public function archivedLeads(): HasMany
    {
        return $this->leads()->withArchived()->whereNotNull('archived_at');
    }

    /**
     * Check if user can manage leads.
     */
    public function canManageLeads(): bool
    {
        if (method_exists($this, 'isAdmin')) {
            return $this->isAdmin();
        }

        return false;
    }
}