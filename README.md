# Bheem Chand — personal portfolio

A complete Laravel 13 application with server-rendered Blade pages, Tailwind CSS, JavaScript, MySQL support, an authenticated administration area, and an optional backend AI assistant. Content was verified against the supplied résumé. No invented clients, project links, social URLs, or testimonials are seeded.

## Quick start

Requirements: PHP 8.3+, Composer 2, Node 22+, MySQL 8+ (or a supported MariaDB release), and PHP GD/WebP for uploads. See [deployment requirements](docs/DEPLOYMENT.md).

```sh
composer install
cp .env.example .env
php artisan key:generate
# Configure DB_* in .env and create the MySQL database/user.
php artisan migrate --seed
php artisan storage:link
npm ci
npm run build
php artisan portfolio:admin your-email@example.com
php artisan serve
```

On PowerShell use `Copy-Item .env.example .env` instead of `cp` if preferred, and `npm.cmd` when script execution policy blocks `npm.ps1`. The admin command prompts for a hidden password; no credentials are hardcoded or seeded. Visit `http://127.0.0.1:8000` and `/admin`.

For a quick local SQLite preview, set `DB_CONNECTION=sqlite`, remove `DB_DATABASE` or set it to an absolute SQLite file path, create `database/database.sqlite`, and run migrations. The supplied workspace is already installed with a SQLite preview database and built assets; `.env.example` targets MySQL. Never use a production database for tests.

For local hot reload use `npm run dev` alongside `php artisan serve`. Production serves the compiled assets. The security policy permits the default localhost Vite server only in the local environment when its hot file exists. Use `npm run build` for a preview identical to production.

## Administration

`/admin` provides profile/settings editing, content collections, and inboxes. Projects, experience, education, skills, articles, and AI knowledge support creation, editing, numeric ordering, publishing/unpublishing, and deletion. Projects support featured status, tags, links, image replacement/removal, and SEO fields. Articles default to disabled. Use plain text with line breaks for descriptions and articles.

Profile settings control contact visibility, homepage text, biography, availability, social links, photo, social sharing image, résumé upload, and optional modules. Phone visibility is off by default. LinkedIn/GitHub/live project links are blank and omitted from the interface until supplied and enabled. Images are re-encoded as WebP with random filenames; SVG uploads are not accepted.

Contact messages are stored immediately in the private admin inbox. A honeypot, a minimum completion time, validation, CSRF protection, and throttling protect submission. SMTP is used for password reset, not required for receiving contact messages. The local `log` mail transport writes reset links to `storage/logs/laravel.log`; configure real SMTP before deploying.

## Résumé

**Admin → Page copy & chat** also edits section headings, page introductions, the homepage technology strip, and assistant starter questions without changing templates.

```sh
php artisan portfolio:resume /path/to/your-public-resume.pdf
```

Import a profile photo from the command line when preparing a deployment:

```sh
php artisan portfolio:photo /path/to/profile-photo.png
```

The image is optimized and stored in the same admin-managed profile field. It can later be replaced or removed under **Admin → Profile & settings**.

You can also upload a PDF in admin. PDF contents are independent of website phone visibility. In this workspace the supplied résumé was copied with its phone number redacted; the redaction was verified by re-extracting the text. The source PDF in Downloads was not modified. A redacted copy is included at `resources/documents/Bheem-Chand-Resume.pdf` for fresh installations:

```sh
php artisan portfolio:resume resources/documents/Bheem-Chand-Resume.pdf
```

## AI assistant

See [AI configuration and behavior](docs/AI.md). Supply a compatible provider API base URL, server-side key, and model identifier. Without credentials the widget shows an unavailable state with a contact link. Published database content and admin-managed knowledge form its context. Hidden contact fields and drafts are excluded. Chat transcripts are not stored by default. Actual live model behavior needs validation after a key/model are supplied; provider calls are mocked in automated tests.

## Verification

```sh
php artisan test
npm run build
# Start the local server first:
npx playwright install chromium
npx playwright test
```

Feature tests use an isolated in-memory SQLite database and cover authorization, content lifecycle, unsafe inputs, contact messages, profile visibility, reset flow, résumé visibility, chat context boundaries, provider failure/budgets, feedback, and retention. Browser tests cover 360/390/768/1440/1920px layouts, persistent themes, navigation, filters, chat fallback/reset, real contact submission, and CSRF enforcement. Browser tests create a contact message named `Browser verification`; delete it from admin afterward. Screenshots are written under ignored `artifacts/`.

On Windows, `scripts/start-local.ps1` starts the preview with GD enabled when needed. For tests, enable GD in PHP configuration or run `$env:PHP_INI_SCAN_DIR = (Resolve-Path scripts/php).Path` before `php artisan test`, so subprocesses inherit it. You can also use `php -d extension=gd vendor/phpunit/phpunit/phpunit` directly. Tests refuse to run against any database other than their isolated in-memory database.

## Source map

```text
app/Http/Controllers/     Public pages, authentication, admin CRUD, chat
app/Http/Middleware/      Security response headers
app/Models/               User, typed content records, settings
app/Services/             Image processing and provider chat adapter
bootstrap/app.php         Routing and middleware configuration
config/portfolio.php      Provider, budget, retention settings
database/migrations/      Content, settings, inbox, feedback/history schema
database/seeders/         Verified résumé content; no default user
resources/views/          Public, admin, authentication, error Blade views
resources/css/app.css     Responsive light/dark design with Tailwind
resources/js/app.js       Theme, navigation, accessible chat interactions
routes/web.php            Public and gated admin routes
routes/console.php        Admin setup, résumé import, scheduled pruning
tests/Feature/            Application/security behavior tests
tests/Browser/            Playwright browser checks
docs/                    Deployment and AI setup
```

## Before launch

- Supply a profile photo and project screenshots with alternative text.
- Add LinkedIn, GitHub, and any publishable project/source URLs.
- Confirm current work availability; it is deliberately unspecified.
- Create your admin account with `portfolio:admin`.
- Supply your production domain, database credentials, SMTP settings, and optional AI key/model.
- Review the public résumé, content, and visibility switches.
- Follow [VPS/shared-hosting deployment instructions](docs/DEPLOYMENT.md), including HTTPS, the `public` web root, scheduler, and backups.

No live deployment or email/AI provider delivery is claimed without those external settings.
