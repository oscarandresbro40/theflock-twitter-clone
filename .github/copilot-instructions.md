# Copilot Instructions — Twitter Clone Challenge

This is a full-stack Twitter/X clone technical challenge.

Use Laravel conventions and keep the implementation pragmatic, maintainable, and well-tested.

## Project goal

Build a functional Twitter/X clone with a clean Git history, meaningful tests, clear documentation, and pragmatic technical decisions.

The evaluators will review:
- Functionality
- Code quality
- Testing
- Development process and commit history
- README / Runbook
- Technical decisions
- Effective use of AI-assisted coding

## Stack

- Laravel
- Blade
- Tailwind CSS
- SQLite for local development
- Laravel authentication scaffolding
- PHPUnit or Pest for tests

## Rules

- Do not use Firebase Auth, Supabase Auth, or third-party authentication providers.
- Make small, focused changes.
- Do not implement multiple major features at once.
- Prefer readable Laravel code over clever abstractions.
- Validate all user input.
- Protect authenticated actions with middleware.
- Add meaningful tests alongside features.
- Avoid unnecessary packages unless clearly justified.
- Do not overengineer the solution.
- Prefer Laravel conventions unless there is a clear reason not to.

## Required features

- Register, login, logout.
- Basic user profile with username, bio, and avatar displayed as a static default image, such as a grey silhouette SVG. No file upload is required.
- Create tweets with max 280 characters.
- Delete own tweets.
- Timeline showing tweets from followed users as well as the authenticated user's own tweets, ordered by most recent first.
- Pagination.
- Follow and unfollow users.
- Like and unlike tweets.
- Visible like count.
- Followers and following lists.
- Basic user search by username prefix using a LIKE query on the username column, displaying matching users with their username and follow/unfollow button, paginated.
- Responsive mobile-first UI.

## Ordering rules

- All tweet listings, including timeline and profile pages, must be ordered by `created_at` descending.
- Followers and following lists must be ordered by follow `created_at` descending.
- Search results must be ordered by `username` ascending.

## Business rules

- Follow and Like actions must be idempotent.
- Use `firstOrCreate` or check existence before inserting to avoid duplicate constraint errors.
- Prevent a user from following themselves. Return a 403 or redirect safely if attempted.
- A user may only delete their own tweets.
- Guest users may view public pages when appropriate, but authenticated actions must require login.

## Expected models

- User
- Tweet
- Follow
- Like

## Database rules

Use database constraints where appropriate:

- `tweets.user_id` references `users.id`.
- `follows.follower_id` references `users.id`.
- `follows.followed_id` references `users.id`.
- `likes.user_id` references `users.id`.
- `likes.tweet_id` references `tweets.id`.
- Add a unique constraint on `follows(follower_id, followed_id)`.
- Add a unique constraint on `likes(user_id, tweet_id)`.

## Testing expectations

Add tests alongside features.

Use meaningful tests, not empty coverage tests.

Backend tests should cover:
- Models and relationships.
- Validation rules.
- Authentication-protected actions.
- Tweet creation and deletion.
- Follow and unfollow.
- Like and unlike.
- Timeline behavior.

Frontend or integration flow tests should cover:
- Login.
- Create tweet.
- Follow user.

## Documentation rule

Do not invent completed features in the README.

Only document features, setup steps, commands, credentials, decisions, limitations, and trade-offs that actually exist in the codebase.

## Validation commands

Use these before committing when relevant:

```bash
php artisan test
npm run build