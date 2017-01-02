<?php

namespace Terranet\Translator\Actions;

use Terranet\Administrator\Services\CrudActions;
use Terranet\Translator\Actions\Handlers\DeleteTranslation;
use Terranet\Translator\Actions\Handlers\SaveTranslations;

class Translations extends CrudActions
{
    public function canCreate()
    {
        return false;
    }

    public function actions()
    {
        return [
            DeleteTranslation::class,
        ];
    }

    public function batchActions()
    {
        return [
            SaveTranslations::class,
        ];
    }
}