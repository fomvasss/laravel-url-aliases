<?php
/**
 * Created by PhpStorm.
 * User: fomvasss
 * Date: 21.08.18
 * Time: 11:34
 */

namespace Fomvasss\UrlAliases\Traits;

trait UrlAliasable
{
    public function urlAlias()
    {
        $model = config('url-aliases.model', \Fomvasss\UrlAliases\Models\UrlAlias::class);

        return $this->morphOne($model, 'model');
    }
    
    public function scopeUrla()
    {
        return $this->urlAlias ? url($this->urlAlias->aliased_path) : abort(404);
    }
}