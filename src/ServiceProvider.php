<?php

namespace Fomvasss\UrlAliases;

use Fomvasss\UrlAliases\Middleware\UrlAliasMiddleware;
use Illuminate\Contracts\Http\Kernel;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();

        $this->publishMigrations();

//        $this->registerMiddleware(UrlAliasMiddleware::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/url-aliases.php', 'url-aliases');
        $this->mergeConfigFrom(__DIR__.'/../config/url-aliases-laravellocalization.php', 'url-aliases-laravellocalization');

        $this->app->singleton(UrlAliasLocalization::class, function () {
            return new UrlAliasLocalization($this->app);
        });

        $this->app->singleton(UrlAlias::class, function () {
            return new UrlAlias($this->app);
        });
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/url-aliases.php' => config_path('url-aliases.php'),
            __DIR__ . '/../config/url-aliases-laravellocalization.php' => config_path('url-aliases-laravellocalization.php'),
        ], 'url-aliases-config');
    }

    protected function publishMigrations()
    {
        if (! class_exists('CreateUrlAliasesTable')) {
            $timestamp = date('Y_m_d_His', time());

            $migrationPath = __DIR__.'/../database/migrations/create_url_aliases_table.php';
                $this->publishes([$migrationPath => database_path('/migrations/' . $timestamp . '_create_url_aliases_table.php'),
            ], 'url-aliases-migrations');
        }
    }

    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }

    protected function checkMakeDir(string $path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path;
    }
}
