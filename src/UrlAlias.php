<?php
/**
 * Created by PhpStorm.
 * User: fomvasss
 * Date: 11.02.19
 * Time: 21:51
 */

namespace Fomvasss\UrlAliases;

class UrlAlias
{
    protected $app;

    protected $config;
    
    /**
     * UrlAlias constructor.
     *
     * @param $app
     * @throws \Exception
     */
    public function __construct($app = null)
    {
        if (!$app) {
            $app = app();   //Fallback when $app is not given
        }
        $this->app = $app;

        $this->config = $this->app['config'];
    }
    
    public function route(string $systemName, $parameters = [], $absolute = true, $forceWithLocalePreffix = false): string
    {
        $parameters = array_wrap($parameters);

        if (! empty($parameters[0]) && ($parameters[0] instanceof \Illuminate\Database\Eloquent\Model)) {
            $entity = $parameters[0];
            if ($entity->urlAlias) {
                unset($parameters[0]);

                if (! $forceWithLocalePreffix && $this->config->get('app.locale') == $entity->urlAlias->locale && $this->config->get('url-aliases-laravellocalization.hideDefaultLocaleInURL')) {
                    $alias = $entity->urlAlias->alias;
                } else {
                    $alias = $entity->urlAlias->localeAlias;
                }

                if (count($parameters)) {
                    $alias .= '?' . http_build_query($parameters);
                }

                return $absolute ? url($alias) : $alias;
            }
        }

        return route($systemName, $parameters, $absolute);
    }

    public function current($absolute = true): string
    {
        $path = request()->server('ALIAS_REQUEST_URI', request()->path());

        return $absolute ? url($path) : $path;
    }
}