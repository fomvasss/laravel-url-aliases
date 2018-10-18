<?php

if (!function_exists('route_alias')) {
    /**
     * Get URL-path (alias/system path) for entity.
     *
     * @param string $systemName
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    function route_alias(string $systemName, \Illuminate\Database\Eloquent\Model $entity, array $parameters = [], $absolute = true): string
    {
        if ($alias = optional($entity->urlAlias)->aliased_path) {
            if (count($parameters)) {
                $alias = $alias.'?'.http_build_query($parameters);
            }
            return $absolute ? url($alias) : $alias;
        }

        return route($systemName, array_merge([$entity], $parameters), $absolute);
    }
}


if (!function_exists('clear_url_path')) {
    /**
     * Clear URL-path.
     *
     * @param string $str
     * @return array
     */
    function clear_url_path(string $url): string
    {
        $root = request()->root();
        $path = str_replace($root, '', $url);

        return trim($path, '\/');
    }
}

