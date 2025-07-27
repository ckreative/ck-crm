<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if ($user) {
            // Super admins can see everything
            if ($user->isSuperAdmin()) {
                return;
            }

            // Regular users only see data from their current organization
            if ($user->current_organization_id) {
                $builder->where($model->getTable() . '.organization_id', $user->current_organization_id);
            } else {
                // If no current organization is set, don't show any data
                $builder->whereRaw('1 = 0');
            }
        }
    }
}