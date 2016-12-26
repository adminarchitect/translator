<?php

namespace Terranet\Translator\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['locale', 'group', 'name', 'value', 'viewed_at'];
}