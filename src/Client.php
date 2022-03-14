<?php

namespace Spatie\Visit;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Str;
use Spatie\Visit\Support\Redirects;

class Client
{
    use MakesHttpRequests;
    use InteractsWithExceptionHandling {
        withoutExceptionHandling as protected traitWithoutExceptionHandling;
    }

    public function __construct(
        protected Application $app,
        protected Redirects $followedRedirects)
    {

    }

    public function getFollowedRedirects(): Redirects
    {
        return $this->followedRedirects;
    }

    public function withoutExceptionHandling(array $except = [])
    {
        $this->traitWithoutExceptionHandling($except);
    }

    protected function followRedirects($response)
    {
        $this->followRedirects = false;

        while ($response->isRedirect()) {
            $this->followedRedirects->add($response);
            $response = $this->get($response->headers->get('Location'));
        }

        return $response;
    }


}
