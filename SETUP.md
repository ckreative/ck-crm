# Laravel Admin Template - Setup Guide

This guide will help you set up a new Laravel admin project using this template.

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- Docker and Docker Compose
- Supabase CLI (for local development) or a Supabase Cloud account

## Quick Start

### 1. Create a new project from template

If using GitHub template:
```bash
# Click "Use this template" button on GitHub
# Clone your new repository
git clone https://github.com/yourusername/your-new-project.git
cd your-new-project
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure your environment

Edit `.env` file and update the following:

#### Application settings
```env
APP_NAME="Your App Name"
APP_URL=http://localhost:8000
```

#### Database (Local Supabase)
```bash
# Start local Supabase
supabase start

# Use the credentials shown in the output
DB_HOST=127.0.0.1
DB_PORT=54322
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=postgres
DB_SCHEMA=laravel
```

#### Database (Supabase Cloud)
```env
# Get these from your Supabase project settings
DB_HOST=db.xxxxxxxxxxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-supabase-password
DB_SCHEMA=laravel
```

#### Email (Resend)
```env
MAIL_MAILER=resend
RESEND_API_KEY=your-resend-api-key
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
```

### 5. Database setup

```bash
# Run migrations
php artisan migrate

# Seed the database with initial admin user
php artisan db:seed
```

**Default admin credentials:**
- Email: `admin@example.com`
- Password: `password`

⚠️ **Important:** Change these credentials immediately after first login!

### 6. Build assets

```bash
npm run build
# or for development
npm run dev
```

### 7. Start the application

#### Using Docker (recommended)
```bash
docker-compose up -d
```
Access the application at: http://localhost:8080

#### Using Laravel's built-in server
```bash
php artisan serve
```
Access the application at: http://localhost:8000

## Docker Development

The template includes a complete Docker setup with:
- PHP 8.4-FPM with all required extensions
- Nginx web server
- Redis for caching
- Xdebug for debugging

### Docker commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Run artisan commands
./bin/artisan migrate
./bin/artisan db:seed

# Run composer commands
./bin/composer install

# Run npm commands
./bin/npm install
```

## Customization

### 1. Update branding

- Replace the logo in views: `resources/views/components/application-logo.blade.php`
- Update app name in `.env`
- Update email templates in `resources/views/emails/`

### 2. Modify admin user

Edit `database/seeders/DatabaseSeeder.php` to change the default admin user before running seeders.

### 3. Configure authentication

- Session lifetime is set to 30 days by default (`SESSION_LIFETIME=43200`)
- Public registration is disabled by default
- Users can only be added through invitations

### 4. Email configuration

The template uses Resend for email delivery. To use a different service:
1. Update `MAIL_MAILER` in `.env`
2. Configure the appropriate mail driver settings
3. Remove Resend-specific code if not needed

## Features

- **Admin-only system**: No public registration
- **Invitation system**: Admins can invite other admins
- **User management**: View users and manage invitations
- **Email integration**: Ready-to-use with Resend
- **Docker ready**: Complete development environment
- **Supabase integration**: Works with local or cloud Supabase
- **Extended sessions**: 30-day session lifetime
- **Clean UI**: Card-based authentication pages with Tailwind CSS

## Troubleshooting

### Database connection issues

If using Docker and local Supabase:
- Use `host.docker.internal` instead of `127.0.0.1` in `.env`
- Ensure Supabase is running: `supabase status`

### Email not sending

- Verify your Resend API key is correct
- Check the from address is verified in Resend
- Review Laravel logs: `tail -f storage/logs/laravel.log`

### Docker issues

- Ensure Docker is running
- Check port conflicts (8080, 6379)
- Review Docker logs: `docker-compose logs -f`

## Security Notes

1. **Change default credentials immediately**
2. **Keep your `.env` file secure and never commit it**
3. **Use strong passwords for all accounts**
4. **Enable HTTPS in production**
5. **Keep dependencies updated**

## Next Steps

1. Change the default admin credentials
2. Configure your production environment
3. Set up SSL certificates
4. Configure backup strategies
5. Set up monitoring and logging
6. Customize the UI to match your brand

## Support

For issues specific to this template, please check:
- The project's issue tracker
- Laravel documentation: https://laravel.com/docs
- Supabase documentation: https://supabase.com/docs