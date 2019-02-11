<?php

namespace Fomvasss\UrlAliases\Middleware;

use Closure;

class ApplyUrlLocaleToRootPage
{
    protected $app;

    protected $config;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->app = app();
        $this->config = $this->app['config'];

        $supportedLocales = $this->config->get('url-aliases-laravellocalization.supportedLocales');

        if (empty($request->segment(1)) || count($request->segments()) == 1 && in_array($request->segment(1), array_keys($supportedLocales))) {
            return $next($request);
        }

        abort(404);
    }
}
