<?php

namespace Terranet\Translator;

use Illuminate\Translation\LoaderInterface;

class Translator extends \Illuminate\Translation\Translator implements TranslatorInterface
{
    private $newLines = [];

    /**
     * @var TranslatorInterface|null
     */
    private $fallbackTranslator = null;

    public function __construct(LoaderInterface $loader, $locale, TranslatorInterface $fallbackTranslator = null)
    {
        parent::__construct($loader, $locale);

        $this->fallbackTranslator = $fallbackTranslator;
    }

    public function has($key, $locale = null, $fallback = true)
    {
        return parent::get($key, [], $locale, $fallback) !== $key;
    }

    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        list($namespace, $group, $item) = $this->parseKey($key);

        // Here we will get the locale that should be used for the language line. If one
        // was not passed, we will use the default locales which was given to us when
        // the translator was instantiated. Then, we can load the lines and return.
        $locales = $fallback ? $this->parseLocale($locale) : [$locale ?: $this->locale];

        foreach ($locales as $locale) {
            $this->load($namespace, $group, $locale);

            $line = $this->getLine(
                $namespace, $group, $locale, $item, $replace
            );

            if (! is_null($line)) {
                break;
            }
        }

        // If the line doesn't exist, we will return back the key which was requested as
        // that will be quick to spot in the UI if language keys are wrong or missing
        // from the application's language files. Otherwise we can return the line.
        if (! isset($line)) {
            if ($this->fallbackTranslator) {
                $key = $this->fallbackTranslator->get($key, $replace, $locales[0], $fallback);
            }

            $this->newLines[$locales[0]][$namespace][$group][$item] = $key;

            return $key;
        }

        return $line;
    }

    public function addNamespace($namespace, $hint)
    {
        parent::addNamespace($namespace, $hint);

        if ($this->fallbackTranslator) {
            $this->fallbackTranslator->addNamespace($namespace, $hint);
        }
    }

    public function getNewLines()
    {
        return $this->newLines;
    }
}