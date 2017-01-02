<?php

namespace Terranet\Translator\Console;

use Illuminate\Console\GeneratorCommand;

class TranslatorTranslationFinderCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translator:make-translation-finder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create translation finder if it does not exist.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Translation finder';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        parent::fire();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/translation-finder.stub';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return 'Translations';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Http\\Terranet\\Administrator\\Finders';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }
}
