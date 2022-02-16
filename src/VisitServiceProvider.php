<?php

namespace Spatie\Visit;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Visit\Commands\VisitCommand;

class VisitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-visit')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-visit_table')
            ->hasCommand(VisitCommand::class);
    }
}
