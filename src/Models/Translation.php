<?php

namespace Terranet\Translator\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['locale', 'key', 'value'];

    public function locales()
    {
        return $this->hasMany(static::class, 'key', 'key');
    }
}