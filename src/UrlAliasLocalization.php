<?php

namespace Fomvasss\UrlAliases;

use Fomvasss\UrlAliases\Traits\Localization;

class UrlAliasLocalization
{
    use Localization;

    protected $app;
    
    protected $config;
    
    protected $defaultLocale;

    protected $supportedLocales;

    protected $currentLocale;

    /**
     * Localization constructor.
     * @param $app
     */
    public function __construct($app)
    {
        if (!$app) {
            $app = app();   //Fallback when $app is not given
        }
        $this->app = $app;

        $this->config = $this->app['config'];

        $this->defaultLocale = $this->getDefaultLocale();

        $this->config->set('url-aliases-laravellocalization.origin_default_locale', $this->defaultLocale); // virtual temporary config

        $this->currentLocale = $this->defaultLocale;

        $this->supportedLocales = $this->getSupportedLocales();

        if (empty($this->supportedLocales[$this->defaultLocale])) {
            throw new \Exception('Laravel default locale is not in the supportedLocales array.');
        }
    }

    /**
     * TODO: remove $segment1
     * @param $path
     * @param $segment1
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function prepareLocalizePath(string $path): array
    {
//        session()->put('locale', $this->defaultLocale);
        $segment1 = url_path_segments($path, 1);

        if (key_exists($segment1, $this->supportedLocales)) {
//            session()->put('locale', $segment1);

            $path = preg_replace("/^$segment1\\//", "", "$path");

            if ($this->hideDefaultLocaleInURL() && $segment1 === $this->defaultLocale) {
                $path = ($path == $segment1) ? '/' : $path;
                return ['redirect' => $path];
            }

            $this->currentLocale = $segment1;
            $this->app->setLocale($segment1);
        }

        // Regional locale such as de_DE, so formatLocalized works in Carbon
        $regional = $this->getCurrentLocaleRegional();
        $suffix = $this->config->get('url-aliases.laravellocalization.utf8suffix');
        if ($regional) {
            setlocale(LC_TIME, $regional . $suffix);
            setlocale(LC_MONETARY, $regional . $suffix);
        }

        return ['path' => $path];
    }

    /**
     * @return bool
     */
    protected function hideDefaultLocaleInURL()
    {
        return $this->config->get('url-aliases-laravellocalization.hideDefaultLocaleInURL');
    }

    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getRoot()
    {
        return url($this->currentLocale);
    }

    /**
     * @param string $default
     * @param bool $absolute
     * @return array
     */
    public function getCurrentBound(string $default = '', $absolute = true)
    {
        $bound = request()->server('ALIAS_LOCALE_BOUND');

        $modelClass = $this->config->get('url-aliases.model', \Fomvasss\UrlAliases\Models\UrlAlias::class);
        $aliasModels = $modelClass::whereNotNull('locale_bound')->where('locale_bound', $bound)->get();

        $res = $this->supportedLocales;
        foreach ($this->supportedLocales as $key => $item) {
            if ($modelClass = $aliasModels->where('locale', $key)->first()) {
                $link = $modelClass->localeAlias;
            } elseif($default) {
                $link = $default;
            } else {
                $link = $key;
            }
            $res[$key]['url'] = $absolute ? url($link) : $link;
        }

        return $res;
    }

    /**
     * @param null $bound
     * @param bool $absolute
     * @return array
     */
    public function getLocalesModelsBound($bound = null, $absolute = true)
    {
        $modelClass = $this->config->get('url-aliases.model', \Fomvasss\UrlAliases\Models\UrlAlias::class);
        $aliasModels = $modelClass::whereNotNull('locale_bound')->where('locale_bound', $bound)->get();

        $res = $this->supportedLocales;
        foreach ($this->supportedLocales as $key => $item) {
            $res[$key]['model'] = null;
            if ($model = $aliasModels->where('locale', $key)->first()) {
                $link = $model->localeAlias;
                $res[$key]['model'] = $model->aliasable;
            } else {
                $link = $key;
            }
            $res[$key]['url'] = $absolute ? url($link) : $link;
        }

        return $res;
    }

    /**
     * @param string $localeKey
     * @param string|null $bound
     * @return mixed|null
     */
    public function getLocaleModelBound(string $localeKey, ?string $bound = null)
    {
        if ($bound) {
            $modelClass = $this->config->get('url-aliases.model', \Fomvasss\UrlAliases\Models\UrlAlias::class);

            return optional($modelClass::whereNotNull('locale_bound')
                ->where('locale_bound', $bound)
                ->where('locale', $localeKey)
                ->first())->aliasable;
        }

        return null;
    }
}