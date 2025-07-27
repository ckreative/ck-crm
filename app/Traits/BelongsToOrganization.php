<?php

namespace App\Traits;

use App\Models\Organization;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToOrganization(): void
    {
        // Add global scope to filter by organization
        static::addGlobalScope(new OrganizationScope);

        // Automatically set organization_id when creating
        static::creating(function ($model) {
            if (!$model->organization_id && auth()->user()) {
                $model->organization_id = auth()->user()->current_organization_id;
            }
        });
    }

    /**
     * Get the organization that owns the model.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope a query to only include models from a specific organization.
     */
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where($this->getTable() . '.organization_id', $organizationId);
    }

    /**
     * Scope a query to only include models from the current user's organization.
     */
    public function scopeForCurrentOrganization($query)
    {
        if (auth()->user() && auth()->user()->current_organization_id) {
            return $query->where($this->getTable() . '.organization_id', auth()->user()->current_organization_id);
        }

        return $query;
    }
}