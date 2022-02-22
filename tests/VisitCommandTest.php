<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::any('/', function () {
        return 'result from home route';
    });

    Route::get('exception', function() {
        throw new Exception("exception in route");
    });

    Route::get('my-contact-page', function() {
        return 'result from contact route';
    })->name('contact');
});

it('by default if will make GET requests', function () {
    Artisan::call('visit /');

    expectOutputContains(
        'GET /',
        200,
        'result from home route',
    );
});

it('can handle all methods', function(string $method) {
    Artisan::call("visit / --method={$method}");

    expectOutputContains(
        strtoupper($method) . ' /',
        200,
        'result from home route',
    );
})->with(['get', 'post', 'patch', 'put', 'delete']);

it('can handle a missing route', function() {
    Artisan::call("visit /non-existing-route");

    expectOutputContains('GET /non-existing-route', 404);
});

it('can handle a route with an exception', function() {
    Artisan::call("visit /exception");

    expectOutputContains('GET /exception', 500);
});

it('can display the underlying exception', function() {
    Artisan::call("visit /exception --show-exception");
})->throws('exception in route');

it('can visit a route using a route name', function() {
    Artisan::call("visit --route=contact");

    expectOutputContains(
        'GET /my-contact-page',
        200,
        'result from contact route',
    );
});

