# translator
Store &amp; Fetch Laravel translations into/from Database.

# Installation

1. In `config/app.php` replace standard provider `Illuminate\Translation\TranslationServiceProvider::class`;
   to `Terranet\Translator\ServiceProvider::class`;
2. Setup translator module `php artisan translator:setup`;
3. Run migrations `php artisan migrate`;
4. Add middleware `\Terranet\Translator\Middleware\SaveNewTranslations::class` to `$middleware` in `app/Http/Kernel.php`.
