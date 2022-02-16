<?php

namespace Spatie\Visit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Visit\Visit
 */
class Visit extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-visit';
    }
}
