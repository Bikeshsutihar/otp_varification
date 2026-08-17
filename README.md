# OTP Email Verification — Laravel + MySQL + Tailwind + SweetAlert2

A complete, ready-to-run Laravel application that verifies new users' email
addresses with a **6-digit One-Time Password (OTP)** sent by email during
registration. Built with Laravel 12, MySQL, Tailwind CSS v4, vanilla
JavaScript, Font Awesome, and SweetAlert2, styled with a custom brand
palette (`#E5F0FF · #99C2FF · #003D99 · #002966 · #001433`).

---

## Features

- **Registration** with name/email/password validation, hashed passwords.
- **OTP generation**: random 6-digit code, only its **SHA-256 hash** is
  stored (never the raw code) in a dedicated `otp_codes` table.
- **Expiry**: codes expire after 10 minutes (configurable).
- **Resend** with a 60-second cooldown, rate-limited server-side.
- **Attempt limiting**: a code is locked out after 5 wrong tries.
- **Email delivery** via a Laravel `Mailable` + branded HTML template.
- **Login** with rate limiting (`RateLimiter`) and friendly error toasts.
- **Route protection**: unverified users are redirected to `/verify-otp`;
  the dashboard is only reachable once verified, via custom middleware
  (`otp.verified`).
- **Logout**.
- **UI**: Tailwind CSS (custom theme), Font Awesome icons, SweetAlert2
  toasts/alerts for every flash message and validation error, an
  auto-advancing 6-box OTP input with paste support and a live resend
  countdown.
- **CSRF protection**, hashed passwords, hashed OTP codes, throttled
  routes on register / login / verify / resend.

---

## Tech Stack

| Layer      | Choice                                   |
|------------|-------------------------------------------|
| Backend    | Laravel 12 (PHP 8.2+)                     |
| Database   | MySQL (via XAMPP)                         |
| Frontend   | Blade + Tailwind CSS v4 + Vite            |
| Icons      | Font Awesome 6 (CDN)                      |
| Alerts     | SweetAlert2 (npm)                         |
| Mail       | Laravel Mailable (SMTP / log driver)      |

---

## Project Structure (key files)

```
app/
  Http/
    Controllers/
      Auth/
        RegisterController.php   # registration + first OTP send
        LoginController.php      # login, logout, rate limiting
        OtpController.php        # verify + resend OTP
      DashboardController.php
    Middleware/
      EnsureOtpIsVerified.php    # blocks unverified users ("otp.verified")
  Mail/
    OtpMail.php                  # Mailable that sends the code
  Models/
    User.php
    OtpCode.php                  # generates, hashes, verifies OTPs

config/
  otp.php                        # length / expiry / attempts / cooldown

database/migrations/
  0001_01_01_000000_create_users_table.php
  2025_01_01_000000_create_otp_codes_table.php

resources/
  css/app.css                    # Tailwind + brand color theme
  js/app.js                      # SweetAlert2 + OTP input + countdown
  views/
    components/layouts/app.blade.php
    partials/nav.blade.php
    auth/register.blade.php
    auth/login.blade.php
    auth/verify-otp.blade.php
    dashboard.blade.php
    emails/otp.blade.php

routes/web.php
```

---

## 1. Requirements

- PHP **8.2+** with extensions: `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`,
  `xml`, `ctype`, `json`, `bcmath`, `fileinfo` (all bundled with XAMPP —
  check your version with `php -v`; most current XAMPP installs ship 8.2).
- **Composer** 2.x — https://getcomposer.org/download/
- **Node.js** 18+ and npm — https://nodejs.org/
- **XAMPP** (Apache + MySQL) — https://www.apachefriends.org/
- A Gmail account (or any SMTP provider) if you want real emails delivered.

---

## 2. Installation (Windows / XAMPP)

### Step 1 — Start MySQL

