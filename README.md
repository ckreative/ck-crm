# Laravel Admin Template

A modern Laravel admin panel starter template with Supabase integration, Docker support, and invitation-only authentication.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## Features

- 🔐 **Admin-only authentication** - No public registration, invitation-only system
- 🐘 **Supabase PostgreSQL** - Works with local or cloud Supabase instances
- 🐳 **Docker ready** - Complete development environment with PHP 8.4, Nginx, and Redis
- 📧 **Email integration** - Pre-configured with Resend for transactional emails
- 👥 **User management** - Invite users, manage permissions, track activity
- 🎨 **Modern UI** - Clean, responsive design with Tailwind CSS and Alpine.js
- 🔑 **Extended sessions** - 30-day session lifetime for better UX
- 🛡️ **Security focused** - CSRF protection, secure authentication, prepared statements
- 🖼️ **Image management** - Logo uploads with automatic transformations and CDN support

## Quick Start

### Using GitHub Template

1. Click the "Use this template" button above
2. Create a new repository
3. Clone your new repository
4. Follow the setup instructions in [SETUP.md](SETUP.md)

### Manual Setup

```bash
# Clone the repository
git clone https://github.com/yourusername/laravel-admin-template.git
cd laravel-admin-template

# Run the setup script
./init.sh

# Or follow manual setup in SETUP.md
```

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and NPM  
- Docker and Docker Compose
- Supabase CLI (for local development) or Supabase Cloud account

## Documentation

- [Setup Guide](SETUP.md) - Detailed installation and configuration
- [Email Configuration](docs/email-configuration.md) - Setting up Resend or other email providers
- [User Invitation System](docs/features/user-invitation.md) - How the invitation system works
- [Customization Guide](docs/customization.md) - Adapting the template for your needs

## Email Configuration (Resend)

The application uses Resend for sending transactional emails (invitations, notifications, etc.). Here's how to set it up:

### Development Setup

For local development, you can use the `log` driver to write emails to your log file instead of sending them:

```bash
MAIL_MAILER=log
```

Emails will be written to `storage/logs/laravel.log`.

### Production Setup with Resend

