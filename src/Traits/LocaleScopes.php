<?php

namespace Fomvasss\UrlAliases\Traits;

trait LocaleScopes
{

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