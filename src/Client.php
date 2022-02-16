<?php

namespace Spatie\Visit;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class Client
{
    use MakesHttpRequests;

    public static function make(): self
    {
        return new static(app());
    }

    public function __construct(protected Application $app)
    {

    }
}
