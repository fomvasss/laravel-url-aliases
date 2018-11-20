# Laravel URL Aliases

[![License](https://img.shields.io/packagist/l/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://packagist.org/packages/fomvasss/laravel-url-aliases)
[![Build Status](https://img.shields.io/github/stars/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://github.com/fomvasss/laravel-url-aliases)
[![Latest Stable Version](https://img.shields.io/packagist/v/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://packagist.org/packages/fomvasss/laravel-url-aliases)
[![Total Downloads](https://img.shields.io/packagist/dt/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://packagist.org/packages/fomvasss/laravel-url-aliases)
[![Quality Score](https://img.shields.io/scrutinizer/g/fomvasss/laravel-url-aliases.svg?style=for-the-badge)](https://scrutinizer-ci.com/g/fomvasss/laravel-url-aliases)

## Installation

Require this package with composer
```shell
composer require fomvasss/laravel-url-aliases
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
- `prepare_url_path()` - return path for URL: https://your-site.com/my-first-page/example/ -> my-first-page/example 

### Blade directive

- @urlAliasCurrent()

### Example:

- `routes/web.php`:

```php
Route::group(['prefix' => 'system', 'as' => 'system'], function () {
    //...
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
        'source' => trim(route('system.article.show', $article, false), '/'),      // Ex.: system/article/26
        'alias' => str_slug($article->title).'/'.str_slug($article->user->name), // must be unique! Ex.: my-first-article/taylor-otwell
    ]);
    
    // Or if external link:
    $article->urlAlias()->create([
        'source' => 'https://google.com.ua',
        'alias' => 'my-google'
        'type' => 301,          // type redirect!
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
<li><a href="{{ request()->path() }}">System Link - redirect to alias (if exists)</a></li>
<h2><a href="{{ url_alias_current() }}">Current path (alias or system)</a></h2>
<li><a href="@urlAliasCurrent()">Url current url alias or system path</a></li>
```

!!! In `route_alias()` second argument (array first index) must by instanceof \Illuminate\Database\Eloquent\Model

## Use localization URL's

For use localization url's, you need do next steps:
1) Add to `Http/Kernel.php` next middleware:
```php
    protected $routeMiddleware = [
        //...
        'applyUrlLocaleToRootPage' => \Fomvasss\UrlAliases\Middleware\ApplyUrlLocaleToRootPage::class,
    ];
```
2) Set in `config/url-aliases.php`: 'use_localization' => true,
3) Uncomment needed locales in `config/url-aliases-laravellocalization.php` and set other params
4) Make or change your home page (root) routes, for example:
```php
Route::get('/{locale?}', function () {
    return view('home');
})->name('home')->middleware('applyUrlLocaleToRootPage');
```
5) Save aliases for entity and set locale:
```php
    $article->urlAlias()->create([
        'source' => trim(route('system.article.show', $article, false), '/'),      // Ex.: system/article/26
        'alias' => str_slug($article->title).'/'.str_slug($article->user->name), // must be unique! Ex.: my-first-article/taylor-otwell
        'locale' => 'en',
    ]);
```
6) Use facade `UrlAliasLocalization` and next methods (like in [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization)):
```php
    UrlAliasLocalization::getDefaultLocale()
    UrlAliasLocalization::getCurrentLocale()
    UrlAliasLocalization::getCurrentLocaleName()
    UrlAliasLocalization::getCurrentLocaleName()
    UrlAliasLocalization::getCurrentLocaleNative()
    UrlAliasLocalization::getCurrentLocaleNativeReading()
    UrlAliasLocalization::getCurrentLocaleRegional()
    UrlAliasLocalization::getCurrentLocaleScript()
    UrlAliasLocalization::getLocalesOrder()
    UrlAliasLocalization::getSupportedLocales()
```

## Links

* [https://github.com/mcamara/laravel-localization](https://github.com/mcamara/laravel-localization)
