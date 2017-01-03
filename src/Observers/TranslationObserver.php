<?php

namespace Terranet\Translator\Observers;

use Terranet\Translator\DbLoader;
use Terranet\Translator\Models\Translation;
use Terranet\Translator\Translator;

class TranslationObserver
{
    public function saved(Translation $row)
    {
        $this->flushCache($row);
    }

    public function deleted(Translation $row)
    {
        $this->flushCache($row);
    }

    protected function flushCache(Translation $row)
    {
        if ($row['value'] != $row->getOriginal('value')) {
            /** @var DbLoader $loader */
            $loader = app('translation.loader');

            /** @var Translator $translator */
            $translator = app('translator');

            list($namespace, $group) = $translator->parseKey($row['key']);

            $loader->clearCache($row['locale'], $namespace, $group);
        }
    }
}