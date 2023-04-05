# Permission based resources with CRUD routing for your models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/koellich/laravel-persources.svg?style=flat-square)](https://packagist.org/packages/koellich/laravel-persources)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/koellich/laravel-persources/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/koellich/laravel-persources/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/koellich/laravel-persources/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/koellich/laravel-persources/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/koellich/laravel-persources.svg?style=flat-square)](https://packagist.org/packages/koellich/laravel-persources)

laravel-persources expands on [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) by connecting **per**missions with re**sources** (models) and automagically generating and handling CRUD routes.


## How to use

Let's say you have a model called `Car` that you want to expose in your frontend:

```bash
php artisan make:persource Car list view create update delete
```

to generate the following:

**Permissions, Routes and Views**

| Permission  | Route             | Views                                     |
|-------------|-------------------|-------------------------------------------|
| cars.list   | GET /cars         | resources/persources/cars/list.blade.html |
| cars.view   | GET /cars/{id}    | resources/persources/cars/view.blade.html |
| cars.create | POST /cars/{id}   |                                           |
| cars.update | PATCH /cars/{id}  |                                           |
| cars.delete | DELETE /cars/{id} |                                           |

If a user who visits `https://yourapp/cars` has the permission `cars.list` either directly or indirectly via a role then the `.../cars/list.blade.html` will be rendered. Otherwise, a 403 error will be returned.

Note that permissions are not created directly in the DB but a migration file is created that will add/remove the permissions. If you do not want a migration, then use the `--noMigration` option.

Views are generated from stubs. You can override the stubs directory and use your own stubs by setting the config variable `view_stubs_path`.

The package also recognizes permissions ending in 
`.read` to mean both `list` and `view`, 
and permissions ending in 
`.write`to mean all of the following: `list`, `view`, `create`, `update`.

**Resource**

`App\Persources\CarResource` 

This is the glue between your ```Car``` model and the routes & views. 
It allows you to fine tune what attributes of your model are passed to the views, which attributes can be edited, which views to use and much more.

Routes are generated at runtime. This means you will only have those routes that correspond to your resource's `$permissions` attribute.

**What if I already have permissions?**

In this case, run:
```bash
php artisan make:persource Car none -Pmy.cars.read -Pmy.cars.update
```

Here, `my.cars.read` and `my.cars.update` are the existing permissions. Multiple permissions are possible.
Note: You can always add existing permissions to the resource's `$permissions` attribute.

**Prefixing permissions**

If you want to prefix generated permissions, you can use the ´--prefix´ option:
```
php artisan make:persource User read write --prefix=admin
```
This will generate the permissions: `admin.users.read` and `admin.users.write` as well as the corresponding routes under `admin/users/`.

## Installation

You can install the package via composer:

```bash
composer require koellich/laravel-persources
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="persources-config"
```

This is the contents of the published config file:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Namespace
    |--------------------------------------------------------------------------
    |
    | This is the namespace under which the Resources are generated.
    |
    */

    'resources_namespace' => env('PERSOURCES_RESOURCES_NAMESPACE', 'App\\Persources'),

    /*
    |--------------------------------------------------------------------------
    | Route Root Path
    |--------------------------------------------------------------------------
    |
    | This is the root path under which all persources routes will be generated.
    | Change this to avoid collisions with your other routes.
    |
    */

    'route_root' => env('PERSOURCES_ROOT', ''),

    /*
    |--------------------------------------------------------------------------
    | View Root Path
    |--------------------------------------------------------------------------
    |
    | This is the path under which the view templates are generated in the
    | resources path.
    |
    */

    'view_root' => env('PERSOURCES_VIEWS_ROOT', 'views/persources'),

    /*
    |--------------------------------------------------------------------------
    | View Stubs Path
    |--------------------------------------------------------------------------
    |
    | This is the path under which the view template stubs are located.
    | If null, then the stubs from the persources package will be used.
    |
    | Set this to e.g.: base_path('stubs') and make sure the following
    | files exist there: list.blade.php, view.blade.php
    |
    */

    'view_stubs_path' => env('PERSOURCES_VIEW_STUBS', null),
    
    /*
    |--------------------------------------------------------------------------
    | Middleware Group
    |--------------------------------------------------------------------------
    |
    | This is the middleware group that is used for the generated routes
    |
    */

    'middleware_group' => env('PERSOURCES_MIDDLEWARE_GROUP', 'web'),

    /*
    |--------------------------------------------------------------------------
    | Admin Role
    |--------------------------------------------------------------------------
    |
    | This is the Role which can access all resources regardless of permissions.
    | Set this to null if you do not want to have an admin role.
    |
    */

    'admin_role' => env('PERSOURCES_ADMIN_PERMISSION', 'admin'),
];
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [koellich](https://github.com/koellich)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
