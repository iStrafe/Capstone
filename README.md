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

The seeder creates one admin account from `ADMIN_EMAIL` and `ADMIN_PASSWORD`. If the password is empty, a random one is printed once. Use it for local setup only.

## Admin accounts on a real server

Don't run the seeder on a deployed site. Create the admin from the server's shell instead:

```bash
php artisan app:create-admin admin@yourdomain.test
```

It asks for the password twice without showing it, so the password never lands in code or shell history. It must be at least 12 characters with upper- and lowercase letters and a number, and in production it is also checked against known leaked passwords. To make an existing account (for example one created with Google sign-in) an admin, add `--promote`.

Optional services read their keys from `.env`: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` and `GOOGLE_REDIRECT` for Google login, `PAYMONGO_SECRET_KEY` for donations, and `OPENAI_API_KEY` for the breed guess.

## Frontend

The site is moving from Blade (Bootstrap + jQuery) to React, one page at a time. Both kinds of page run side by side:

- **React pages** live in `resources/js/pages` and are served with [Inertia](https://inertiajs.com): the controller returns `Inertia::render('Cats/Show', [...])` instead of `view(...)`. They're plain JavaScript (`.jsx`), styled with Tailwind CSS 4 using the Azure theme in `resources/css/site.css`, and share the layout in `resources/js/layouts/SiteLayout.jsx` and the components in `resources/js/components`.
- **Blade pages** keep their own layouts until they're rebuilt.

Converted so far: the home page and the cat profile (`/cat/{id}`). While you work on the frontend, run Vite next to the PHP server so changes reload instantly:

```bash
npm run dev
php artisan serve
```

Links from a React page to a Blade page must be plain `<a href>` tags, not Inertia's `<Link>`, so the browser does a full page load.

## Tests

Tests run against a separate `aducats_test` database (set in `phpunit.xml`), so create it once:

```bash
createdb aducats_test
php artisan test
```

CI runs the suite on both SQLite and PostgreSQL.
