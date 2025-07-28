<?php

namespace App\Models;

use App\Traits\HasUuid;
use CkCrm\Leads\Models\Lead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Organization extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'settings',
        'logo_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = static::generateUniqueSlug($organization->name);
            } else {
                // Validate slug is not reserved
                if (static::isReservedSlug($organization->slug)) {
                    throw new \InvalidArgumentException("The slug '{$organization->slug}' is reserved and cannot be used.");
                }
            }
        });
        
        static::updating(function ($organization) {
            if ($organization->isDirty('slug')) {
                // Validate slug is not reserved
                if (static::isReservedSlug($organization->slug)) {
                    throw new \InvalidArgumentException("The slug '{$organization->slug}' is reserved and cannot be used.");
                }
            }
        });
    }

    /**
     * Get the users that belong to the organization.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->withPivot('role', 'joined_at');
    }

    /**
     * Get the leads for the organization.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the user invitations for the organization.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(UserInvitation::class);
    }

    /**
     * Get users with a specific role in the organization.
     */
    public function getUsersByRole(string $role)
    {
        return $this->users()->wherePivot('role', $role)->get();
    }

    /**
     * Get the owner of the organization.
     */
    public function owner()
    {
        return $this->users()->wherePivot('role', 'org_owner')->first();
    }

    /**
     * Check if a user has a specific role in the organization.
     */
    public function userHasRole(User $user, string $role): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', $role)
            ->exists();
    }

    /**
     * Add a user to the organization with a specific role.
     */
    public function addUser(User $user, string $role = 'org_member'): void
    {
        $this->users()->attach($user->id, [
            'role' => $role,
            'joined_at' => now()
        ]);
    }

    /**
     * Update a user's role in the organization.
     */
    public function updateUserRole(User $user, string $newRole): void
    {
        $this->users()->updateExistingPivot($user->id, ['role' => $newRole]);
    }

    /**
     * Remove a user from the organization.
     */
    public function removeUser(User $user): void
    {
        $this->users()->detach($user->id);
    }

    /**
     * Get Cal.com settings for the organization.
     */
    public function getCalcomSettings(): array
    {
        return $this->settings['calcom'] ?? [
            'enabled' => false,
            'api_key' => null,
            'sync_enabled' => false,
            'sync_days' => 30,
        ];
    }

    /**
     * Check if Cal.com is enabled for the organization.
     */
    public function hasCalcomEnabled(): bool
    {
        return $this->getCalcomSettings()['enabled'] ?? false;
    }

    /**
     * Get decrypted Cal.com API key.
     */
    public function getCalcomApiKey(): ?string
    {
        $settings = $this->getCalcomSettings();
        
        if (empty($settings['api_key'])) {
            return null;
        }

        try {
            return Crypt::decryptString($settings['api_key']);
        } catch (\Exception $e) {
            // If decryption fails, assume it's not encrypted (backward compatibility)
            return $settings['api_key'];
        }
    }

    /**
     * Set Cal.com API key (encrypted).
     */
    public function setCalcomApiKey(?string $apiKey): void
    {
        $settings = $this->settings ?? [];
        
        if (!isset($settings['calcom'])) {
            $settings['calcom'] = [];
        }

        if ($apiKey) {
            $settings['calcom']['api_key'] = Crypt::encryptString($apiKey);
        } else {
            $settings['calcom']['api_key'] = null;
        }

        $this->settings = $settings;
        $this->save();
    }

    /**
     * Update Cal.com settings.
     */
    public function updateCalcomSettings(array $calcomSettings): void
    {
        $settings = $this->settings ?? [];
        
        // Encrypt API key if provided
        if (isset($calcomSettings['api_key'])) {
            $calcomSettings['api_key'] = $calcomSettings['api_key'] 
                ? Crypt::encryptString($calcomSettings['api_key']) 
                : null;
        }

        $settings['calcom'] = array_merge(
            $this->getCalcomSettings(),
            $calcomSettings
        );

        $this->settings = $settings;
        $this->save();
    }

    /**
     * Get upcoming appointments count from local database.
     */
    public function getUpcomingAppointmentsCount(): int
    {
        return $this->leads()
            ->upcomingAppointments()
            ->count();
    }

    /**
     * Get URL for logo with specific dimensions.
     */
    public function getLogoUrl($width = null, $height = null, $quality = 85)
    {
        if (!$this->logo_path) {
            return null;
        }
        
        // Build transformation options
        $options = [];
        if ($width) {
            $options[] = "width=$width";
        }
        if ($height) {
            $options[] = "height=$height";
        }
        if ($quality !== 85) {
            $options[] = "quality=$quality";
        }
        
        // For production with R2, return the CDN URL directly
        $disk = config('filesystems.default');
        if ($disk === 'r2' && !empty($options)) {
            $baseUrl = rtrim(config('filesystems.disks.r2.url'), '/');
            $optionsString = implode(',', $options);
            return "{$baseUrl}/cdn-cgi/image/{$optionsString}/{$this->logo_path}";
        }
        
        // For local development, use the image transform package
        if (!empty($options)) {
            $optionsString = implode(',', $options);
            $routePrefix = config('image-transform-url.route_prefix', 'image-transform');
            return url("/{$routePrefix}/{$optionsString}/{$this->logo_path}");
        }
        
        // If no transformations, return storage URL
        return Storage::url($this->logo_path);
    }

    /**
     * Get srcset attribute for responsive images (supports retina displays).
     */
    public function getLogoSrcset($width, $height = null)
    {
        if (!$this->logo_path) {
            return null;
        }
        
        // If height not specified, make it square
        $height = $height ?? $width;
        
        // Generate URLs for 1x and 2x
        $url1x = $this->getLogoUrl($width, $height);
        $url2x = $this->getLogoUrl($width * 2, $height * 2);
        
        return "{$url1x} 1x, {$url2x} 2x";
    }

    /**
     * Get default logo URL (200x200).
     */
    public function getLogoUrlAttribute()
    {
        return $this->getLogoUrl(200, 200);
    }

    /**
     * Get thumbnail logo URL (48x48).
     */
    public function getLogoThumbnailUrlAttribute()
    {
        return $this->getLogoUrl(48, 48);
    }

    /**
     * Get large logo URL (400x400).
     */
    public function getLogoLargeUrlAttribute()
    {
        return $this->getLogoUrl(400, 400);
    }

    /**
     * Generate a unique slug for the organization.
     */
    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;
        
        // Check against reserved slugs
        while (static::isReservedSlug($slug) || static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        
        return $slug;
    }

    /**
     * Check if a slug is reserved.
     */
    public static function isReservedSlug(string $slug): bool
    {
        return in_array(strtolower($slug), array_map('strtolower', config('app.reserved_slugs', [])));
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}