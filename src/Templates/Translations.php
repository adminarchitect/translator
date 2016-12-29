<?php

namespace Terranet\Translator\Templates;

use Terranet\Administrator\Contracts\Services\TemplateProvider;
use Terranet\Administrator\Services\Template;

class Translations extends Template implements TemplateProvider
{
    public function index($partial = 'index')
    {
        $partials = array_merge(parent::index(null), [
            'index' => $this->customPartial('index'),
            'row' => $this->customPartial('row'),
            'scripts' => $this->customPartial('scripts'),
        ]);

        return $partial ? $partials[$partial] : $partials;
    }

    protected function customPartial($partial)
    {
        return 'administrator.translations.' . $partial;
    }
}