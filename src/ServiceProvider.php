<?php

namespace Terranet\Translator;

use Illuminate\Translation\FileLoader;
use Illuminate\Translation\TranslationServiceProvider;
use Terranet\Translator\Console\TranslatorMigrationCommand;
use Terranet\Translator\Console\TranslatorSetupCommand;
use Terranet\Translator\Console\TranslatorTranslationModelCommand;
use Terranet\Translator\Console\TranslatorTranslationModuleCommand;

class ServiceProvider extends TranslationServiceProvider
{
    protected $commands = [
        'Migration' => 'command.translator.migration',
        'MakeTranslationModel' => 'command.translator.make-translation-model',
        'MakeTranslationModule' => 'command.translator.make-translation-module',
        'Setup' => 'command.translator.setup',
    ];

    public function register()
    {
        $this->registerCommands();

        parent::register();
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

    protected function registerSetupCommand()
    {
        $this->app->singleton('command.translator.setup', function () {
            return new TranslatorSetupCommand();
        });
    }

    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new FileLoader($app['files'], $app['path.lang']);
        });
    }
}
