<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\artisan;

beforeEach(function() {
    Route::get('/', function () {
        return 'get result';
    });
});

it('can visit a page', function() {
    Artisan::call('visit /');
    expect(true)->toBe(true);

    dd(Artisan::output());
});
