# translator
Store &amp; Fetch Laravel translations into/from Database.

# Installation

1. In `config/app.php` replace standart `Illuminate\Translation\TranslationServiceProvider::class`
   to `Terranet\Translator\ServiceProvider::class`.
2. Create migrations `php artisan translator:migrations`.
3. Run migrations `php artisan migrate`.
