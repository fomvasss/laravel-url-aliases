<?php

if (!function_exists('route_alias')) {
    /**
     * Get URL-path (alias/system path) for entity.
     *
     * @param string $systemName
     * @param array $parameters
     * @param bool $absolute
     * @param bool $forceWithLocalePreffix
     * @return string
     */
    function route_alias(string $systemName, $parameters = [], $absolute = true, $forceWithLocalePreffix = false): string
    {
        $parameters = array_wrap($parameters);

        if (! empty($parameters[0]) && ($parameters[0] instanceof \Illuminate\Database\Eloquent\Model)) {
            $entity = $parameters[0];
            if ($entity->urlAlias) {
                unset($parameters[0]);

                if (! $forceWithLocalePreffix && config('app.locale') == $entity->urlAlias->locale && config('url-aliases-laravellocalization.hideDefaultLocaleInURL')) {
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
}


if (!function_exists('url_alias_current')) {
    /**
     * Get current url alias path or system path.
     *
     * @param string $str
     * @return array
     */
    function url_alias_current($absolute = true): string
    {
        $path = request()->server('ALIAS_REQUEST_URI', request()->path());
        
        return $absolute ? url($path) : $path;
    }
}

if (!function_exists('array_wrap')) {
    /**
     * @param $value
     * @return array
     */
    function array_wrap($value)
    {
        if (is_null($value)) {
            return [];
        }

        return ! is_array($value) ? [$value] : $value;
    }
}

if (!function_exists('is_url')) {
    /**
     * Check the string is url.
     *
     * @param string $str
     * @return mixed
     */
    function is_url(string $str)
    {
        return filter_var($str, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }
}

if (!function_exists('prepare_url_path')) {
    /**
     * Remove sheme & current domain from url.
     *
     * @param string $str
     * @return mixed
     */
    function prepare_url_path(string $url = null)
    {
        $path = parse_url($url)['path'] ?? '/';

        // TODO: to replace request()->root()
        if ($path === '/' || $url === '/' || $url === request()->root()) {
            return '/';
        } elseif ($path) {
            return trim($path, '/');
        }

        return null;
    }
}

if (!function_exists('url_path_segments')) {
    /**
     * @param string $path
     * @param null $index
     * @return mixed
     */
    function url_path_segments(string $path, int $index = null)
    {
        $segments = explode('/', $path);

        $array = array_values(array_filter($segments, function ($value) {
            return $value !== '';
        }));

        if ($index) {
            return $array[$index - 1] ?? '';
        }

        return $array;
    }
}

