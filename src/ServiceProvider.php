<?php

namespace Terranet\Translator;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Terranet\Translator\Console\TranslatorTableCommand;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        if (! defined('_TERRANET_TRANSLATOR_')) {
            define('_TERRANET_TRANSLATOR_', 1);
        }

        $this->checkDependencies();

        $baseDir = realpath(__DIR__ . '/..');

        /*
         * Publish & Load configuration
         */
//        $this->publishes(["{$baseDir}/publishes/config.php" => config_path('translator.php')], 'config');
//        $this->mergeConfigFrom("{$baseDir}/publishes/config.php", 'translator');

        /*
         * Publish & Load views, assets
         */
//        $this->publishes(["{$baseDir}/publishes/views" => base_path('resources/views/vendor/translator')], 'views');
        $this->loadViewsFrom("{$baseDir}/publishes/views", 'translator');

        /*
         * Publish & Load translations
         */
//        $this->publishes(
//            ["{$baseDir}/publishes/lang" => base_path('resources/lang/vendor/translator')],
//            'translator'
//        );
        $this->loadTranslationsFrom("{$baseDir}/publishes/lang", 'translator');

        $this->publishes(
            [
                "{$baseDir}/publishes/Models" => app_path(),
                "{$baseDir}/publishes/Modules" => app_path(app('scaffold.config')->get('paths.module')),
            ],
            'translator'
        );
    }

    public function register()
    {
        $this->app->singleton('command.translator.table', function ($app) {
            return new TranslatorTableCommand($app['files'], $app['composer']);
        });

        $this->app->singleton('command.translator.load', function ($app) {
            return new TranslatorLoadCommand($app['files'], $app['composer']);
        });

        $this->commands(['command.translator.table']);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function checkDependencies()
    {
        $mandatory = [
            'terranet/localizer' => \Terranet\Localizer\ServiceProvider::class,
        ];

        foreach ($mandatory as $package => $provider) {
            if (!array_has(app()->getLoadedProviders(), $provider)) {
                throw new \Exception("Mandatory package `{$package}` is missing.");
            }
        }
    }
}
