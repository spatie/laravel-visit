<?php

namespace Spatie\Visit;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Visit\Commands\VisitCommand;

class VisitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-visit')
            ->hasViews()
            ->hasConfigFile()
            ->hasCommand(VisitCommand::class);
    }
}
