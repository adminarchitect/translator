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
            $translates[Str::substr($key, $prefixLength)] = $value;
            /*
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $translates[Str::substr($key, $prefixLength)] = $decoded;
            } else {
                $translates[Str::substr($key, $prefixLength)] = $value;
            }
            */
        }

        $translates = static::unpackIndexes($translates);

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

        $lines = static::packIndexes($lines);

        foreach ($lines as $key => $value) {
            try {
                DB::table('translations')->insert([
                    'locale' => $locale,
                    'key' => $prefix . $key,
                    'value' => $value,
                    //'value' => is_array($value) ? json_encode($value) : $value,
                ]);
            } catch (\Exception $e) {
                //
            }
        }

        $this->cache->forget($this->getCacheNamespace($locale, $prefix));

        return $this;
    }

    public function clearCache($locale, $namespace, $group)
    {
        $prefix = $this->getPrefix($group, $namespace);

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

    protected static function packIndexes(array &$array, $delimiter = '.')
    {
        $new = [];

        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                foreach (static::packIndexes($value) as $k => $v) {
                    $new[$key . $delimiter . $k] = $v;
                }
            } else {
                $new[$key] = $value;
            }
        }
        unset($value);

        return $new;
    }

    protected static function unpackIndexes(array &$array, $delimiter = '.')
    {
        $new = [];

        foreach ($array as $index => &$value) {
            $sub = static::unpackToIndexPath($new, $index, $delimiter);

            $sub[0] = (array) $sub[0];

            static::set($sub[0], $sub[1], $value);
        }
        unset($value);

        return $new;
    }

    protected static function unpackToIndexPath(&$array, $index, $delimiter = '.')
    {
        $sub = &$array;

        $indexes = explode($delimiter, $index);

        $lastKey = array_pop($indexes);

        foreach ($indexes as $k) {
            if ($k === '') {
                $sub = &$sub[];
            } else {
                if (is_array($sub) and static::has($sub, $k) and !is_array($sub[$k])) {
                    $sub[$k] = [];
                }

                $sub = &$sub[$k];
            }
        }

        return [&$sub, $lastKey];
    }

    protected static function set(array &$array, $key, $value)
    {
        if ($key === null or $key === '') {
            $array[] = $value;
        } else {
            $array[$key] = $value;
        }

        return $array;
    }

    protected static function has(array &$array, $key)
    {
        if (is_array($key)) {
            foreach (($keys = $key) as $k) {
                if (!static::has($array, $k)) {
                    return false;
                }
            }

            return true;
        }

        return array_key_exists($key, $array);
    }
}