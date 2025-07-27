<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'current_organization_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the organizations that the user belongs to.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_user')
            ->withPivot('role', 'joined_at');
    }

    /**
     * Get the user's current organization.
     */
    public function currentOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    /**
     * Check if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if the user is an admin (legacy support).
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get the invitations sent by this user.
     */
    public function sentInvitations()
    {
        return $this->hasMany(UserInvitation::class, 'invited_by');
    }

    /**
     * Get the user's role in a specific organization.
     */
    public function getOrganizationRole(Organization $organization): ?string
    {
        $pivot = $this->organizations()
            ->where('organization_id', $organization->id)
            ->first()?->pivot;
        
        return $pivot?->role;
    }

    /**
     * Check if the user has a specific role in an organization.
     */
    public function hasOrganizationRole(Organization $organization, string $role): bool
    {
        return $this->getOrganizationRole($organization) === $role;
    }

    /**
     * Check if the user can access an organization.
     */
    public function canAccessOrganization(Organization $organization): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->organizations()->where('organization_id', $organization->id)->exists();
    }

    /**
     * Check if the user has permission to perform an action in an organization.
     */
    public function hasOrganizationPermission(Organization $organization, string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->getOrganizationRole($organization);
        
        if (!$role) {
            return false;
        }

        // Define permissions for each role
        $permissions = [
            'org_owner' => ['manage_organization', 'manage_users', 'manage_billing', 'manage_leads', 'view_reports', 'export_data'],
            'org_admin' => ['manage_users', 'manage_leads', 'view_reports', 'export_data'],
            'org_manager' => ['manage_leads', 'view_reports', 'export_data'],
            'org_member' => ['manage_own_leads'],
            'org_guest' => ['view_assigned_data'],
        ];

        return in_array($permission, $permissions[$role] ?? []);
    }

    /**
     * Set the user's current organization.
     */
    public function setCurrentOrganization(Organization $organization): void
    {
        if ($this->canAccessOrganization($organization)) {
            $this->current_organization_id = $organization->id;
            $this->save();
        }
    }
}
