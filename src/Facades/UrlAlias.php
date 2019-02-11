<?php

namespace Fomvasss\UrlAliases\Facades;

use Illuminate\Support\Facades\Facade;

class UrlAlias extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Fomvasss\UrlAliases\UrlAlias::class;
    }
}