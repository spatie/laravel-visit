<?php

namespace Spatie\Visit\Support;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

class Redirects
{
    protected array $redirects = [];

    public static function forUrl(string $startUrl): self
    {
        return new static($startUrl);

    }

    protected function __construct(string $startUrl)
    {
        $this->redirects[] = ['from' => $startUrl];
    }

    public function add(TestResponse|RedirectResponse $response)
    {
        $redirectLocation = Str::after($response->headers->get('Location'), config('app.url'));

        $this
            ->addToLastItem('to',$redirectLocation)
            ->addToLastItem('status',$response->getStatusCode());

        $this->redirects[] = ['from' => $redirectLocation];
    }

    protected function addToLastItem(string $key, string $value): self
    {
        $lastKey = array_key_last($this->redirects);

        $this->redirects[$lastKey][$key] = $value;

        return $this;
    }

    public function all(): array
    {
        if (! count($this->redirects)) {
            return [];
        }

        return array_slice($this->redirects, 0, -1);
    }

    public function lastTo(): string
    {
        $lastKey = array_key_last($this->redirects);

        return $this->redirects[$lastKey]['from'];

    }
}
