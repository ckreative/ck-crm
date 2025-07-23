#!/bin/bash

# Laravel Admin Template - Setup Script
# This script helps set up a new project from the template

set -e

echo "🚀 Laravel Admin Template Setup"
echo "=============================="
echo ""

# Check prerequisites
echo "📋 Checking prerequisites..."

# Check PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.2 or higher."
    exit 1
fi

# Check Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer."
    exit 1
fi

# Check Node
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js."
    exit 1
fi

# Check Docker
if ! command -v docker &> /dev/null; then
    echo "⚠️  Docker is not installed. Docker is optional but recommended."
fi

echo "✅ Prerequisites check passed!"
echo ""

# Get project information
echo "📝 Project Configuration"
echo "----------------------"
read -p "Enter your project name [Laravel Admin]: " project_name
project_name=${project_name:-"Laravel Admin"}

read -p "Enter your project URL [http://localhost:8000]: " app_url
app_url=${app_url:-"http://localhost:8000"}

# Install dependencies
echo ""
echo "📦 Installing dependencies..."
composer install --no-interaction
npm install

# Setup environment file
echo ""
echo "⚙️  Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Created .env file from .env.example"
else
    echo "ℹ️  .env file already exists, skipping..."
fi

# Generate app key
php artisan key:generate

# Update .env with project name and URL
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    sed -i '' "s|APP_NAME=.*|APP_NAME=\"$project_name\"|" .env
    sed -i '' "s|APP_URL=.*|APP_URL=$app_url|" .env
else
    # Linux
    sed -i "s|APP_NAME=.*|APP_NAME=\"$project_name\"|" .env
    sed -i "s|APP_URL=.*|APP_URL=$app_url|" .env
fi

# Database setup
echo ""
echo "🗄️  Database Configuration"
echo "------------------------"
echo "Choose your database setup:"
echo "1) Local Supabase (recommended for development)"
echo "2) Supabase Cloud"
echo "3) Skip database setup"
read -p "Enter your choice [1]: " db_choice
db_choice=${db_choice:-"1"}

case $db_choice in
    1)
        echo ""
        echo "📡 Setting up local Supabase..."
        if command -v supabase &> /dev/null; then
            echo "Starting Supabase..."
            supabase start
            echo ""
            echo "✅ Local Supabase is running!"
            echo "ℹ️  Database credentials are already configured in .env"
        else
            echo "❌ Supabase CLI is not installed."
            echo "Install it from: https://supabase.com/docs/guides/cli"
            echo "Then run: supabase start"
        fi
        ;;
    2)
        echo ""
        echo "☁️  Supabase Cloud Configuration"
        echo "Get your credentials from: https://app.supabase.com/project/_/settings/database"
        echo ""
        read -p "Enter your Supabase host: " db_host
        read -p "Enter your Supabase password: " db_password
        
        if [[ "$OSTYPE" == "darwin"* ]]; then
            # macOS
            sed -i '' "s|DB_HOST=.*|DB_HOST=$db_host|" .env
            sed -i '' "s|DB_PORT=.*|DB_PORT=5432|" .env
            sed -i '' "s|DB_PASSWORD=.*|DB_PASSWORD=$db_password|" .env
        else
            # Linux
            sed -i "s|DB_HOST=.*|DB_HOST=$db_host|" .env
            sed -i "s|DB_PORT=.*|DB_PORT=5432|" .env
            sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$db_password|" .env
        fi
        echo "✅ Supabase Cloud configured!"
        ;;
    3)
        echo "⏭️  Skipping database setup..."
        ;;
esac

# Email configuration
echo ""
echo "📧 Email Configuration"
echo "--------------------"
read -p "Do you want to configure Resend for emails? (y/N): " configure_email
if [[ $configure_email =~ ^[Yy]$ ]]; then
    read -p "Enter your Resend API key: " resend_key
    read -p "Enter your from email address: " from_email
    
    if [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        sed -i '' "s|RESEND_API_KEY=.*|RESEND_API_KEY=$resend_key|" .env
        sed -i '' "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"$from_email\"|" .env
    else
        # Linux
        sed -i "s|RESEND_API_KEY=.*|RESEND_API_KEY=$resend_key|" .env
        sed -i "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"$from_email\"|" .env
    fi
    echo "✅ Email configured!"
else
    echo "⏭️  Skipping email configuration..."
fi

# Run migrations
if [[ $db_choice != "3" ]]; then
    echo ""
    echo "🔧 Running database migrations..."
    read -p "Run database migrations now? (Y/n): " run_migrations
    if [[ ! $run_migrations =~ ^[Nn]$ ]]; then
        php artisan migrate
        echo "✅ Migrations completed!"
        
        echo ""
        read -p "Seed the database with default admin user? (Y/n): " seed_db
        if [[ ! $seed_db =~ ^[Nn]$ ]]; then
            php artisan db:seed
            echo "✅ Database seeded!"
            echo ""
            echo "📧 Default admin credentials:"
            echo "   Email: admin@example.com"
            echo "   Password: password"
            echo "   ⚠️  Change these after first login!"
        fi
    fi
fi

# Build assets
echo ""
echo "🎨 Building assets..."
npm run build
echo "✅ Assets built!"

# Final steps
echo ""
echo "🎉 Setup Complete!"
echo "=================="
echo ""
echo "Next steps:"
echo ""

if command -v docker &> /dev/null; then
    echo "1. Start the application with Docker:"
    echo "   docker-compose up -d"
    echo "   Visit: http://localhost:8080"
    echo ""
    echo "   OR"
    echo ""
fi

echo "1. Start the application with Laravel:"
echo "   php artisan serve"
echo "   Visit: $app_url"
echo ""
echo "2. In a new terminal, start Vite for development:"
echo "   npm run dev"
echo ""

if [[ $db_choice == "3" ]]; then
    echo "3. Configure your database in .env and run:"
    echo "   php artisan migrate"
    echo "   php artisan db:seed"
    echo ""
fi

echo "📚 Documentation:"
echo "   - Setup Guide: SETUP.md"
echo "   - Email Config: docs/email-configuration.md"
echo "   - User Invitations: docs/features/user-invitation.md"
echo ""
echo "Happy coding! 🚀"