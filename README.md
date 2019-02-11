# Laravel URL Aliases

[![License](https://img.shields.io/packagist/l/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://packagist.org/packages/fomvasss/laravel-url-aliases)
[![Build Status](https://img.shields.io/github/stars/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://github.com/fomvasss/laravel-url-aliases)
[![Latest Stable Version](https://img.shields.io/packagist/v/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://packagist.org/packages/fomvasss/laravel-url-aliases)
[![Total Downloads](https://img.shields.io/packagist/dt/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://packagist.org/packages/fomvasss/laravel-url-aliases)
[![Quality Score](https://img.shields.io/scrutinizer/g/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://scrutinizer-ci.com/g/fomvasss/laravel-url-aliases)

## Installation

__Branch 1.0.* is DEPRECATED!!! Use 2.*__

Require this package with composer
```shell
composer require fomvasss/laravel-url-aliases
```

If you don't use auto-discovery (Laravel < 5.5), add the ServiceProvider to the providers array in config/app.php
```php
Fomvasss\UrlAliases\ServiceProvider::class,
```

Publish package resource:
```shell
php artisan vendor:publish --provider="Fomvasss\UrlAliases\ServiceProvider"
```
- config
- migration
- test seeder

Run migrate:
```shell
php artisan migrate
```

## Usage

### Model

Add to your model trait: `Fomvasss\UrlAliases\Traits\UrlAliasable` 

This trait have the next relation-method:
-  `urlAlias()` //return model UrlAlias
-  `urlAliases()` //return models UrlAliases
and Scope for your model:
- `urlA()`      // return string url (alias - first!)

__Do not forget use `with('urlAlias')` in your models!__

Add the middleware to `Http/Kernel.php`:
```php
    protected $middleware = [
        //...
        \Fomvasss\UrlAliases\Middleware\UrlAliasMiddleware::class,
    ];
```

### Helper functions

- `route_alias()` - works the same way as Laravel helper `route()`
- `url_alias_current()` - return alias path (or system path if alias not exists)

### Blade directive

- @urlAliasCurrent()

### Example:


- `routes/web.php`:

```php
Route::group(['prefix' => 'system', 'as' => 'system'], function () {
Route::get('article', 'ArticleController@index')->name('article.index');
Route::get('article/{id}', 'ArticleController@show')->name('article.show');
Route::post('article', 'ArticleController@store')->name('article.store');
});
```

- `app/Http/Controllers/ArticleController.php`:

```php

public function index(Request $request)
{
    $articles = Models\Article::paginate($request->per_page);
    
    return view('article.index', compact('articles'));
}


public function store(Request $request)
{
    $article = Models\Article::create($request->only([
        //...
    ]);
    
    $article->urlAlias()->create([
        'system_path' => trim(route('system.article.show', $article, false), '/'),
        'aliased_path' => str_slug($article->title).'/'.str_slug($article->user->name), // must be unique!
    ]);

    return redirect()->route('system.article.index');
}

public function show(Request $request, $id)
{
    $article = Models\Article::findOrFail($id);

    // $article->urlAlias;
    // $article->urlA();
   
    return view('article.show', compact('article'));
}
```

```blade
<li><a href="{{ route_alias('system.article.index', ['page' => '3', 'per_page' => 15]) }}">All articles</a></li>
<li><a href="{{ route('system.article.show', $article->id) }}">System Link - 301 redirect to alias (if exists)</a></li>
<li><a href="{{ url($article->urlA()) }}">Alias Link - if not exists - return setted in config 'url-aliases.if_urlA_is_empty'</a></li>
<li><a href="{{ route_alias('system.article.show', [$article, 'page' => '3', 'per_page' => 15]) }}">Alias Link - absolute path</a></li>
<li><a href="{{ route_alias('system.article.show', $article, false) }}">Alias Link - relative path</a></li>
<li><a href="{{ route_alias('system.article.show', ['page' => '3', 'per_page' => 15]) }}">System Link - if not exist alias</a></li>
<li><a href="{{ request()->path() }}">System Link</a></li>
<h2><a href="{{ url_alias_current() }}">Current path (alias or system)</a></h2>
<li><a href="@urlAliasCurrent()">Url current url alias or system path</a></li>
```

!!! In `route_alias()` second argument (array first index) must by instanceof \Illuminate\Database\Eloquent\Model