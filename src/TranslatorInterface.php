<?php

namespace Terranet\Translator;

interface TranslatorInterface
{
    public function get($key, array $replace = [], $locale = null, $fallback = true);

    public function addNamespace($namespace, $hint);
}