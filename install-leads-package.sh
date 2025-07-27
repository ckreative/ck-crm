#!/bin/bash

echo "Installing CK CRM Leads Package..."

# Install the package
echo "1. Installing package via composer..."
composer require ck-crm/leads:@dev

# Publish config
echo "2. Publishing configuration..."
php artisan vendor:publish --tag=leads-config

# Publish migrations
echo "3. Publishing migrations..."
php artisan vendor:publish --tag=leads-migrations

# Run migrations
echo "4. Running migrations..."
php artisan migrate

# Clear caches
echo "5. Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo ""
echo "✅ CK CRM Leads Package installed successfully!"
echo ""
echo "Next steps:"
echo "1. Configure Cal.com integration in your .env file:"
echo "   LEADS_CALCOM_ENABLED=true"
echo "   CALCOM_API_KEY=your_api_key_here"
echo "   LEADS_CALCOM_SYNC_ENABLED=true"
echo ""
echo "2. Visit /leads to access the leads management interface"
echo ""
echo "3. (Optional) Publish views for customization:"
echo "   php artisan vendor:publish --tag=leads-views"