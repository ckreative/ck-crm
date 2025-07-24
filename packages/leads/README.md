# CK CRM Leads Package

A Laravel package for managing leads with Cal.com integration, built for Laravel 10+ applications.

## Features

- 📝 Full CRUD operations for lead management
- 🗃️ Soft delete functionality (archive instead of delete)
- 🔍 Search and filter capabilities
- 📅 Cal.com booking synchronization
- 🔐 Built-in authorization with admin-only access
- 🆔 UUID support for primary keys
- 🎨 Tailwind CSS styled views
- ⚡ Easy installation and configuration

## Requirements

- PHP 8.1+
- Laravel 10.0+
- A configured authentication system
- User model with `isAdmin()` method (or configure authorization)

## Installation

### Option 1: Install from GitHub (Private Repository)

Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/your-username/ck-crm-leads.git"
        }
    ]
}
```

Then install:

```bash
composer require ck-crm/leads:^1.0
```

### Option 2: Install from Packagist (Public Package)

Once published to Packagist:

```bash
composer require ck-crm/leads
```

### Option 3: Local Development

For local development, use the path repository:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../path/to/ck-crm-leads"
        }
    ]
}
```

Then install:

```bash
composer require ck-crm/leads:@dev
```

### Step 2: Publish Configuration

```bash
php artisan vendor:publish --tag=leads-config
```

### Step 3: Publish and Run Migrations

```bash
php artisan vendor:publish --tag=leads-migrations
php artisan migrate
```

### Step 4: (Optional) Publish Views

If you want to customize the views:

```bash
php artisan vendor:publish --tag=leads-views
```

## Configuration

After publishing the config file, you can customize various aspects of the package in `config/leads.php`:

### Routes Configuration

```php
'routes' => [
    'prefix' => 'leads',              // URL prefix for all routes
    'middleware' => ['web', 'auth', 'admin'], // Middleware stack
    'as' => 'leads.',                 // Route name prefix
],
```

### Features

```php
'features' => [
    'archive' => true,        // Enable soft delete/archive functionality
    'bulk_actions' => true,   // Enable bulk operations
    'export' => false,        // Future feature
    'import' => false,        // Future feature
],
```

### Cal.com Integration

```php
'calcom' => [
    'enabled' => env('LEADS_CALCOM_ENABLED', false),
    'api_key' => env('CALCOM_API_KEY'),
    'sync_enabled' => env('LEADS_CALCOM_SYNC_ENABLED', false),
    'sync_days' => env('LEADS_CALCOM_SYNC_DAYS', 30),
],
```

Add these to your `.env` file:

```env
LEADS_CALCOM_ENABLED=true
CALCOM_API_KEY=your_calcom_api_key_here
LEADS_CALCOM_SYNC_ENABLED=true
LEADS_CALCOM_SYNC_DAYS=30
```

### Authorization

```php
'authorization' => [
    'enabled' => true,
    'admin_only' => true,
],
```

## Usage

### Basic Usage

Once installed, the leads management interface will be available at `/leads` (or your configured prefix).

### Navigation Integration

Add a link to your navigation:

```blade
@if(auth()->user()->isAdmin())
    <a href="{{ route('leads.index') }}">Leads</a>
@endif
```

### Cal.com Sync

To manually sync Cal.com bookings:

```bash
php artisan leads:sync-calcom
```

To sync the last 60 days:

```bash
php artisan leads:sync-calcom --days=60
```

The sync will automatically run every 15 minutes if `LEADS_CALCOM_SYNC_ENABLED=true`.

### Using the Lead Model

```php
use CkCrm\Leads\Models\Lead;

// Create a lead
$lead = Lead::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1234567890',
    'company' => 'Acme Inc',
    'notes' => 'Interested in enterprise plan',
]);

// Archive a lead
$lead->archive();

// Query leads (archived are excluded by default)
$leads = Lead::where('company', 'like', '%Acme%')->get();

// Include archived leads
$allLeads = Lead::withArchived()->get();

// Only archived leads
$archivedLeads = Lead::onlyArchived()->get();
```

## Customization

### Custom Layout

By default, the package uses `x-app-layout`. To use a different layout:

```php
// config/leads.php
'views' => [
    'layout' => 'layouts.admin', // Your custom layout
    'styles' => 'tailwind',       // Currently only tailwind is supported
],
```

### Extending the Lead Model

Create your own Lead model that extends the package model:

```php
namespace App\Models;

use CkCrm\Leads\Models\Lead as BaseLeadModel;

class Lead extends BaseLeadModel
{
    // Add your custom methods and properties
}
```

### Custom Authorization

If your User model doesn't have an `isAdmin()` method, you can:

1. Disable authorization in config:
```php
'authorization' => [
    'enabled' => false,
],
```

2. Or add the method to your User model:
```php
public function isAdmin(): bool
{
    return $this->role === 'admin';
}
```

## Testing

Run the package tests:

```bash
composer test
```

## Troubleshooting

### Migration Issues with UUIDs

If you're upgrading from integer IDs to UUIDs, ensure your existing data is properly migrated. The package includes UUID support by default.

### Cal.com Sync Not Working

1. Verify your API key is correct
2. Check Laravel logs for error messages
3. Ensure the scheduler is running: `php artisan schedule:work`

### Routes Not Found

Clear route cache after installation:

```bash
php artisan route:clear
php artisan route:cache
```

## License

This package is open-sourced software licensed under the MIT license.

## Support

For issues and feature requests, please use the GitHub issue tracker.