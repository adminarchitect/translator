<?php

namespace Terranet\Translator\Models;

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
}