# Using Packages from CK CRM Monorepo

This repository serves as a monorepo for CK CRM packages. Here's how to use these packages in other internal projects.

## Available Packages

- **ck-crm/leads** - Lead management package with Cal.com integration

## Installation in Other Projects

To use packages from this monorepo in your other Laravel projects, you need to add a custom repository configuration to your `composer.json`.

### Method 1: Direct Package Definition (Recommended for Internal Use)

Add this to your project's `composer.json`:

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
    ],
    "require": {
        "ck-crm/leads": "dev-main"
    }
}
```

Then run:
```bash
composer update ck-crm/leads
```

### Method 2: Using a Specific Commit

To lock to a specific version, change the reference:

```json
"reference": "43742b4"  // Use a specific commit hash
```

### Method 3: Private Repository Access

If the repository is private, configure authentication:

```bash
composer config --global github-oauth.github.com YOUR_GITHUB_TOKEN
```

Or add to your project's `composer.json`:

```json
{
    "config": {
        "github-oauth": {
            "github.com": "YOUR_GITHUB_TOKEN"
        }
    }
}
```

## Configuration After Installation

After installing the package:

1. **Publish the configuration:**
   ```bash
   php artisan vendor:publish --tag=leads-config
   ```

2. **Run migrations:**
   ```bash
   php artisan migrate
   ```

3. **Configure Cal.com integration:**
   - Add your Cal.com API key to `.env`:
     ```
     CALCOM_API_KEY=your_api_key_here
     ```

## Updating Packages

To get the latest version:

```bash
composer update ck-crm/leads
```

To update to a specific commit:
1. Update the `reference` in your `composer.json`
2. Run `composer update ck-crm/leads`

## Troubleshooting

### Package Not Found
- Ensure the repository URL is correct
- Check that you have access to the repository
- Verify the package path in the autoload configuration

### Authentication Issues
- Make sure your GitHub token has `repo` scope
- For private repositories, ensure proper authentication is configured

### Version Conflicts
- Check that your Laravel version matches the package requirements
- Review the package's `composer.json` for dependency constraints

## Package Development

If you need to modify a package:
1. Clone this entire repository
2. Make changes in the `packages/` directory
3. Submit a pull request with your changes

## Support

For issues or questions about packages, please open an issue in the main CK CRM repository.