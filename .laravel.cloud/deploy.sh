#!/bin/bash

# Clear all caches
php artisan optimize:clear

# Rediscover packages
php artisan package:discover --ansi

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Run migrations
php artisan migrate --force