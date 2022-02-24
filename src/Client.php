<?php

namespace Spatie\Visit;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;

class Client
{
    use MakesHttpRequests;
    use InteractsWithExceptionHandling {
        withoutExceptionHandling as protected traitWithoutExceptionHandling;
    }

    public static function make(): self
    {
        return new self(app());
    }

    public function __construct(protected Application $app)
    {
    }

    public function withoutExceptionHandling(array $except = [])
    {
        $this->traitWithoutExceptionHandling($except);
    }
}
