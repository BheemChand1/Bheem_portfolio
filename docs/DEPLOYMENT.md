# Production deployment

## Requirements

PHP 8.3 or newer with PDO MySQL, mbstring, OpenSSL, tokenizer, XML, ctype, JSON, fileinfo, curl, and GD with WebP support; MySQL 8+ or MariaDB 10.6+; Composer 2; Node 22+ for building assets. PHP GD is required to re-encode uploads. Set `upload_max_filesize=10M`, `post_max_size=16M`, and `memory_limit=256M`. HTTPS is required in production.

## VPS installation

1. Copy the project into `/srv/bheem-portfolio`. Keep `.env`, dependencies, database files, and `storage/app/private` outside the web root.
2. Create a database and a dedicated user with access only to that database. For example, as your database administrator:

```sql
CREATE DATABASE bheem_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'portfolio'@'localhost' IDENTIFIED BY 'REPLACE_WITH_A_RANDOM_SECRET';
GRANT ALL PRIVILEGES ON bheem_portfolio.* TO 'portfolio'@'localhost';
```

3. Install and configure:

```sh
cp .env.example .env
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan key:generate
```

Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain.example`, `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, and the `DB_*` variables. Configure SMTP and AI if desired. Keep the generated application key stable after launch.

```sh
php artisan migrate --seed --force
php artisan storage:link
php artisan portfolio:admin your-admin-email@example.com
php artisan portfolio:resume /secure/path/to/public-resume.pdf
php artisan optimize
```

The admin command prompts for a hidden password, requires at least 12 characters with letters and numbers, and refuses to overwrite existing accounts. There is no registration route and no default password. Seeders do not create users or overwrite edited content.

Grant the PHP-FPM user write access only to `storage` and `bootstrap/cache`, using your deployment group. Do not use world-writable permissions. Back up the database, `.env`/application key, `storage/app/public`, and `storage/app/private` securely. Test restoration.

## Nginx

Use this as the site block behind your HTTPS certificate setup. Replace the domain and PHP socket for your host. Redirect HTTP to HTTPS using your certificate tooling.

```nginx
server {
    listen 443 ssl;
    server_name your-domain.example;
    # ssl_certificate and ssl_certificate_key supplied by your certificate tooling
    root /srv/bheem-portfolio/public;
    index index.php;
    charset utf-8;
    client_max_body_size 16M;

    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_hide_header X-Powered-By;
    }
    location ~ \.php$ { return 404; }
    location ~ /\.(?!well-known).* { deny all; }
}
```

Only `index.php` executes PHP. Uploaded images are validated, re-encoded as WebP, and stored under random names. PDF files stay private and are delivered through a controlled route. Images are publicly accessible once uploaded; deleting or unpublishing a content record is not an access-control mechanism for an already shared image URL. Deleting the record removes its image file.

## Scheduler and retention

Add to the deployment user's crontab:

```cron
* * * * * cd /srv/bheem-portfolio && php artisan schedule:run >> /dev/null 2>&1
```

The hourly command removes expired chat transcripts. `AI_RETENTION_DAYS=0` disables new transcript storage and removes existing history on the next pruning run. For immediate removal run `php artisan portfolio:prune-chat`. Contact messages and anonymous chat feedback remain until deleted from admin. Review provider-side retention separately. Web server access logs may retain IP addresses according to hosting configuration; the chat application does not put IP addresses into its transcript table.

## SMTP and password reset

Set `MAIL_MAILER=smtp`, `MAIL_SCHEME`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME` using your mail provider's settings. The local default `log` transport writes reset emails into `storage/logs/laravel.log`; it does not deliver email. Protect logs because reset links are sensitive. Test a real password reset after SMTP configuration. Contact submissions are persisted directly in the admin inbox; they do not depend on SMTP.

## Shared hosting

Set the domain document root to the application's **`public` directory**. Enable PHP 8.3+ and required extensions, create MySQL credentials in the hosting panel, upload built assets and Composer dependencies, and run the migration/admin commands using SSH or the host's terminal. If the host does not permit the document root to target `public`, use a host-supported mapping to that directory or change hosting plans. Do not expose the project root or move `.env` into `public_html`. If symlinks are restricted, arrange a host-supported public-storage mapping; do not publish private storage.

## Release checks

- `/up` responds; HTTPS and correct canonical URLs work.
- `APP_DEBUG=false`, secure session cookies, no default administrator credentials.
- Admin login/logout, password reset email delivery, contact inbox, image and PDF uploads work.
- Real provider chat succeeds with configured credentials, or unavailable state is intentional.
- Scheduler runs, database/file backups succeed, and error logs are monitored.
- Assets load from `public/build`; PHP-FPM has the required extensions.

Framework baseline: [Laravel 13 deployment documentation](https://laravel.com/docs/13.x/deployment).
