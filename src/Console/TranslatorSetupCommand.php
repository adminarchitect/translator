<?php

namespace Terranet\Translator\Console;

use Illuminate\Console\Command;

class TranslatorSetupCommand extends Command
{
    protected $name = 'translator:setup';

    protected $description = 'Setup translator package.';

    protected $commands = [
        'translator:migration' => 'Creating migration',
        'translator:make-translation-model' => 'Creating translation model',
        'translator:make-translation-module' => 'Creating translation module',
        'translator:make-translation-action' => 'Creating translation action',
        'translator:make-translation-finder' => 'Creating translation finder',
        'translator:make-translation-template' => 'Creating translation template',
    ];

    public function fire()
    {
        foreach ($this->commands as $command => $info) {
            $this->line(PHP_EOL . $info);

            $this->call($command);
        }
    }
}