1. **Sign up for Resend**
   - Go to [https://resend.com](https://resend.com)
   - Create a free account (3,000 emails/month free tier)

2. **Get your API key**
   - In the Resend dashboard, go to API Keys
   - Create a new API key
   - Copy the key (it starts with `re_`)

3. **Update your `.env` file**
   ```bash
   MAIL_MAILER=resend
   RESEND_API_KEY=re_xxxxxxxxxxxxxxxxxx  # Your actual API key
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

4. **Verify your domain (optional but recommended)**
   - In Resend dashboard, add your domain
   - Add the DNS records as instructed
   - This improves email deliverability

### Important Notes

- **Organization Creation**: You cannot create organizations with new user emails unless email is configured (either `log` driver or valid Resend API key)
- **Existing Users**: You can always add existing users as organization owners without email configuration
- **Error Handling**: The system will show a clear error if you try to invite users without proper email configuration

## Default Credentials

After seeding the database:
- **Email**: admin@example.com
- **Password**: password

⚠️ **Change these immediately after first login!**

## Project Structure

```
├── app/                    # Application code
│   ├── Http/Controllers/   # Controllers including auth and user management
│   ├── Mail/              # Email templates and transports
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── database/              # Migrations and seeders
├── docker/                # Docker configuration
├── resources/             # Views, CSS, and JavaScript
│   └── views/
│       ├── app-settings/  # Admin panel views
│       ├── auth/          # Authentication views
│       └── components/    # Blade components
├── routes/                # Application routes
├── docker-compose.yml     # Docker services configuration
└── CLAUDE.md             # AI assistant guidelines
```

## Customization Points

1. **Branding**
   - Logo: `resources/views/components/application-logo.blade.php`
   - App name: `.env` file
   - Colors: Tailwind config

2. **Authentication**
   - Session lifetime: `SESSION_LIFETIME` in `.env`
   - Password requirements: `app/Http/Controllers/Auth/RegisteredUserController.php`
   - Add roles: Extend the user model and middleware

3. **Email**
   - Templates: `resources/views/emails/`
   - From address: `MAIL_FROM_ADDRESS` in `.env`
   - Provider: Configure in `config/mail.php`

## Development

### Docker Commands

```bash
# Start development environment
docker-compose up -d

# Run artisan commands
./bin/artisan migrate
./bin/artisan db:seed

# Run composer
./bin/composer install

# Run npm
./bin/npm run dev
```

### Without Docker

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite
npm run dev
```

## Testing

```bash
# Run PHP tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## Storage Configuration

The application supports multiple storage backends for file uploads (logos, images, etc.) with automatic image transformations.

### Local Development

By default, files are stored locally with on-the-fly transformations:

```bash
# Default configuration in .env
FILESYSTEM_DISK=public

# Files are stored in storage/app/public
# Access via: http://localhost:8000/storage/logos/filename.jpg
# Transformations: http://localhost:8000/image-transform/width=200,height=200/logos/filename.jpg
```

### Production with Cloudflare R2

For production, we recommend using Cloudflare R2 for storage with automatic CDN and image transformations:

1. **Create R2 Bucket**
   - Sign up for Cloudflare and create an R2 bucket
   - Enable public access for the bucket
   - Note your bucket name and account ID

2. **Configure Laravel Cloud / Production Environment**
   ```bash
   # Update .env for production
   FILESYSTEM_DISK=r2
   
   # R2 Configuration (Laravel Cloud provides these automatically)
   AWS_ACCESS_KEY_ID=your_r2_access_key
   AWS_SECRET_ACCESS_KEY=your_r2_secret_key
   AWS_DEFAULT_REGION=auto
   AWS_BUCKET=your_bucket_name
   AWS_URL=https://pub-xxxxx.r2.dev  # Your R2 public URL
   AWS_ENDPOINT=https://account_id.r2.cloudflarestorage.com
   ```

3. **Enable Cloudflare Image Transformations**
   - In Cloudflare dashboard, go to Images → Transformations
   - Enable "Transform images on your zone"
   - The app will automatically generate transformation URLs like:
     ```
     https://yourdomain.com/cdn-cgi/image/width=200,height=200,quality=85/logos/filename.jpg
     ```

### Image Transformation Options

The application supports various transformation options:

- **Dimensions**: `width`, `height`
- **Quality**: `quality` (1-100)
- **Format**: `format` (auto, webp, jpg, png)
- **Fit**: `fit` (cover, contain, fill, inside, outside)

Example URLs:
```bash
# Local development
/image-transform/width=48,height=48/logos/company.png

# Production with R2/Cloudflare
https://yourdomain.com/cdn-cgi/image/width=48,height=48,format=webp/logos/company.png
```

### Storage Best Practices

1. **File Size Limits**: Set appropriate upload limits (default: 2MB for logos)
2. **Allowed Formats**: JPG, PNG, SVG for logos
3. **Naming Convention**: Files are automatically renamed with timestamps to prevent conflicts
4. **CDN Caching**: Transformed images are cached at the CDN edge for performance

## Production Deployment

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Configure a production database (Supabase Cloud recommended)
3. Set up a real email service (Resend API key)
4. Configure storage (Cloudflare R2 recommended)
5. Use a proper web server (Nginx/Apache)
6. Enable HTTPS with SSL certificates
7. Set up monitoring and backups

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## Security

- Never commit `.env` files
- Keep dependencies updated
- Use strong passwords
- Enable HTTPS in production
- Regular security audits

## License

This template is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

Built with:
- [Laravel](https://laravel.com) - The PHP Framework
- [Supabase](https://supabase.com) - Open source Firebase alternative
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript framework
- [Resend](https://resend.com) - Email API for developers

---

## Support

For issues and questions:
- Create an issue in the GitHub repository
- Check Laravel documentation: https://laravel.com/docs
- Check Supabase documentation: https://supabase.com/docs