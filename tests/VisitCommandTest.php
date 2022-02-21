<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/', function () {
        return 'get result';
    });
});

it('can visit a page', function () {
    Artisan::call('visit /');
    expect(true)->toBe(true);

    dd(\Illuminate\Support\Facades\Artisan::output());
});
