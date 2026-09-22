# Verification record

Verified locally on Windows with PHP 8.3 and Laravel 13.

- 17 application feature tests passed (123 assertions), including GD/WebP resizing, replacement, and deletion.
- 9 Chromium browser tests passed, including viewport widths of 360, 390, 768, 1440, and 1920 pixels.
- Light/dark screenshots inspected; tablet decorative overflow fixed and retested.
- Production asset compilation and Laravel configuration/route/view caching succeeded.
- MySQL PDO driver migrations, seeding, JSON casting, update, and deletion succeeded against an isolated MariaDB 10.4.32 instance. Oracle MySQL 8 was not installed locally; deployment configuration targets MySQL. Use a maintained MySQL/MariaDB release in production.
- Public résumé phone redaction verified by text extraction. The original file was not changed.
- No live AI request or SMTP delivery was tested because provider credentials were not supplied. Mocked provider tests cover context filtering, graceful failures, limits, and feedback.

The local preview uses SQLite. Browser contact test messages can be removed from the private inbox. Screenshots are under `artifacts/`; test reports and local tool/runtime directories are ignored by Git. No administrator password is included; run the setup command in the README.
