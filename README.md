# AduCats

A Laravel app for a cat shelter: public cat listings and adoption requests, news and events, donations through PayMongo, and an admin area for cats, requests and events.

## Requirements

- PHP 8.3 or newer, Composer
- Node 20 or newer, npm
- PostgreSQL 16 (the default connection)

## Setup

```bash
composer install
npm ci && npm run build
cp .env.example .env
php artisan key:generate
```

Set `DB_USERNAME` and `DB_PASSWORD` in `.env`, create the `aducats` database, then:

```bash
php artisan migrate --seed
php artisan serve
```

The seeder creates one admin account from `ADMIN_EMAIL` and `ADMIN_PASSWORD`. If the password is empty, a random one is printed once.

Optional services read their keys from `.env`: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` and `GOOGLE_REDIRECT` for Google login, `PAYMONGO_SECRET_KEY` for donations, and `OPENAI_API_KEY` for the breed guess.

## Tests

Tests run against a separate `aducats_test` database (set in `phpunit.xml`), so create it once:

```bash
createdb aducats_test
php artisan test
```

CI runs the suite on both SQLite and PostgreSQL.
