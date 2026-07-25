# BusinessSphere ERP — PHP Edition

34-module ERP in plain PHP. No npm. No build step. No React. Just upload and run.

## Deploy in 60 seconds

### cPanel / Shared Hosting (easiest)
1. Upload all files to `public_html/`
2. Done — visit your domain

### Render
1. Push to GitHub
2. Render → New → Web Service → PHP
3. Build Command: (leave empty)
4. Start Command: `php -S 0.0.0.0:$PORT`

### Any VPS / Server
```bash
git clone your-repo /var/www/html/businesssphere
# Apache or Nginx with PHP 8.x installed — done
```

### Local Dev
```bash
cd businesssphere-php
php -S localhost:8080
# Open http://localhost:8080
```

## Setup

Edit `config.php`:
```php
define('SUPABASE_URL',      'https://YOUR-PROJECT.supabase.co');
define('SUPABASE_ANON_KEY', 'your-anon-key');
define('COMPANY_NAME',      'Your Company');
define('CURRENCY',          'TZS');
```

## Requirements
- PHP 7.4+ (8.x recommended)
- Any web server (Apache, Nginx, Render, cPanel)
- No Composer, no npm, no Node.js

## Modules
Dashboard · CRM · Sales · Inventory · Finance · HR + 28 more industry modules
