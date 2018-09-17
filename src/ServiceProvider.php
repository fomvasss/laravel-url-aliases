<?php

namespace Fomvasss\UrlAliases;

use Fomvasss\UrlAliases\Middleware\UrlAliasMiddleware;
use Illuminate\Contracts\Http\Kernel;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishedConfig();

        $this->makeMigrations();

        $this->makeSeeder();
        
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
    }

    protected function publishedConfig()
    {
        $this->publishes([__DIR__ . '/../config/url-aliases.php' => config_path('url-aliases.php')
        ], 'url-aliases-config');
    }

    protected function makeSeeder()
    {
        $seedPath = __DIR__ . '/../database/seeds/UrlAliasesTableSeeder.php.stub';
        $this->publishes([$seedPath => database_path('seeds/UrlAliasesTableSeeder.php')
            ], 'url-aliases-seeder');
    }

    protected function makeMigrations()
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
