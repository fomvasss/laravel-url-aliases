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


    /**
     * @param $query
     * @param null $locale
     * @return mixed
     */
    public function scopeByLocale($query, $locale = null)
    {
        $locale = $locale ?: \UrlAliasLocalization::getCurrentLocale();

        return $query->whereLocale($locale);
    }
    

    /**
     * @param $query
     * @param null $locales
     * @return mixed
     */
    public function scopeByLocales($query, $locales = null)
    {
        $locales = $locales ?: session('app_locales');

        if ($locales) {
            return $query->whereIn('locale', is_array($locales) ? $locales : [$locales]);
        }
    }

}