<?php

namespace Fomvasss\UrlAliases\Traits;

trait UrlAliasable
{
    use LocaleScopes;

    /**
     * @return mixed
     */
    public function urlAlias()
    {
        $model = config('url-aliases.model', \Fomvasss\UrlAliases\Models\UrlAlias::class);

        return $this->morphOne($model, 'model');
    }

    /**
     * @return mixed
     */
    public function urlAliases()
    {
        $model = config('url-aliases.model', \Fomvasss\UrlAliases\Models\UrlAlias::class);

        return $this->morphMany($model, 'model');
    }

    /**
     * @return |null
     */
    public function getLocaleboundStr()
    {
        if ($this->urlAlias) {
            return $this->urlAlias->locale_bound;
        } 
        
        return null;
    }
}