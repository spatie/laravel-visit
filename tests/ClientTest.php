<?php

use Illuminate\Support\Facades\Route;
use Spatie\Visit\Client;

beforeEach(function() {
    Route::get('get-route', function() {
        return 'get result';
    });
});

it('can perform a get request', function () {
    $content = Client::make()->get('/get-route')->content();

    expect($content)->toBe('get result');
});
