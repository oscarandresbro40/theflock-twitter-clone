# The Flock Twitter Clone

This repository contains a Laravel-based Twitter/X clone challenge project.

The current implementation includes first-party authentication, tweet creation and deletion, follow and unfollow, and like and unlike behavior. It uses Blade views, Tailwind CSS, and SQLite for local development.

## Current stack

- Laravel
- Blade
- Tailwind CSS
- SQLite for local development
- Laravel Breeze for authentication scaffolding

## Current features

- Register, login, and logout with Laravel Breeze
- Authenticated dashboard
- Create tweets with a 280 character limit
- Delete only your own tweets
- Show the authenticated user's tweets ordered by newest first
- Follow and unfollow other users
- Prevent self-follow
- Idempotent follow and unfollow actions
- Like and unlike tweets
- Visible like count on displayed tweets
- Idempotent like and unlike actions

## Prerequisites

- PHP 8.3+
- Composer 2.9+
- Node 24 LTS or a compatible version
- SQLite and the required PHP SQLite extensions enabled

## Setup

Run these commands from the project root:

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

After `php artisan serve`, open the local URL shown in the terminal.

## Test and validation commands

```bash
php artisan test
npm run build
```

## Technical decisions

- Laravel + Blade + Tailwind keeps the application close to framework conventions and easy to review.
- SQLite is used for local development to reduce setup overhead.
- Laravel Breeze provides first-party authentication scaffolding without introducing third-party auth services.

## Known limitations and pending work

- Public profile pages and fuller profile functionality are still pending.
- User search is not implemented yet.
- Personalized timeline behavior is not complete, and pagination is not yet in place.
- Seed data is not included yet.
- Responsive polish is still pending.
- Final AI usage notes for submission documentation are still pending.
