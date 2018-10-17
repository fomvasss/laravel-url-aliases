<?php

namespace Fomvasss\UrlAliases\Middleware;

use Closure;
use Illuminate\Http\Request;

class UrlAliasMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->isAvailableMethod($request) && $this->isAvailableCheckPath($request)) {
            $path = $request->path();
            $urlModels = $this->getByPath($path);

            // is system_path
            if ($urlModel = $urlModels->where('system_path', $path)->first()) {
                $redirectStatus = config('url-aliases.redirect_for_system_path', 301);

                // redirect in alias
                if (in_array($redirectStatus, ['301', '302'])) {
                    $params = count($request->all()) ? '?'.http_build_query($request->all()) : '';
                    return redirect(url($urlModel->aliased_path).$params, $redirectStatus);
                }

            // is aliased_path
            } elseif ($urlModel = $urlModels->where('aliased_path', $path)->first()) {
                if ($redirect = $this->isTypeRedirect($urlModel)) {
                    return $redirect;
                }

                $newRequest = $this->makeNewRequest($request, $urlModel);
                return $next($newRequest);
                
            // check if isset facet in current url and find aliased path without facet
            } elseif ($customReturn = $this->customize($request, $next)) {
                return $customReturn;
            }
        }
        return $next($request);
    }

    /**
     * Создание нового request'a
     * @param Request $request
     * @param $urlModel
     * @return Request
     */
    protected function makeNewRequest(Request $request, $urlModel, $getParams = [])
    {
        $newRequest = $request;
        $newRequest->server->set('REQUEST_URI', $urlModel->system_path);
        $newRequest->initialize(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $newRequest->server->all(),
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
            return redirect(url($urlModel->system_path), $urlModel->type);
        }

        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isAvailableCheckPath(Request $request)
    {
        if ($request->is(...config('url-aliases.ignore_paths', []))) {
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
        if (in_array($request->getMethod(), config('url-aliases.available_mathods', [])) || empty(config('url-aliases.available_mathods', []))) {
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
        $model = config('url-aliases.model', \Fomvasss\UrlAliases\Models\UrlAlias::class);
        
        return $model::byPath($path)->get();
    }
}
