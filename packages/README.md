# CK CRM Packages

This directory contains all the packages for the CK CRM monorepo.

## Structure

Each package is in its own subdirectory:
- `leads/` - Lead management package with Cal.com integration

## Development

Packages are automatically discovered by Composer using the path repository configuration in the root `composer.json`.

To add a new package:
1. Create a new directory under `packages/`
2. Add a `composer.json` file with the package configuration
3. Run `composer update` in the root directory

## Testing

Each package has its own test suite. To run tests for a specific package:
```bash
cd packages/leads
composer test
```

## Distribution

For production deployment, packages are included directly with the application.
For external use, packages can be split into separate repositories using git subtree.