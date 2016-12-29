<?php

namespace Terranet\Translator\Console;

use Illuminate\Console\Command;

class TranslatorSetupCommand extends Command
{
    protected $name = 'translator:setup';

    protected $description = 'Setup translator package.';

    protected $commands = [
        'translator:migration' => 'Creating migration',
        'translator:make-translation' => 'Creating translation model',
    ];

    public function fire()
    {
        foreach ($this->commands as $command => $info) {
            $this->line(PHP_EOL . $info);

            $this->call($command);
        }
    }
}
