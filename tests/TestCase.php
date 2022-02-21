<?php

namespace Spatie\Visit\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Visit\VisitServiceProvider;
use Illuminate\Console\OutputStyle;
use Termwind\Laravel\TermwindServiceProvider;
use Termwind\Termwind;

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
