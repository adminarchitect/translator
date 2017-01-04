<?php

namespace Terranet\Translator\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $primaryKey = ['locale', 'key'];

    public $incrementing = false;

    protected $fillable = ['locale', 'key', 'value'];

    public $timestamps = false;

    public function locales()
    {
        return $this->hasMany(static::class, 'key', 'key');
    }

    /** Stupid Eloquent */
    protected function setKeysForSaveQuery(Builder $query)
    {
        foreach((array) $this->getKeyName() as $key) {
            $query->where($key, '=', $this->getSingleKeyForSaveQuery($key));
        }

        return $query;
    }

    protected function getSingleKeyForSaveQuery($key)
    {
        if (isset($this->original[$key])) {
            return $this->original[$key];
        }

        return $this->getAttribute($key);
    }
}