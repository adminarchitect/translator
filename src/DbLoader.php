<?php

namespace Terranet\Translator;

use Illuminate\Cache\CacheManager;
use Illuminate\Support\Str;
use Illuminate\Translation\LoaderInterface;
use Terranet\Translator\Models\Translation;

class DbLoader implements LoaderInterface
{
    /**
     * @var Translation
     */
    private $model = null;

    /**
     * @var CacheManager
     */
    private $cache = null;

    public function __construct(Translation $model, CacheManager $cache)
    {
        $this->model = $model;

        $this->cache = $cache;
    }

    public function load($locale, $group, $namespace = null)
    {
        $prefix = $this->getPrefix($group, $namespace);

        if (!$this->cache->has('key')) {
            $translates = $this->loadFromDb($locale, $group, $namespace);

            $this->cache->put($this->getCacheNamespace($locale, $prefix), $translates);

            return $translates;
        }

        return $this->cache->get($this->getCacheNamespace($locale, $prefix));
    }

    protected function loadFromDb($locale, $group, $namespace = null)
    {
        $prefix = $this->getPrefix($group, $namespace);

        $items = $this->model
            ->where('locale', $locale)
            ->where('key', 'like', str_replace('%', '%%', $prefix) . '%')
            ->pluck('value', 'key')
            ->toArray();

        $prefixLength = mb_strlen($prefix);

        $translates = [];

        foreach ($items as $key => $value) {
            $translates[Str::substr($key, $prefixLength)] = $value;
        }

        return $translates;
    }

    public function addNamespace($namespace, $hint) { }

    public function save(array $locales)
    {
        foreach ($locales as $locale => $namespaces) {
            $this->saveNamespaces($locale, $namespaces);
        }

        return $this;

    }

    public function saveNamespaces($locale, array $namespaces)
    {
        foreach($namespaces as $namespace => $groups) {
            $this->saveGroups($locale, $namespace, $groups);
        }

        return $this;

    }

    public function saveGroups($locale, $namespace, array $groups)
    {
        foreach($groups as $group => $lines) {
            $this->saveLines($locale, $namespace, $group, $lines);
        }

        return $this;

    }

    public function saveLines($locale, $namespace, $group, array $lines)
    {
        $prefix = $this->getPrefix($group, $namespace);

        foreach ($lines as $key => $value) {
            $this->model->create([
                'locale' => $locale,
                'key' => $prefix . $key,
                'value' => $value,
            ]);
        }

        $this->clearCache($locale, $namespace, $group);

        return $this;
    }

    public function clearCache($locale, $namespace, $group)
    {
        $this->cache->forget($this->getCacheNamespace($locale, $this->getPrefix($group, $namespace)));

        return $this;
    }

    protected function getPrefix($group, $namespace = null)
    {
        $prefix = $group . '.';

        if (!(is_null($namespace) || $namespace == '*')) {
            $prefix = $namespace . '::' . $prefix;
        }

        return $prefix;
    }

    protected function getCacheNamespace($locale, $prefix)
    {
        return 'translations:' . $locale . ':' . $prefix;
    }
}