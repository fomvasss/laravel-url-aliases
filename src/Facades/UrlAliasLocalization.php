<?php

namespace Fomvasss\UrlAliases\Facades;

use Illuminate\Support\Facades\Facade;

class UrlAliasLocalization extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Fomvasss\UrlAliases\UrlAliasLocalization::class;
    }
}