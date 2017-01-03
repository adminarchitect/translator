<?php

namespace Terranet\Translator\Console;

use Illuminate\Console\Command;
use Terranet\Translator\DbLoader;

class TranslatorFlushCommand extends Command
{
    protected $name = 'translator:flush';

    protected $description = 'Flush translator cache data.';

    public function fire()
    {
        $loader = app('translation.loader');

        if ($loader instanceof DbLoader) {
            $loader->flush();
        }
    }
}
