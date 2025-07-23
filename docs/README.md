# Project Documentation

## Overview

This Laravel application uses Supabase as its PostgreSQL database provider and includes a custom authentication system with admin-only access control.

## Architecture

- **Framework**: Laravel 12.x
- **Database**: PostgreSQL (via Supabase)
- **Authentication**: Laravel Breeze (customized)
- **Frontend**: Blade templates with Tailwind CSS
- **Development Environment**: Docker

## Features

### Implemented
- [x] Docker development environment
- [x] Laravel Breeze authentication
- [x] Admin-only system (no public registration)
- [x] Role-based user system

### Planned
- [ ] [User Invitation System](./features/user-invitation.md)
- [ ] App Settings Management
- [ ] Audit Logging

## Key Concepts

### Authentication
- Public registration is disabled
- Users can only be created through invitations
- All users currently have 'admin' role
- Sessions stored in database

### Database Schema
- Uses custom 'laravel' schema in PostgreSQL
- Migrations managed by Laravel
- Connection configured for Supabase

### Security
- All routes protected by authentication
- Admin middleware for administrative functions
- CSRF protection enabled
- Password hashing via bcrypt

## Development Guidelines

### Code Style
- Follow PSR-12 standards
- Use Laravel conventions
- Meaningful variable and method names
- Comment complex logic

### Git Workflow
- Feature branches for new development
- Pull requests for code review
- Semantic commit messages

### Testing
- Feature tests for user flows
- Unit tests for business logic
- Browser testing for critical paths

## Getting Started

See the main [README.md](../README.md) for setup instructions.