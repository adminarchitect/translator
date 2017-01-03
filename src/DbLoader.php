<?php

namespace Terranet\Translator;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Translation\LoaderInterface;

class DbLoader implements LoaderInterface
{
    /**
     * @var Repository
     */
    private $cache = null;

    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public function load($locale, $group, $namespace = null)
    {
        $prefix = $this->getPrefix($group, $namespace);

        $cacheNamespace = $this->getCacheNamespace($locale, $prefix);

        if (!$this->cache->has($cacheNamespace)) {
            $translates = $this->loadFromDb($locale, $group, $namespace);

            $this->cache->forever($cacheNamespace, $translates);

            return $translates;
        }

        $translates = $this->cache->get($cacheNamespace);

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

        $this->cache->forget($this->getCacheNamespace($locale, $prefix));

        return $this;
    }

    public function flush()
    {
        $this->cache->getStore()->flush();

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