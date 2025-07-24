# Distribution Guide for CK CRM Leads Package

This guide explains how to distribute and use the CK CRM Leads package in other Laravel projects.

## Setting Up the Package Repository

### 1. Create a Standalone Repository

First, copy the package to its own directory and initialize Git:

```bash
# Copy package to a new location
cp -r packages/ck-crm-leads ~/Development/packages/ck-crm-leads

# Navigate to the package
cd ~/Development/packages/ck-crm-leads

# Initialize Git repository
git init
git add .
git commit -m "Initial commit of CK CRM Leads package"

# Create repository on GitHub and push
git remote add origin https://github.com/your-username/ck-crm-leads.git
git branch -M main
git push -u origin main

# Create initial tag
git tag v1.0.0
git push origin v1.0.0
```

### 2. Directory Structure for Multiple Projects

Recommended structure:

```
~/Development/
├── packages/
│   ├── ck-crm-leads/           # Your package repository
│   └── other-packages/         # Other packages
└── projects/
    ├── project1/               # Laravel project 1
    ├── project2/               # Laravel project 2
    └── project3/               # Laravel project 3
```

## Installing in Other Projects

### Method 1: GitHub Repository (Recommended)

In your Laravel project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/your-username/ck-crm-leads.git"
        }
    ],
    "require": {
        "ck-crm/leads": "^1.0"
    }
}
```

Then run:

```bash
composer update
php artisan vendor:publish --tag=leads-config
php artisan vendor:publish --tag=leads-migrations
php artisan migrate
```

### Method 2: Local Development (Symlink)

For active development across multiple local projects:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../../packages/ck-crm-leads",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "ck-crm/leads": "@dev"
    }
}
```

### Method 3: Private Packagist

For team/company use:

1. Sign up for [Private Packagist](https://packagist.com)
2. Add your package repository
3. Configure in `composer.json`:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://your-company.repo.packagist.com/your-username/"
        }
    ],
    "require": {
        "ck-crm/leads": "^1.0"
    }
}
```

### Method 4: GitLab Package Registry

For GitLab users:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://gitlab.com/api/v4/group/YOUR_GROUP_ID/-/packages/composer/packages.json"
        }
    ],
    "require": {
        "ck-crm/leads": "^1.0"
    }
}
```

## Version Management

### Semantic Versioning

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** version (1.0.0 → 2.0.0): Breaking changes
- **MINOR** version (1.0.0 → 1.1.0): New features, backward compatible
- **PATCH** version (1.0.0 → 1.0.1): Bug fixes

### Creating a New Release

```bash
# Update CHANGELOG.md with changes
# Update version in composer.json if needed

# Commit changes
git add .
git commit -m "Prepare release v1.1.0"

# Create and push tag
git tag v1.1.0
git push origin main
git push origin v1.1.0

# Create GitHub release
# Go to GitHub → Releases → Create new release
# Select the tag and add release notes
```

## Publishing to Packagist (Optional)

For public packages:

1. Create account at [packagist.org](https://packagist.org)
2. Click "Submit"
3. Enter your GitHub repository URL
4. Packagist will auto-update on new tags

## Environment Configuration

Each project needs these `.env` variables:

```env
# Cal.com Integration (optional)
LEADS_CALCOM_ENABLED=true
CALCOM_API_KEY=your_api_key_here
LEADS_CALCOM_SYNC_ENABLED=true
LEADS_CALCOM_SYNC_DAYS=30
```

## Troubleshooting

### Package Not Found

```bash
# Clear Composer cache
composer clear-cache

# Update with verbose output
composer update -vvv
```

### Changes Not Reflected

For local development with symlinks:

```bash
# In the Laravel project
composer dump-autoload
php artisan cache:clear
php artisan config:clear
```

### Permission Issues

```bash
# Ensure proper permissions
chmod -R 755 vendor/ck-crm/leads
```

## Best Practices

1. **Always tag releases** - Makes version constraints work properly
2. **Update CHANGELOG.md** - Document all changes
3. **Test in isolation** - Test package in a fresh Laravel installation
4. **Use CI/CD** - Set up GitHub Actions for automated testing
5. **Document breaking changes** - Clear upgrade guides for major versions

## Example GitHub Actions Workflow

Create `.github/workflows/tests.yml` in your package:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php: [8.1, 8.2, 8.3]
        laravel: [10.*, 11.*]
        
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
      - name: Install dependencies
        run: |
          composer require "laravel/framework:${{ matrix.laravel }}" --no-interaction --no-update
          composer update --prefer-dist --no-interaction
      - name: Run tests
        run: vendor/bin/phpunit
```

## Support

For package-specific issues, use GitHub Issues on the package repository.
For integration help, refer to the package README and configuration options.