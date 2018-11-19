<?php

namespace Fomvasss\UrlAliases\Middleware;

use Closure;
use Fomvasss\UrlAliases\UrlAliasLocalization;
use Illuminate\Http\Request;

class UrlAliasMiddleware
{

    const ALIAS_REQUEST_URI_KEY = 'ALIAS_REQUEST_URI';

    /**
     * @var
     */
    protected $config;

    /**
     * @var bool
     */
    protected $useLocalization = false;

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

            $path = $request->path();

            // Check lovalization support
            if ($this->useLocalization = $this->config->get('url-aliases.use_localization') && $this->isAvailableLocalizationPath($request)) {
                $localization = $this->app->make(UrlAliasLocalization::class);

                $localizationResult = $localization->prepareLocalizePath($request->path());

                if (isset($localizationResult['redirect'])) {
                    $params = count($request->all()) ? '?' . http_build_query($request->all()) : '';
                    return redirect()->to($localizationResult['redirect'] . $params, 301); // hide default locale in URL
                } elseif (isset($localizationResult['path'])) {
                    $path = $localizationResult['path'];
                }
            }

            $urlModels = $this->getByPath($path);

            // If visited source - system path
            if ($urlModel = $urlModels->where('source', $path)->where('locale', $this->app->getLocale())->first()/* && $path != 'de'*/) {

                $redirectStatus = $this->config->get('url-aliases.redirect_for_system_path', 301) == 301 ? 301 : 302;

                // Redirect to alias path
                $params = count($request->all()) ? '?' . http_build_query($request->all()) : '';

                if ($this->useLocalization) {
                    return redirect()->to(url($urlModel->localeAlias) . '/' . $params, $redirectStatus);
                }

                return redirect()->to(url($urlModel->alias) . '/' . $params, $redirectStatus);

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
            if ($this->useLocalization) {
                return redirect(url($urlModel->localeSource), $urlModel->type);
            }

            return redirect()->to(url($urlModel->source), $urlModel->type);
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

    protected function isAvailableLocalizationPath(Request $request)
    {
        if ($request->is(...$this->config->get('url-aliases-laravellocalization.urlsIgnored', []))) {
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
