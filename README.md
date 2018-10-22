## Installation

Require this package with composer
```shell
composer require fomvasss/laravel-url-aliases
```

If you don't use auto-discovery (Laravel < 5.5), add the ServiceProvider to the providers array in config/app.php
```php
Fomvasss\UrlAliases\ServiceProvider::class,
```

Publish package resource:
- config
- migration
- test seeder

```shell
php artisan vendor:publish --provider="Fomvasss\UrlAliases\ServiceProvider"
```

Run migrate:
```shell
php artisan migrate
```

## Usage

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

### Helper functions:
- `route_alias()` // works the same way as Laravel helper `route()`

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

public function store(Request $request)
{
    $articles = Models\Article::paginate($request->per_page);
    
    return view('article.index', compact('articles'));
}


public function store(Request $request)
{
    $article = Models\Article::create($request->only([
        //...
    ]);
    
    $article->urlAlias()->updateOrCreate([
        'system_path' => trim(route('system.article.show', $article, false), '/'),
        'aliased_path' => str_slug($article->title).'/'.str_slug($article->user->name),
    ]);

    return redirect()->route('system.article.index');
}

public function show(Request $request, $id)
{
    $article = Models\Article::findOrFail($id);

    // $article->urlAlias;
    // $article->urlA();
    // $request->server('REQUEST_URI'); // system/article/32
    // $request->server('ALIAS_REQUEST_URI'); // some-title-article/taylor-otwell

    return view('article.show', compact('article'));
}

```

```blade
    <li><a href="{{ route_alias('system.article.index', ['page' => '3', 'per_page' => 15]) }}">All articles</a></li>
    <li><a href="{{ route('system.article.show', $article->id) }}">System Link - 301 redirect to alias (if exists)</a></li>
    <li><a href="{{ url(optional($article->urlAlias)->aliased_path) }}">Alias Link</a></li>
    <li><a href="{{ url($article->urlA()) }}">Alias Link</a></li>
    <li><a href="{{ route_alias('system.article.show', [$article, 'page' => '3', 'per_page' => 15]) }}">Alias Link</a></li>
    <li><a href="{{ route_alias('system.article.show', $article, false) }}">Alias Link</a></li>
    <li><a href="{{ route_alias('system.article.show', ['page' => '3', 'per_page' => 15]) }}">System Link</a></li>
    <li><a href="{{ request()->path() }}">System Link</a></li>
```
(For entity first array index must by instanceof \Illuminate\Database\Eloquent\Model)