# The Flock Twitter Clone Challenge

Full-stack Twitter/X clone built for The Flock technical challenge.

This implementation covers the core social workflows end to end: authentication, profiles, tweeting, following, likes, replies, personalized timeline behavior, user discovery, image upload on tweets, and seeded demo-ready data so evaluators can run the app immediately.

## Project Overview

Core workflows implemented in the app:

- Register, login, logout
- Create and delete tweets
- Upload an optional single image per tweet
- Follow and unfollow users
- Like and unlike tweets
- Reply to tweets and browse reply threads
- Discover users via username prefix search
- View public profiles and follower/following lists
- Browse a personalized, paginated timeline

## Technical Stack

- Laravel 13
- Blade
- Tailwind CSS
- SQLite
- Laravel Breeze
- Vite and npm
- Laravel feature and unit tests

Why this stack:

- Laravel plus Blade plus Tailwind provides a fast, maintainable full-stack delivery path with clear conventions.
- SQLite reduces local setup friction for evaluation and reproducibility.
- Breeze provides first-party authentication scaffolding without third-party auth providers.
- Vite and npm provide a standard frontend asset pipeline.
- Laravel tests provide confidence for feature behavior and regressions.

## Prerequisites

- PHP 8.3+
- Composer 2.9+
- Node 20+ or compatible
- npm
- PHP SQLite extensions:
  - pdo_sqlite
  - sqlite3

Developed with Node 24.16.0.

Any local PHP environment is supported as long as prerequisites are installed, including terminal PHP, XAMPP, WAMP, Laragon, Laravel Herd, Valet, Linux, macOS, or Windows.

## Setup Runbook

### 1) Clone the repository

Main option (HTTPS):

```bash
git clone https://github.com/oscarandresbro40/theflock-twitter-clone.git
cd theflock-twitter-clone
```

Alternative option (SSH):

```bash
git clone git@github.com:oscarandresbro40/theflock-twitter-clone.git
cd theflock-twitter-clone
```

### 2) Install dependencies

```bash
composer install
npm install
```

### 3) Create environment file

Windows (CMD or PowerShell):

```bash
copy .env.example .env
```

macOS, Linux, or Git Bash:

```bash
cp .env.example .env
```

### 4) Generate app key

```bash
php artisan key:generate
```

### 5) Configure SQLite

Ensure this exists in .env:

- DB_CONNECTION=sqlite

Laravel uses database/database.sqlite by default for sqlite.

Optional explicit path if needed:

- DB_DATABASE=database/database.sqlite

Create the database file if needed:

Windows:

```bash
type nul > database\database.sqlite
```

macOS or Linux:

```bash
touch database/database.sqlite
```

### 6) Run database and seed data

```bash
php artisan migrate:fresh --seed
```

### 7) Link storage for uploaded images

```bash
php artisan storage:link
```

### 8) Build frontend assets

```bash
npm run build
```

### 9) Start the application

```bash
php artisan serve
```

Open the local URL shown in terminal.

Optional Windows note:

- Laragon can be used, but it is not required.

## Development Commands

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

- email: demo@example.com
- password: password

## Completed Features

### Authentication

- Register, login, logout with Breeze
- Authenticated route protection for authenticated actions

### Tweets

- Create tweet with max 280-character validation
- Delete tweet by owner only
- Optional single image upload per tweet
- Image validation for type and size
- Uploaded image cleanup on tweet deletion

### Social Graph

- Follow and unfollow users
- Followers list page
- Following list page

### Likes

- Like and unlike tweets
- Visible like counts

### Replies and Threads

- Reply threads on tweet detail pages
- Public thread viewing for guests
- Reply posting for authenticated users
- Reply counts on tweet cards

### User Discovery

- User search by username prefix

### Profiles

- Public profile pages
- Unique username and editable bio
- Static avatar placeholder
- Follower and following counts
- Profile tweet list

### Timeline

- Personalized timeline includes authenticated user tweets and followed users' tweets
- Newest-first ordering
- Pagination

### Seed Data

- Demo user credentials
- At least 10 users seeded
- Seeded tweets, follows, likes, replies

## Bonus Features Implemented

- Reply threads
- Seeded replies
- Reply counts on tweet cards
- Optional single image upload per tweet

## Technical Decisions

- Laravel plus Blade plus Tailwind:
  - Chosen for pragmatic full-stack delivery with strong defaults and maintainable server-rendered UI.
- SQLite:
  - Chosen for low-friction local setup and evaluator-friendly onboarding.
- Authentication with Laravel Breeze:
  - First-party auth scaffolding only, without third-party auth services.
- Follows graph modeling:
  - Modeled with follows table linking follower_id and followed_id, with uniqueness constraints for idempotent behavior.
- Personalized timeline:
  - Built from root tweets authored by the authenticated user and users they follow, ordered newest first and paginated.
- Likes modeling:
  - Modeled with likes table linking user_id and tweet_id, with uniqueness constraints to prevent duplicate likes.
- Replies modeling:
  - Replies use tweets.parent_id referencing tweets.id, enabling root tweets and direct-thread replies.
- Image uploads:
  - Optional single image per tweet, stored on public disk, with type and size validation and cleanup during delete.
- Profiles and search:
  - Public profile pages expose user metadata and tweet history; search uses username prefix matching.
- Seed data for evaluation:
  - Seeding produces realistic social data so reviewers can immediately test key workflows.

## Trade-offs and Known Limitations

- SQLite is optimized for local evaluation; production would typically use PostgreSQL or MySQL.
- No real-time updates.
- No notifications.
- No Docker workflow.
- No multiple image uploads per tweet.
- No image editing, replacement, or removal flow for existing tweets.
- UI is pragmatic and challenge-focused rather than a polished design system.

## AI Usage Notes

- ChatGPT, using GPT-5.5 Thinking, was used for planning, architecture guidance, scope control, README and runbook drafting, prompt design, and debugging strategy.
- GitHub Copilot Agent inside VS Code was used for implementation assistance on focused feature slices.
- AI-generated changes were manually reviewed and validated with:
  - git diff
  - php artisan test
  - npm run build
  - manual browser testing
  - database and migration checks
  - small focused commits
- Final validation, debugging decisions, git process, and delivery decisions were manually reviewed.

## Final Validation Checklist

Run:

```bash
php artisan migrate:fresh --seed
php artisan test
npm run build
php artisan serve
```

Manually verify:

- Registration and login
- Demo login with demo@example.com
- Create and delete tweets
- Optional image upload on tweet create
- Reply creation and thread viewing
- Personalized timeline behavior and pagination
- Search behavior
- Follow and unfollow behavior
- Like and unlike behavior
- Public profiles
- Followers and following pages
- Mobile-usable layout

## Live Demo

A hosted demo is available at:

https://theflockchallenge.citricstudio.com

Demo credentials:

- Email: demo@example.com
- Password: password

The repository remains fully runnable locally through the setup runbook below.
