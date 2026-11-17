<?php

namespace JeffersonGoncalves\CcpaBreaches;

use JeffersonGoncalves\CcpaBreaches\Commands\OverdueCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CcpaBreachesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ccpa-breaches')
            ->hasConfigFile()
            ->hasMigrations(['create_ccpa_breaches_table'])
            ->hasCommand(OverdueCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(BreachManager::class);
    }
}
