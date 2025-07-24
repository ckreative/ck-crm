<?php

namespace CkCrm\Leads\Policies;

use CkCrm\Leads\Models\Lead;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class LeadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user): bool
    {
        if (!config('leads.authorization.enabled', true)) {
            return true;
        }

        if (config('leads.authorization.admin_only', true)) {
            return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Lead $lead): bool
    {
        if (!config('leads.authorization.enabled', true)) {
            return true;
        }

        if (config('leads.authorization.admin_only', true)) {
            return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user): bool
    {
        if (!config('leads.authorization.enabled', true)) {
            return true;
        }

        if (config('leads.authorization.admin_only', true)) {
            return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, Lead $lead): bool
    {
        if (!config('leads.authorization.enabled', true)) {
            return true;
        }

        if (config('leads.authorization.admin_only', true)) {
            return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, Lead $lead): bool
    {
        return false; // Leads cannot be deleted
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, Lead $lead): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, Lead $lead): bool
    {
        return false; // Leads cannot be deleted
    }

    /**
     * Determine whether the user can archive the model.
     */
    public function archive($user, Lead $lead): bool
    {
        if (!config('leads.features.archive', true)) {
            return false;
        }

        if (!config('leads.authorization.enabled', true)) {
            return true;
        }

        if (config('leads.authorization.admin_only', true)) {
            return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
        }

        return true;
    }
}