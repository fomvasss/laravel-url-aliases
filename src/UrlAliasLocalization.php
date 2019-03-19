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

    public function getRoot()
    {
        return url($this->currentLocale);
    }
}