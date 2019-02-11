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
     * Get url-alias.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function urlA()
    {
        return url(optional($this->urlAlias)->alias ?? config('url-aliases.url_a_is_empty', '/'));
    }

    /**
     * Get locale url-alias.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function urlLA()
    {
        return url(optional($this->urlAlias)->localeAlias ?? config('url-aliases.url_a_is_empty', '/'));
    }
}