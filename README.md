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

## Production Deployment

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Configure a production database (Supabase Cloud recommended)
3. Set up a real email service (Resend API key)
4. Use a proper web server (Nginx/Apache)
5. Enable HTTPS with SSL certificates
6. Set up monitoring and backups

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