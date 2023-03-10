<?php

namespace Koellich\LaravelPersources;

use Koellich\LaravelPersources\Commands\LaravelPersourcesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelPersourcesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-persources')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-persources_table')
            ->hasCommand(LaravelPersourcesCommand::class);
    }
}
