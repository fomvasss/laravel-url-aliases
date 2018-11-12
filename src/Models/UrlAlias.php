<?php

namespace Fomvasss\UrlAliases\Models;

use Illuminate\Database\Eloquent\Model;

class UrlAlias extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function getLocaleAliasAttribute()
    {
        return $this->attributes['locale'] . '/' . $this->attributes['alias'];
    }

    public function getLocaleSourceAttribute()
    {
        return $this->attributes['locale'] . '/' . $this->attributes['source'];
    }

    public function scopeByPath($query, $path)
    {
        return $query->where(function($q) use ($path) {
                $q->where('source', $path)->orWhere('alias', $path);
            });
    }
}
