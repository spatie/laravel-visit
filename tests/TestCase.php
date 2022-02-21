<?php

namespace Spatie\Visit\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Visit\VisitServiceProvider;

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
    }
}
