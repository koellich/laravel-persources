# Permission based resources with CRUD routing for your models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/koellich/laravel-persources.svg?style=flat-square)](https://packagist.org/packages/koellich/laravel-persources)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/koellich/laravel-persources/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/koellich/laravel-persources/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/koellich/laravel-persources/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/koellich/laravel-persources/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/koellich/laravel-persources.svg?style=flat-square)](https://packagist.org/packages/koellich/laravel-persources)

laravel-persources expands on [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) by connecting **per**missions with re**sources** (models) and automagically generating and handling CRUD routes.


## How to use

Let's say you have a model called ```Car``` that you want to expose in your frontend:

```bash
php artisan make:persource Car
```

to generate the following:

**Permissions, Routes and Views**

| Permission  | Route             | Views                                     |
|-------------|-------------------|-------------------------------------------|
| car.list    | GET /cars         | resources/persources/cars/list.blade.html |
| car.view    | GET /cars/{id}    | resources/persources/cars/view.blade.html |
| car.update  | PATCH /cars/{id}  |  |
| car.delete  | DELETE /cars/{id} |  |

If a user who visits `https://yourapp/cars` has the permission `car.list` either directly or indirectly via a role then the `list.blade.html` will be rendered. Otherwise, a 403 error will be returned.
Persources was designed with Livewire in mind, but you can customize everything to use Vue.js if that is your preferred front end stack.

**Resource**

`App\Persources\CarResource` 

This is the glue between your ```Car``` model and the routes & views. 
It allows you to fine tune what attributes of your model are passed to the views, which attributes can be edited, which views to use and much more.


## Installation

You can install the package via composer:

```bash
composer require koellich/laravel-persources
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-persources-migrations"
php artisan migrate
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
