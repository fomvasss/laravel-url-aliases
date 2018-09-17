<?php

if (!function_exists('route_alias')) {
    /**
     * Получить url-путь (алиас/системный путь) для сущности
     * @param string $str
     * @return array
     */
    function route_alias(string $systemName, \Illuminate\Database\Eloquent\Model $entity, array $parameters = []): string
    {
        if ($alias = optional($entity->urlAlias)->aliased_path) {
            if (count($parameters)) {
                return url($alias.'?'.http_build_query($parameters));
            }
            return url($alias);
        }

        return route($systemName, array_merge([$entity], $parameters));
    }
}


if (!function_exists('clear_url_path')) {
    /**
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

