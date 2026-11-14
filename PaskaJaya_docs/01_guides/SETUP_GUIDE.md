# Setup Guide
Prasyarat: PHP 8.3, Composer, Node 20+, MySQL 8 (atau SQLite untuk dev), Git.
```bash
composer create-project laravel/laravel paska-jaya
cd paska-jaya
composer require inertiajs/inertia-laravel tightenco/ziggy maatwebsite/excel barryvdh/laravel-dompdf
composer require --dev pestphp/pest pestphp/pest-plugin-laravel laravel/pint
php artisan inertia:middleware
npm i react react-dom @inertiajs/react @vitejs/plugin-react typescript recharts lucide-react clsx
npm i -D tailwindcss postcss autoprefixer @types/react @types/react-dom
cp .env.example .env && php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run dev & php artisan serve
```
`.env`: `APP_NAME="PASKA JAYA"`, `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`, `AUTH_OTP_ENABLED=false`.
Scheduler (produksi): cron `* * * * * php artisan schedule:run`.
Akun seeder: lihat `07_extras/DEMO_DATA.md` (hanya local).
