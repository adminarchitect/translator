<?php

namespace Terranet\Translator\Console;

use Illuminate\Console\Command;

class TranslatorLoadCommand extends Command
{
    protected $name = 'translator:load';

    protected $description = "Load translations into the database";
}