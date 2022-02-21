<?php

namespace Spatie\Visit\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Visit\VisitServiceProvider;
use Illuminate\Console\OutputStyle;
use Termwind\Termwind;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            VisitServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $app->resolving(OutputStyle::class, function ($style): void {
            Termwind::renderUsing($style->getOutput());
        });
    }
}
