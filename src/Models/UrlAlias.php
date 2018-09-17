<?php

namespace Fomvasss\UrlAliases\Models;

use Illuminate\Database\Eloquent\Model;

class UrlAlias extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function scopeByPath($query, $path)
    {
        return $query->where(function($q) use ($path) {
            $q->where('aliased_path', $path)->where('system_path', '<>', null);
        })->orWhere(function($q) use ($path) {
            $q->where('system_path', $path)->where('aliased_path', '<>', null);
        });
    }
}
