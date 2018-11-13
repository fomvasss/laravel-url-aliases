<?php

namespace Fomvasss\UrlAliases\Middleware;

use Closure;
use Fomvasss\UrlAliases\UrlAliasLocalization;
use Illuminate\Http\Request;

class UrlAliasMiddleware
{

    const ALIAS_REQUEST_URI_KEY = 'ALIAS_REQUEST_URI';
    
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

        if ($this->isAvailableMethod($request) && $this->isAvailableCheckPath($request)) {

            if ($useLocalization = $this->config->get('url-aliases.use_localization')) {
                $localization = $this->app->make(UrlAliasLocalization::class);

                // TODO: remove $segment1 in params next function
                $path = $localization->prepareLocalizePath($request->path(), $request->segment(1));
                if ($path instanceof \Illuminate\Http\RedirectResponse) {
                    return $path;
                }
            } else {
                $path = $request->path();
            }

            $urlModels = $this->getByPath($path);

            // If visited source - system path
            if ($urlModel = $urlModels->where('source', $path)->first()) {

                $redirectStatus = $this->config->get('url-aliases.redirect_for_system_path', 301) == 301 ?:302;

                // Redirect to alias path
                $params = count($request->all()) ? '?' . http_build_query($request->all()) : '';

                return redirect(url($urlModel->localeAlias) . $params, $redirectStatus);

            // If visited alias
            } elseif ($urlModel = $urlModels->where('alias', $path)->where('locale', $this->app->getLocale())->first()) {

                // Redirect to source
                if ($redirect = $this->isTypeRedirect($urlModel)) {
                    return $redirect;
                }

                // Make new request
                $newRequest = $this->makeNewRequest($request, $urlModel);
                
                return $next($newRequest);
                
            // Check if isset facet in current url and find aliased path without facet
            } elseif ($customReturn = $this->customize($request, $next)) {
                return $customReturn;
            }
        }
        
        return $next($request);
    }

    /**
     * Remake request
     * @param Request $request
     * @param $urlModel
     * @return Request
     */
    protected function makeNewRequest(Request $request, $urlModel, $getParams = [])
    {
        $newRequest = $request;
        $newRequest->server->set('REQUEST_URI', $urlModel->source);
        $newRequest->initialize(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $newRequest->server->all() + [static::ALIAS_REQUEST_URI_KEY => $request->path()],
//            $request->server->all(),
            $request->getContent()
        );

//          $request = \Request::create($systemPath, 'GET');
//          return $response = \Route::dispatch($request);

        $newRequest->merge($getParams);

        return $newRequest;
    }

    public function customize($request, Closure $next)
    {
       //...
        return $next($request);
    }

    /**
     * If type redirect - redirect.
     * @param $urlModel
     * @return bool|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function isTypeRedirect($urlModel)
    {
        if (in_array($urlModel->type, [301, 302])) {
            return redirect(url($urlModel->localeSource), $urlModel->type);
        }

        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isAvailableCheckPath(Request $request)
    {
        if ($request->is(...$this->config->get('url-aliases.ignore_paths', []))) {
            return false;
        }
        
        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isAvailableMethod(Request $request)
    {
        if (in_array($request->getMethod(), $this->config->get('url-aliases.available_methods', [])) || empty($this->config->get('url-aliases.available_methods', []))) {
            return true;
        }
        
        return false;
    }

    /**
     * @param $path
     * @return mixed
     */
    protected function getByPath($path)
    {
        $model = $this->config->get('url-aliases.model', \Fomvasss\UrlAliases\Models\UrlAlias::class);
        
        return $model::byPath($path)->get();
    }
}
