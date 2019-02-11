<?php
/**
 * Created by PhpStorm.
 * User: fomvasss
 * Date: 13.11.18
 * Time: 1:09
 */

namespace Fomvasss\UrlAliases\Traits;

/**
 * Trait Localization
 *
 * Thank developers package
 * Mcamara\LaravelLocalization\LaravelLocalization
 *
 * @package Fomvasss\UrlAliases\Traits
 */
trait Localization
{

    /**
     * Returns default locale.
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        if (isset($this->defaultLocale)) {
            return $this->defaultLocale;
        }
        return $this->config->get('app.locale');
    }

    /**
     * Return an array of all supported Locales.
     *
     * @throws SupportedLocalesNotDefined
     *
     * @return array
     */
    public function getSupportedLocales()
    {
        if (!empty($this->supportedLocales)) {
            return $this->supportedLocales;
        }

        $locales = $this->config->get('url-aliases-laravellocalization.supportedLocales');

        if (empty($locales) || !\is_array($locales)) {
            throw new \Exception();
        }

        $this->supportedLocales = $locales;

        return $locales;
    }

    /**
     * Return an array of all supported Locales but in the order the user
     * has specified in the config file. Useful for the language selector.
     *
     * @return array
     */
    public function getLocalesOrder()
    {
        $locales = $this->getSupportedLocales();

        $order = $this->config->get('url-aliases-laravellocalization.localesOrder');

        uksort($locales, function ($a, $b) use ($order) {
            $pos_a = array_search($a, $order);
            $pos_b = array_search($b, $order);
            return $pos_a - $pos_b;
        });

        return $locales;
    }

    /**
     * Returns current locale name.
     *
     * @return string current locale name
     */
    public function getCurrentLocaleName()
    {
        return $this->supportedLocales[$this->getCurrentLocale()]['name'];
    }

    /**
     * Returns current locale native name.
     *
     * @return string current locale native name
     */
    public function getCurrentLocaleNative()
    {
        return $this->supportedLocales[$this->getCurrentLocale()]['native'];
    }

    /**
     * Returns current locale direction.
     *
     * @return string current locale direction
     */
    public function getCurrentLocaleDirection()
    {
        if (!empty($this->supportedLocales[$this->getCurrentLocale()]['dir'])) {
            return $this->supportedLocales[$this->getCurrentLocale()]['dir'];
        }

        switch ($this->getCurrentLocaleScript()) {
            // Other (historic) RTL scripts exist, but this list contains the only ones in current use.
            case 'Arab':
            case 'Hebr':
            case 'Mong':
            case 'Tfng':
            case 'Thaa':
                return 'rtl';
            default:
                return 'ltr';
        }
    }

    /**
     * Returns current locale script.
     *
     * @return string current locale script
     */
    public function getCurrentLocaleScript()
    {
        return $this->supportedLocales[$this->getCurrentLocale()]['script'];
    }

    /**
     * Returns current language's native reading.
     *
     * @return string current language's native reading
     */
    public function getCurrentLocaleNativeReading()
    {
        return $this->supportedLocales[$this->getCurrentLocale()]['native'];
    }

    /**
     * Returns current language.
     *
     * @return string current language
     */
    public function getCurrentLocale()
    {
        if ($this->currentLocale) {
            return $this->currentLocale;
        }
// TODO
//        if ($this->useAcceptLanguageHeader() && !$this->app->runningInConsole()) {
//            $negotiator = new LanguageNegotiator($this->defaultLocale, $this->getSupportedLocales(), $this->request);
//
//            return $negotiator->negotiateLanguage();
//        }

        // or get application default language
        return $this->config->get('app.locale');
    }

    /**
     * Returns current regional.
     *
     * @return string current regional
     */
    public function getCurrentLocaleRegional()
    {
        // need to check if it exists, since 'regional' has been added
        // after version 1.0.11 and existing users will not have it
        if (isset($this->supportedLocales[$this->getCurrentLocale()]['regional'])) {
            return $this->supportedLocales[$this->getCurrentLocale()]['regional'];
        } else {
            return;
        }
    }

    /**
     * Returns supported languages language key.
     *
     * @return array keys of supported languages
     */
    public function getSupportedLanguagesKeys()
    {
        return array_keys($this->supportedLocales);
    }

    /**
     * Check if Locale exists on the supported locales array.
     *
     * @param string|bool $locale string|bool Locale to be checked
     *
     * @throws SupportedLocalesNotDefined
     *
     * @return bool is the locale supported?
     */
    public function checkLocaleInSupportedLocales($locale)
    {
        $locales = $this->getSupportedLocales();
        if ($locale !== false && empty($locales[$locale])) {
            return false;
        }

        return true;
    }
}