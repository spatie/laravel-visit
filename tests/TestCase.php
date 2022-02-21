<?php

namespace Spatie\Visit\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Visit\VisitServiceProvider;
use Termwind\Laravel\TermwindServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            VisitServiceProvider::class,
            TermwindServiceProvider::class,
        ];
    }
}
