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

---

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@thecrosslove.com` | `admin123` |
| **User** | `john.doe@example.com` | `password123` |

## Quick Demo Setup

```bash
# One-liner to prepare demo environment
docker-compose up -d && \
php bin/console doctrine:database:create --if-not-exists && \
php bin/console doctrine:migrations:migrate --no-interaction && \
php bin/console doctrine:fixtures:load --no-interaction && \
php bin/console cache:clear && \
symfony server:start
```

## Verification Checklist

After any code change, verify:
- [ ] `php bin/phpunit` - All tests pass
- [ ] `php bin/console lint:container` - Container valid
- [ ] `php bin/console doctrine:schema:validate` - DB schema sync
- [ ] `curl localhost:8000/health` - Health check OK

## Common Mistakes to Avoid

- **Doctrine Relations**: Always use `cascade: ['persist']` on OneToMany when needed
- **Unique Constraints**: Check ToRegister (user+event) before INSERT
- **Form CSRF**: Never disable CSRF protection in forms
- **Slugs**: Let Gedmo/Sluggable auto-generate, don't set manually
- **Passwords**: Always use `UserPasswordHasherInterface`, never plain text
- **maxParticipants null**: Means unlimited, not zero - always check with `=== null`
- **Date validation**: `dateStart > now` only validated on create, not update
- **Cascade remove**: Event deletion cascades to ToRegister automatically

## Learning from Errors

Document errors here after each bug fix to avoid repeating them.

### Doctrine Errors
- **LazyInitializationException**: Always fetch relations in DQL or use `fetch: EAGER`
- **UniqueConstraintViolation on ToRegister**: Check `isUserRegistered()` before persist
- **Schema out of sync**: Run `doctrine:migrations:migrate` after entity changes

### Form Errors
- **Invalid CSRF token**: Never disable CSRF, clear cache if persistent
- **Form not submitted**: Check `$form->handleRequest($request)` is called
- **Validation groups**: Use groups for conditional validation (create vs update)

### Security Errors
- **Access denied on admin routes**: Verify user has ROLE_ADMIN, not just ROLE_USER
- **Session lost after redirect**: Check cookie domain in production
- **Password not matching**: Always use `UserPasswordHasherInterface` for comparison

### Template Errors
- **Undefined variable in Twig**: Pass all required variables from controller
- **Asset not found**: Run `assets:install` and check `public/` directory
- **Translation missing**: Check `translations/` files and locale config

## Key Routes Reference

| Route | URL | Access |
|-------|-----|--------|
| Home | `/` | Public |
| Events List | `/event/` | Public |
| Event Detail | `/event/{id}` | Public |
| Login | `/login` | Public |
| Register | `/register` | Public |
| My Registrations | `/my-registrations` | ROLE_USER |
| Admin Dashboard | `/admin/` | ROLE_ADMIN |
| Admin Events | `/admin/events` | ROLE_ADMIN |
| Health Check | `/health` | Public |
