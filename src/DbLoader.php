<?php

namespace Terranet\Translator;

use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Translation\LoaderInterface;

class DbLoader implements LoaderInterface
{
    /**
     * @var CacheManager
     */
    private $cache = null;

    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    public function load($locale, $group, $namespace = null)
    {
        $prefix = $this->getPrefix($group, $namespace);

        if (!$this->cache->has('key')) {
            $translates = $this->loadFromDb($locale, $group, $namespace);

            $this->cache->forever($this->getCacheNamespace($locale, $prefix), $translates);

            return $translates;
        }

        $translates = $this->cache->get($this->getCacheNamespace($locale, $prefix));

        return $translates;
    }

    protected function loadFromDb($locale, $group, $namespace = null)
    {
        $prefix = $this->getPrefix($group, $namespace);

        $items = DB::table('translations')
            ->where('locale', $locale)
            ->where('key', 'like', str_replace('%', '%%', $prefix) . '%')
            ->pluck('value', 'key')
            ->toArray();

        $prefixLength = mb_strlen($prefix);

        $translates = [];

        foreach ($items as $key => $value) {
            $decoded = json_decode($value);

            if (json_last_error() === JSON_ERROR_NONE) {
                $translates[Str::substr($key, $prefixLength)] = $decoded;
            } else {
                $translates[Str::substr($key, $prefixLength)] = $value;
            }
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
            DB::table('translations')->insert([
                'locale' => $locale,
                'key' => $prefix . $key,
                'value' => is_array($value) ? json_encode($value) : $value,
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