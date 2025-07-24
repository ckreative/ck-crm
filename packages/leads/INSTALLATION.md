# CK CRM Leads Package Installation

## Requirements

- PHP ^8.1
- Laravel 10.x, 11.x, or 12.x
- Cal.com API key (optional, for booking synchronization)

## Installation

### Step 1: Configure Composer

Add the following to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "ck-crm/leads",
                "version": "dev-main",
                "source": {
                    "type": "git",
                    "url": "https://github.com/ckreative/ck-crm.git",
                    "reference": "main"
                },
                "dist": {
                    "type": "zip",
                    "url": "https://github.com/ckreative/ck-crm/archive/main.zip"
                },
                "require": {
                    "php": "^8.1",
                    "illuminate/support": "^10.0|^11.0|^12.0",
                    "illuminate/database": "^10.0|^11.0|^12.0",
                    "illuminate/http": "^10.0|^11.0|^12.0"
                },
                "autoload": {
                    "psr-4": {
                        "CkCrm\\Leads\\": "packages/leads/src/"
                    }
                },
                "extra": {
                    "laravel": {
                        "providers": [
                            "CkCrm\\Leads\\LeadsServiceProvider"
                        ]
                    }
                }
            }
        }
    ]
}
```

### Step 2: Require the Package

```bash
composer require ck-crm/leads:dev-main
```

### Step 3: Publish Configuration

```bash
php artisan vendor:publish --tag=leads-config
```

This will create `config/leads.php` where you can customize the package settings.

### Step 4: Run Migrations

```bash
php artisan migrate
```

This will create the `leads` table in your database.

### Step 5: Configure Cal.com Integration (Optional)

Add your Cal.com API key to your `.env` file:

```env
CALCOM_API_KEY=your_cal_com_api_key_here
```

## Configuration

The `config/leads.php` file contains all configuration options:

```php
return [
    // Enable/disable Cal.com integration
    'calcom' => [
        'enabled' => env('LEADS_CALCOM_ENABLED', true),
        'api_key' => env('CALCOM_API_KEY'),
        'sync' => [
            'enabled' => env('LEADS_CALCOM_SYNC_ENABLED', true),
            'days_to_sync' => env('LEADS_CALCOM_SYNC_DAYS', 30),
        ],
    ],
    
    // Lead status options
    'statuses' => [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'proposal' => 'Proposal',
        'negotiation' => 'Negotiation',
        'closed_won' => 'Closed Won',
        'closed_lost' => 'Closed Lost',
    ],
    
    // Lead source options
    'sources' => [
        'website' => 'Website',
        'referral' => 'Referral',
        'cold_call' => 'Cold Call',
        'advertisement' => 'Advertisement',
        'cal_com' => 'Cal.com',
        'other' => 'Other',
    ],
];
```

## Usage

### Basic Usage

```php
use CkCrm\Leads\Models\Lead;

// Create a new lead
$lead = Lead::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1234567890',
    'company' => 'Acme Corp',
    'notes' => 'Interested in our services',
]);

// Find leads
$leads = Lead::where('status', 'new')->get();
```

### Routes

The package provides the following routes:

- `GET /leads` - List all leads
- `GET /leads/create` - Show create form
- `POST /leads` - Store new lead
- `GET /leads/{lead}` - Show lead details
- `GET /leads/{lead}/edit` - Show edit form
- `PUT /leads/{lead}` - Update lead
- `DELETE /leads/{lead}` - Delete lead

### Views

The package includes basic views that you can customize:

```bash
php artisan vendor:publish --tag=leads-views
```

This will publish the views to `resources/views/vendor/leads/`.

### Cal.com Synchronization

To sync Cal.com bookings as leads:

```bash
php artisan leads:sync-calcom
```

You can schedule this command in your `routes/console.php`:

```php
Schedule::command('leads:sync-calcom')->hourly();
```

## Authorization

The package includes a `LeadPolicy` for authorization. You can customize it in your `AuthServiceProvider`:

```php
use CkCrm\Leads\Models\Lead;
use CkCrm\Leads\Policies\LeadPolicy;

protected $policies = [
    Lead::class => LeadPolicy::class,
];
```

## Customization

### Extending the Lead Model

```php
namespace App\Models;

use CkCrm\Leads\Models\Lead as BaseLeadModel;

class Lead extends BaseLeadModel
{
    // Add your customizations here
}
```

### Custom Controllers

You can extend the default controller:

```php
namespace App\Http\Controllers;

use CkCrm\Leads\Http\Controllers\LeadController as BaseLeadController;

class LeadController extends BaseLeadController
{
    // Add your customizations here
}
```

## Troubleshooting

### Package Not Found
Ensure the repository configuration is correct in your `composer.json`.

### Migration Errors
Check that your database connection is properly configured.

### Cal.com Sync Not Working
- Verify your API key is correct
- Check that the Cal.com integration is enabled in config
- Review Laravel logs for any error messages

## Support

For issues or feature requests, please open an issue in the [CK CRM repository](https://github.com/ckreative/ck-crm/issues).