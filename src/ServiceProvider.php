<?php

namespace Terranet\Translator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\TranslationServiceProvider;
use Terranet\Translator\Console\TranslatorFlushCommand;
use Terranet\Translator\Console\TranslatorMigrationCommand;
use Terranet\Translator\Console\TranslatorSetupCommand;
use Terranet\Translator\Console\TranslatorTranslationActionCommand;
use Terranet\Translator\Console\TranslatorTranslationFinderCommand;
use Terranet\Translator\Console\TranslatorTranslationModelCommand;
use Terranet\Translator\Console\TranslatorTranslationModuleCommand;
use Terranet\Translator\Console\TranslatorTranslationTemplateCommand;
use Terranet\Translator\Observers\TranslationObserver;

class ServiceProvider extends TranslationServiceProvider
{
    protected $commands = [
        'Migration' => 'command.translator.migration',
        'MakeTranslationModel' => 'command.translator.make-translation-model',
        'MakeTranslationModule' => 'command.translator.make-translation-module',
        'MakeTranslationAction' => 'command.translator.make-translation-action',
        'MakeTranslationFinder' => 'command.translator.make-translation-finder',
        'MakeTranslationTemplate' => 'command.translator.make-translation-template',
        'Setup' => 'command.translator.setup',
        'Flush' => 'command.translator.flush',
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/translator.php', 'translator');

        $this->registerCommands();

        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $fallbackLoader = $app['translation.loader_fallback'];

            $locale = $app['config']['app.locale'];

            $fallbackTranslator = new Translator($fallbackLoader, $locale);

            $fallbackTranslator->setFallback($app['config']['app.fallback_locale']);

            $loader = $app['translation.loader'];

            $translator = new Translator($loader, $locale, $fallbackTranslator);

            $translator->setFallback($app['config']['app.fallback_locale']);

            return $translator;
        });
    }

    protected function registerCommands()
    {
        foreach (array_keys($this->commands) as $command) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], []);
        }

        $this->commands(array_values($this->commands));
    }

    protected function registerMigrationCommand()
    {
        $this->app->singleton('command.translator.migration', function ($app) {
            return new TranslatorMigrationCommand($app['files'], $app['composer']);
        });
    }

    protected function registerMakeTranslationModelCommand()
    {
        $this->app->singleton('command.translator.make-translation-model', function ($app) {
            return new TranslatorTranslationModelCommand($app['files']);
        });
    }

    protected function registerMakeTranslationModuleCommand()
    {
        $this->app->singleton('command.translator.make-translation-module', function ($app) {
            return new TranslatorTranslationModuleCommand($app['files']);
        });
    }

    protected function registerMakeTranslationActionCommand()
    {
        $this->app->singleton('command.translator.make-translation-action', function ($app) {
            return new TranslatorTranslationActionCommand($app['files']);
        });
    }

    protected function registerMakeTranslationFinderCommand()
    {
        $this->app->singleton('command.translator.make-translation-finder', function ($app) {
            return new TranslatorTranslationFinderCommand($app['files']);
        });
    }

    protected function registerMakeTranslationTemplateCommand()
    {
        $this->app->singleton('command.translator.make-translation-template', function ($app) {
            return new TranslatorTranslationTemplateCommand($app['files']);
        });
    }

    protected function registerSetupCommand()
    {
        $this->app->singleton('command.translator.setup', function () {
            return new TranslatorSetupCommand();
        });
    }

    protected function registerFlushCommand()
    {
        $this->app->singleton('command.translator.flush', function () {
            return new TranslatorFlushCommand();
        });
    }

    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new DbLoader($app['cache']->store());
        });

        $this->app->singleton('translation.loader_fallback', function ($app) {
            return new FileLoader($app['files'], $app['path.lang']);
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'translator');

        $this->publishes([
            __DIR__ . '/../views' => resource_path('views/vendor/translator'),
        ]);

        $this->publishes([
            __DIR__.'/../config/translator.php' => config_path('translator.php'),
        ]);

        if (class_exists($model = config('translator.model'))) {
            $model::observe(TranslationObserver::class);
        }
    }
}
