<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/', function () {
        return 'result from get route';
    });
});

it('can visit a page', function () {
    Artisan::call('visit /');

    expectOutputContains(
        'GET /',
        200,
        'result from get route',
    );
});
