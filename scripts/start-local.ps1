$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath (Split-Path -Parent $PSScriptRoot)
php -r "exit(extension_loaded('gd') ? 0 : 1);"
if ($LASTEXITCODE -ne 0) {
    $env:PHP_INI_SCAN_DIR = Join-Path $PSScriptRoot 'php'
}
php artisan serve --host=127.0.0.1 --port=8000
