<?php

if (!function_exists('route_alias')) {
    /**
     *
     * Get URL-path (alias/system path) for entity.
     * 
     * @param string $systemName
     * @param array $parameters [\Illuminate\Database\Eloquent\Model]
     * @param bool $absolute
     * @return string
     */
    function route_alias(string $systemName, $parameters = [], $absolute = true): string
    {
        $parameters = array_wrap($parameters);

        if (! empty($parameters[0]) && ($parameters[0] instanceof \Illuminate\Database\Eloquent\Model)) {
            $entity = $parameters[0];
            if ($alias = optional($entity->urlAlias)->aliased_path) {
                unset($parameters[0]);
                if (count($parameters)) {
                    $alias = $alias . '?' . http_build_query($parameters);
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

