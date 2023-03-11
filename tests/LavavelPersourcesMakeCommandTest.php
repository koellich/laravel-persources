<?php

use Illuminate\Support\Facades\Artisan;

it('should create Permissions', function () {

    Artisan::call('make:persource Car');

    expect(false)->toBeTrue();
});


it('should create views', function () {
    $list = resource_path('resources/persources/cars/list.blade.html');
    $view = resource_path('resources/persources/cars/view.blade.html');

    expect(file_exists($list))->toBeFalse();
    expect(file_exists($view))->toBeFalse();

    Artisan::call('make:persource Car');
});

it('should create a Resource', function () {
    $carResource = app_path('Persources/CarResource.php');

    expect(file_exists($carResource))->toBeFalse();

    Artisan::call('make:persource Car');

    expect(file_exists($carResource))->toBeTrue();
    unlink($carResource);
});

it('should create routes', function () {
    expect(false)->toBeTrue();
});