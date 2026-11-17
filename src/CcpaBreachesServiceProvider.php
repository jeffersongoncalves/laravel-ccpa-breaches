<?php

namespace JeffersonGoncalves\CcpaBreaches;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CcpaBreachesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-ccpa-breaches')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
