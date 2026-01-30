# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TheCrossLove is a Symfony 7.3 event management platform built with PHP 8.2+, Doctrine ORM, and MySQL 8.0. Users can create events, register for events, and manage their participation. Admins have access to a dashboard for managing events, categories, and participants.

## Common Commands

```bash
# Install dependencies
composer install

# Database setup
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --no-interaction

# Start development server
symfony server:start
# Or: php -S localhost:8000 -t public/

# Run all tests
php bin/phpunit

# Run a single test file
php bin/phpunit tests/Unit/Entity/EventTest.php

# Run tests with coverage report
php bin/phpunit --coverage-html=var/coverage

# Generate code
php bin/console make:entity
php bin/console make:migration
php bin/console make:controller

# Clear cache
php bin/console cache:clear

# Docker (development)
docker-compose up -d    # Start MySQL + MailCatcher
docker-compose down
```

## Architecture

### MVC Structure
- **Entities** (`src/Entity/`): Doctrine ORM models with validation constraints
- **Controllers** (`src/Controller/`): HTTP request handlers
- **Repositories** (`src/Repository/`): Database queries via Doctrine
- **Forms** (`src/Form/`): Symfony Form Types for data binding
- **Templates** (`templates/`): Twig views with `base.html.twig` as layout

### Core Entities

**Event**: Central entity for event management
- Relationships: `createdBy` (User), `category` (Category), `registrations` (ToRegister[])
- Key methods: `isUpcoming()`, `isPast()`, `isFull()`, `canRegister()`, `isUserRegistered(User)`
- Slug auto-generated from title

**User**: Authentication with role-based access
- Roles: `ROLE_USER` (default), `ROLE_ADMIN`
- Key method: `hasRole(string)`, `isAdmin()`

**ToRegister**: Event registration junction table
- Unique constraint on (user, event)
- Tracks: registration status, WhatsApp number, GPS coordinates

**Category**: Event classification with auto-generated slugs

### Security Configuration

Form-based authentication at `/login`. Access control:
- `/admin/*` requires `ROLE_ADMIN`
- `/login`, `/register` are public
- Most routes require `ROLE_USER`

### Database Conventions

- ID columns named `id_<entity>` (e.g., `id_user`, `id_event`)
- MySQL 8.0 with UTF8MB4 encoding
- Test database: `thecrosslove_test`

## Testing

Tests are organized in `tests/`:
- `Unit/`: Entity and repository logic tests
- `Integration/Controller/`: Full HTTP request/response tests

Use `DatabaseTestTrait` for test isolation. The test environment uses a separate database configured in `.env.test`.

## Docker Services

Development (`compose.yaml`):
- MySQL 8.0 on port 3306
- MailCatcher: SMTP on 1025, Web UI on 1080

Production (`docker-compose.prod.yml`):
- Multi-container with PHP/Nginx, MySQL, Redis, Traefik
- Auto HTTPS via Let's Encrypt
- Health checks and resource limits

## CI/CD Pipeline

GitHub Actions workflow (`.github/ci.yml`):
1. Code quality checks (composer audit, validation)
2. Tests with coverage (PHP 8.2, 8.3 matrix)
3. Asset build
4. Docker image push
5. Auto-deploy: `develop` → staging, `main` → production

## File Upload

Event images stored in `public/uploads/events/`. Handle via Symfony's file upload system in forms.
