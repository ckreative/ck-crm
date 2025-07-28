<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class FaviconController extends Controller
{
    /**
     * Serve organization-specific favicon.
     */
    public function favicon(Request $request, string $organization, ?int $size = null)
    {
        // Find organization by slug
        $org = Organization::where('slug', $organization)->first();
        
        if (!$org) {
            return $this->defaultFavicon($size ?: 32);
        }
        
        // Default size is 32 for standard favicon
        $size = $size ?: 32;
        
        // Validate size
        $allowedSizes = [16, 32, 48, 64, 96, 128, 180, 192, 256, 512];
        if (!in_array($size, $allowedSizes)) {
            $size = 32;
        }
        
        // Cache key for this specific favicon
        $cacheKey = "favicon_{$org->id}_{$size}";
        
        // Try to get from cache first
        $favicon = Cache::remember($cacheKey, 60 * 60 * 24 * 7, function () use ($org, $size) {
            return $this->generateFavicon($org, $size);
        });
        
        if (!$favicon) {
            // If no favicon could be generated, return default
            return $this->defaultFavicon($size);
        }
        
        // Return the favicon with appropriate headers
        return response($favicon, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=604800', // 1 week
            'Expires' => gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT',
        ]);
    }
    
    /**
     * Generate favicon from organization logo.
     */
    private function generateFavicon(Organization $organization, int $size): ?string
    {
        if (!$organization->logo_path) {
            return null;
        }
        
        try {
            // Get the logo from storage
            if (!Storage::exists($organization->logo_path)) {
                return null;
            }
            
            $logoContent = Storage::get($organization->logo_path);
            
            // Process the image
            $image = Image::read($logoContent);
            
            // Make it square by cropping to center
            $width = $image->width();
            $height = $image->height();
            $minSize = min($width, $height);
            
            $image->crop($minSize, $minSize, position: 'center');
            
            // Resize to requested size
            $image->resize($size, $size);
            
            // Convert to PNG for better quality
            return $image->toPng()->toString();
            
        } catch (\Exception $e) {
            \Log::error('Failed to generate favicon', [
                'organization_id' => $organization->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Return default favicon.
     */
    private function defaultFavicon(int $size)
    {
        // Try to read and resize the default favicon
        $defaultPath = public_path('favicon.ico');
        
        if (file_exists($defaultPath)) {
            try {
                $image = Image::read($defaultPath);
                $image->resize($size, $size);
                
                return response($image->toPng()->toString(), 200, [
                    'Content-Type' => 'image/png',
                    'Cache-Control' => 'public, max-age=604800',
                ]);
            } catch (\Exception $e) {
                // Fall through to generated favicon
            }
        }
        
        // Generate a simple default favicon
        $image = Image::create($size, $size);
        $image->fill('#6366f1'); // Indigo color matching the app theme
        
        return response($image->toPng()->toString(), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}