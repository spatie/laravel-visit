<?php

namespace Spatie\Visit\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Visit\VisitServiceProvider;
use Termwind\Laravel\TermwindServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        ArtisanOutput::clear();
    }

    protected function getPackageProviders($app)
    {
        return [
            VisitServiceProvider::class,
            TermwindServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('password');

            $table->timestamps();
        });

        Model::unguard();
    }
}
