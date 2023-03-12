# Permission based resources with CRUD routing for your models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/koellich/laravel-persources.svg?style=flat-square)](https://packagist.org/packages/koellich/laravel-persources)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/koellich/laravel-persources/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/koellich/laravel-persources/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/koellich/laravel-persources/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/koellich/laravel-persources/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/koellich/laravel-persources.svg?style=flat-square)](https://packagist.org/packages/koellich/laravel-persources)

laravel-persources expands on [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) by connecting **per**missions with re**sources** (models) and automagically generating and handling CRUD routes.


## How to use

Let's say you have a model called ```Car``` that you want to expose in your frontend:

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
Persources was designed with Livewire in mind, but you can customize everything to suit your preferred front end stack, e.g. Vue.js.

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
php artisan make:persource Car none
```

and then paste your existing permissions into the resource's `$permissions` attribute.

## Installation

You can install the package via composer:

```bash
composer require koellich/laravel-persources
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-persources-config"
```

This is the contents of the published config file:

```php
return [
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
