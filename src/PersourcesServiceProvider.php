<?php

namespace Koellich\Persources;

use Koellich\Persources\Commands\MakePersourceCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PersourcesServiceProvider extends PackageServiceProvider
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
            ->hasTranslations()
            ->hasRoutes('web')
            ->hasCommand(MakePersourceCommand::class);
    }

    public function packageRegistered()
    {
        $this->app->singleton(\Koellich\Persources\Facades\Persources::class, Persources::class);
        \Koellich\Persources\Facades\Persources::registerResources();
    }
}
