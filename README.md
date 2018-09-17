## Installation

Require this package with composer
```shell
composer require fomvasss/laravel-url-taxonomy
```

If you don't use auto-discovery (Laravel < 5.5), add the ServiceProvider to the providers array in config/app.php
```php
Fomvasss\UrlAliases\ServiceProvider::class,
```

Publish package resource
```shell
php artisan vendor:publish --provider="Fomvasss\UrlAliases\ServiceProvider"
```

Run:
```shell
php artisan migrate
```

## Usage

Add to your model trait: `Fomvasss\UrlAliases\Traits\UrlAliasable` 
This trait have the next relation-method:
-  `urlAlias()`
and Scope:
- `urla()`

```php
$article = Models\Article::find(2);
$article->urlAlias;
```

Add the middleware to `Http/Kernel.php`:
```php
    protected $middleware = [
        //...
        \Fomvasss\UrlAliases\Middleware\UrlAliasMiddleware::class,
    ];
```

### Helper functions:
- `route_alias()`

### Example:
```php
$article = \Article::find(1);
```

```blade
<a href="{{ route('system.article.show', $article) }}">System Link</a>
<a href="{{ url($article->urlAlias->aliased_path) }}">Alias Link</a>
<a href="{{ $article->urla() }}">Alias Link</a>
<a href="{{ route_alias('system.article.show', $article, ['qq' => '11']) }}">Link</a>
<a href="{{ route_alias('system.article.show', $article, ['page' => '3', 'per_page' => 15]) }}">Link</a>
```