Open the **XAMPP Control Panel** and start **MySQL** (Apache is optional —
we'll use Laravel's own `php artisan serve` instead of XAMPP's Apache).

### Step 2 — Create the database

Open **phpMyAdmin** (`http://localhost/phpmyadmin`) and create a new
database named:

```
otp_auth
```

(Or run: `mysql -u root -e "CREATE DATABASE otp_auth CHARACTER SET utf8mb4;"`)

### Step 3 — Unzip the project

Extract the ZIP anywhere, e.g. `C:\xampp\htdocs\otp-project`, then open a
terminal (CMD / PowerShell) in that folder.

### Step 4 — Install PHP dependencies

```bash
composer install
```

### Step 5 — Configure environment

```bash
copy .env.example .env
php artisan key:generate
```

Open `.env` and confirm the database block matches your XAMPP MySQL setup
(defaults already match a stock XAMPP install):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=otp_auth
DB_USERNAME=root
DB_PASSWORD=
```

### Step 6 — Configure mail (choose ONE)

**Option A — quick local testing (no real email account needed):**

```env
MAIL_MAILER=log
```

OTP codes will be written to `storage/logs/laravel.log` — open that file
and search for `Your Verification Code:` after registering.

**Option B — real delivery via Gmail SMTP:**

1. Enable 2-Step Verification on your Google account.
2. Create an **App Password**: Google Account → Security → App passwords.
3. Fill in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_16_char_app_password
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="OTP Email Verification"
```

Any other SMTP provider (Mailtrap, SendGrid, Brevo, etc.) works the same
way — just swap the host/port/credentials.

### Step 7 — Run migrations

```bash
php artisan migrate
```

This creates `users`, `otp_codes`, `sessions`, `cache`, and `jobs` tables.

### Step 8 — Install front-end dependencies & build assets

```bash
npm install
npm run build
```

(Use `npm run dev` instead, in a separate terminal, if you want hot-reload
while developing.)

### Step 9 — Serve the app

```bash
php artisan serve
```

Visit **http://127.0.0.1:8000** — you'll land on the login page. Click
**Sign up** to register a new account and receive your OTP code.

---

## 3. How the OTP flow works

1. User submits the registration form → a `User` row is created and they
   are logged in immediately (`Auth::login`), but `email_verified_at` is
   `null`.
2. `OtpCode::generateFor()` creates a random 6-digit code, stores only its
   **SHA-256 hash** plus an `expires_at` timestamp (default: now + 10 min),
   and returns the raw code so it can be emailed.
3. `OtpMail` is dispatched with the raw code — the branded email template
   lives at `resources/views/emails/otp.blade.php`.
4. The user is redirected to `/verify-otp`, where 6 individual input boxes
   (auto-advance, backspace, paste-friendly) combine into one hidden
   `code` field on submit.
5. `OtpController::verify()`:
   - Rejects if no unverified code exists or if it has expired.
   - Rejects and increments `attempts` if the code is wrong.
   - Locks the code out after 5 wrong attempts (`OTP_MAX_ATTEMPTS`).
   - On success, stamps `otp_codes.verified_at` and sets
     `users.email_verified_at = now()` (`markOtpVerified()`), then
     redirects to the dashboard.
6. `OtpController::resend()` is rate-limited to once every 60 seconds
   (`OTP_RESEND_COOLDOWN`) and issues a brand-new code (the old one simply
   stays expired/unverified in the table for audit purposes).
7. The `otp.verified` middleware (`EnsureOtpIsVerified`) guards
   `/dashboard` and any future protected routes — an authenticated but
   unverified user is bounced back to `/verify-otp`.

---

## 4. Security notes

- OTP codes are **never stored in plain text** — only a SHA-256 hash.
- Registration, login, OTP verification, and OTP resend routes are all
  wrapped in `throttle:*` middleware, on top of the app-level
  `RateLimiter` checks inside `LoginController` and `OtpController`
  (keyed per email+IP or per user, with escalating cooldowns).
- Passwords are hashed with bcrypt (`Hash::make`, `'password' => 'hashed'`
  cast).
- CSRF protection is on by default for every POST form (`@csrf`).
- Session-based authentication (`Auth::login`) with session regeneration
  on login/logout to prevent session fixation.
- Validation on every input (server-side); `digits:6` enforced for the OTP
  code, `confirmed`/`min:8` for passwords, `unique:users,email` for email.

---

## 5. Configuration reference (`config/otp.php` / `.env`)

| Env var                    | Default | Meaning                                   |
|-----------------------------|---------|--------------------------------------------|
| `OTP_LENGTH`                | 6       | Digits in the generated code               |
| `OTP_EXPIRES_IN_MINUTES`    | 10      | Minutes before a code expires              |
| `OTP_MAX_ATTEMPTS`          | 5       | Wrong guesses allowed before lockout       |
| `OTP_RESEND_COOLDOWN`       | 60      | Seconds between resend requests            |

---

## 6. Useful commands

```bash
# Re-run migrations from scratch (drops all tables first)
php artisan migrate:fresh

# Tail logs (handy when MAIL_MAILER=log)
type storage\logs\laravel.log        # Windows CMD
Get-Content storage\logs\laravel.log -Wait   # PowerShell

# Clear cached config after editing .env
php artisan config:clear
```

---

## 7. Customizing the color palette

All brand colors live as CSS variables in `resources/css/app.css`:

```css
@theme {
    --color-brand-50: #E5F0FF;
    --color-brand-200: #99C2FF;
    --color-brand-600: #003D99;
    --color-brand-700: #002966;
    --color-brand-900: #001433;
}
```

Use them anywhere in Blade/Tailwind as `bg-brand-600`, `text-brand-900`,
`border-brand-200`, etc. Run `npm run build` again after changes.

---

## 8. Troubleshooting

**`Failed opening required '...vendor/autoload.php'`**
This means `composer install` hasn't completed successfully yet — Laravel
can't run without its dependencies. Run `composer install` and read its
output for errors before doing anything else.

**`Root composer.json requires php ^8.x but your php version (...) does not satisfy that requirement`**
Your PHP CLI version is older than this project needs. Check it with:
```bash
php -v
```
This project targets PHP 8.2+. If you're on an older XAMPP PHP build,
either update XAMPP to a version bundling PHP 8.2+, or install a newer
PHP separately and point your terminal's `PATH` at it before running
Composer commands.

**`...these were not loaded, because they are affected by security advisories (...)`**
Newer Composer versions block installing any package version flagged in
Packagist's security-advisory database by default. This project's
`composer.json` already sets `"audit": {"block-insecure": false}` to
avoid this. If you still hit it after re-extracting the latest ZIP, you
can also run:
```bash
composer config audit.block-insecure false
composer install
```

**`Laravel\Pail\Guards\EnsurePcntlIsAvailable::check()` fails / `composer run dev` exits with code 1 on Windows**
`laravel/pail` (live log tailing) depends on the `pcntl` PHP extension,
which does not exist on Windows at all — not even in XAMPP. This
project's `composer.json` no longer includes `laravel/pail` and its
`dev` script only runs `php artisan serve` + `npm run dev` together, so
this shouldn't come up. If you still see it, delete `vendor/` and
`node_modules/`, re-extract the latest ZIP, and run `composer install`
and `npm install` again.

## License

MIT — do whatever you like with this starter.
