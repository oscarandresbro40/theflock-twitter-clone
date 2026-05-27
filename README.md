# The Flock Twitter Clone

Laravel-based Twitter/X clone challenge implementation with first-party authentication, tweet interactions, social graph features, public profile/search pages, personalized timeline behavior, and seeded demo data for immediate evaluation.

## Project Overview

This project focuses on delivering a pragmatic, test-covered Laravel application that mirrors core Twitter/X workflows:

- Account registration and authentication
- Posting and deleting tweets
- Following and unfollowing users
- Liking and unliking tweets
- Discovering users via search
- Viewing public profiles and follow lists
- Seeing a personalized timeline of relevant tweets

The app is designed so evaluators can clone, install, seed, and run it quickly with minimal local setup.

## Stack and Rationale

- Laravel 13: fast delivery using framework conventions and strong testing support.
- Blade: simple server-rendered UI, good fit for challenge scope.
- Tailwind CSS: utility-first styling for responsive views without heavy frontend overhead.
- SQLite: easiest local evaluation path, no external database server required.
- Laravel Breeze: first-party auth scaffolding for register/login/logout without third-party auth providers.

## Prerequisites

- PHP 8.3+
- Composer 2.9+
- Node 24 LTS (or compatible)
- PHP SQLite extensions enabled:
  - pdo_sqlite
  - sqlite3

Any local PHP environment is fine (for example: terminal PHP, XAMPP, WAMP, Laragon, Laravel Herd, Valet, Linux, macOS, or Windows) as long as the requirements above are installed.

## Setup Runbook

Run from the repository root.

### 1) Environment file

Windows (CMD/PowerShell):

```bash
copy .env.example .env
```

Generic alternative:

```bash
cp .env.example .env
```

In .env, ensure:

- DB_CONNECTION=sqlite
- DB_DATABASE=database/database.sqlite

Create the SQLite file if needed:

Windows:

```bash
type nul > database\database.sqlite
```

Generic:

```bash
touch database/database.sqlite
```

### 2) Install dependencies and initialize app

```bash
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

After php artisan serve, open the local URL shown in terminal.

## Windows / Laragon Notes

- Laragon is optional, not required.
- If using Laragon on Windows, use the same setup commands above from the project root.
- Ensure the active Laragon PHP version is 8.3+ with pdo_sqlite and sqlite3 enabled.

## Daily Development Commands

```bash
php artisan serve
npm run dev
```

## Test and Build Commands

```bash
php artisan test
npm run build
```

## Demo Credentials

- Email: demo@example.com
- Password: password

Seed data includes at least 10 additional users plus crossed tweets, follows, and likes so the app is populated immediately after migrate:fresh --seed.

## Completed Features

- Laravel scaffold
- Breeze authentication: register, login, logout
- SQLite database setup for local development
- Tweet creation (max 280 chars) and deletion (owner only)
- Follow and unfollow users
- Like and unlike tweets
- User search by username prefix
- Followers and following lists
- User profiles with username, bio, avatar placeholder, follower/following counts, and profile tweets
- Personalized timeline showing:
  - authenticated user tweets
  - followed users' tweets
  - newest-first ordering
  - pagination
- Seed data with demo credentials and populated content

## Technical Decisions

- Laravel + Blade + Tailwind for fast, maintainable full-stack delivery.
- SQLite for local ease of setup and evaluation.
- Breeze used as first-party auth scaffolding (no Firebase/Supabase/third-party auth).
- Follows and likes modeled with pivot-style tables and unique constraints to enforce idempotent relationships.
- Timeline query built from authenticated user plus followed users, ordered by created_at descending.

## Trade-offs and Known Limitations

- SQLite is optimized for local/dev simplicity, not production-scale throughput.
- No notifications, replies, or media upload in current scope.
- UI is intentionally pragmatic and challenge-focused rather than a fully polished design system.
- No Docker workflow is included in this repository.

## AI Usage Notes

- GitHub Copilot Agent was used to accelerate implementation.
- Human review and validation were performed through:
  - automated tests (php artisan test)
  - build checks (npm run build)
  - git diff inspection
  - small, focused commits during development
