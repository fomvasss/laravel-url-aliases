<?php

namespace Fomvasss\UrlAliases\Models;

use Illuminate\Database\Eloquent\Model;

class UrlAlias extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected static function boot()
    {
        static::creating(function (Model $model) {
            if (! $model->locale) {
                $model->locale = app()->app['config']->get('app.locale');
            }
        });

        parent::boot();
    }

    /**
     * @return string
     */
    public function getLocaleAliasAttribute()
    {
        if (is_url($this->attributes['alias']) || $this->attributes['locale'] == $this->attributes['alias']) {
            return $this->attributes['alias'];
        }

        return $this->attributes['locale'] . '/' . $this->attributes['alias'];
    }

    /**
     * @return string
     */
    public function getLocaleSourceAttribute()
    {
        if (is_url($this->attributes['source']) || $this->attributes['locale'] == $this->attributes['alias']) {
            return $this->attributes['source'];
        }
        return $this->attributes['locale'] . '/' . $this->attributes['source'];
    }

    /**
     * @param $query
     * @param $path
     * @return mixed
     */
    public function scopeByPath($query, $path)
    {
        return $query->where(function($q) use ($path) {
                $q->where('source', $path)->orWhere('alias', $path);
            });
    }

    /**
     * @param $value
     */
    public function setAliasAttribute($value)
    {
        $this->attributes['alias'] = $value == '/' ? $value : trim($value, '/');
    }

    /**
     * @param $value
     */
    public function setSourceAttribute($value)
    {
        $this->attributes['source'] = $value == '/' ? $value : trim($value, '/');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function aliasable()
    {
        return $this->morphTo('aliasable', 'model_type', 'model_id');
    }
}
