<?php

namespace Spatie\Visit\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Illuminate\Testing\TestResponse;
use Spatie\Visit\Client;
use Spatie\Visit\Colorizers\Colorizer;
use Spatie\Visit\Colorizers\DummyColorizer;
use Spatie\Visit\Exceptions\InvalidMethod;
use Spatie\Visit\Exceptions\InvalidPayload;
use Spatie\Visit\Exceptions\NoUrlSpecified;
use Spatie\Visit\Exceptions\NoUserFound;
use function Termwind\render;

class VisitCommand extends Command
{
    public $signature = '
        visit {url?}
            {--route=}
            {--method=get}
            {--payload=}
            {--user=}
            {--show-exception}
            {--show-headers}
            {--no-color}
            {--only-response}
            {--only-response-properties}
        ';

    public $description = 'Visit a route';

    public function handle()
    {
        $this->logInUser();

        $response = $this->makeRequest();

        $this->renderResponse($response);

        return $response->isSuccessful() || $response->isRedirect()
            ? self::SUCCESS
            : self::FAILURE;
    }

    protected function logInUser(): self
    {
        if (! $user = $this->option('user')) {
            return $this;
        }

        $user = is_numeric($user)
            ? User::find($user)
            : User::firstWhere('email', $user);

        if (! $user) {
            throw NoUserFound::make();
        }

        auth()->login($user);

        return $this;
    }

    protected function getMethod(): string
    {
        $method = strtolower($this->option('method'));

        $validMethodNames = collect(['get', 'post', 'put', 'patch', 'delete']);

        if (! $validMethodNames->contains($method)) {
            throw InvalidMethod::make($method, $validMethodNames);
        }

        return $method;
    }

    protected function getUrl(): string
    {
        if ($routeName = $this->option('route')) {
            return route($routeName, absolute: false);
        }

        if ($url = $this->argument('url')) {
            return $url;
        }

        throw NoUrlSpecified::make();
    }

    protected function getPayload(): array
    {
        $payloadString = $this->option('payload');

        if (is_null($payloadString)) {
            return [];
        }

        $payload = json_decode($payloadString, true);

        if (is_null($payload)) {
            throw InvalidPayload::make();
        }

        return $payload;
    }

    protected function makeRequest(): TestResponse
    {
        $method = $this->getMethod();

        $url = $this->getUrl();

        $client = Client::make();

        if ($this->option('show-exception')) {
            $client->withoutExceptionHandling();
        }

        return $method === 'get'
            ? $client->get($url)
            : $client->$method($url, $this->getPayload());
    }

    protected function renderResponse(TestResponse $response): self
    {
        if (! $this->option('only-response-properties')) {
            $this->renderContent($response);
        }

        if (! $this->option('only-response')) {
            $this->renderResponseProperties($response);
        }

        return $this;
    }

    protected function renderContent(TestResponse $response): self
    {
        $colorizer = $this->getColorizer($response);

        $content = $response->content();

        if (! $this->option('no-color')) {
            $content = $colorizer->colorize($response->content());
        }

        $this->output->write($content);

        return $this;
    }

    protected function renderResponseProperties(TestResponse $response): self
    {
        $requestPropertiesView = view('visit::responseProperties', [
            'method' => $this->option('method'),
            'url' => $this->getUrl(),
            'statusCode' => $response->getStatusCode(),
            'content' => $response->content(),
            'headers' => $response->headers->all(),
            'showHeaders' => $this->option('show-headers'),
            'headerStyle' => $this->getHeaderStyle($response),
        ]);

        render($requestPropertiesView);

        return $this;
    }

    protected function getHeaderStyle(TestResponse $response): string
    {
        if ($response->isSuccessful() || $response->isRedirect()) {
            return 'bg-green text-black';
        }

        return 'bg-red text-white';
    }

    protected function getColorizer(TestResponse $response): Colorizer
    {
        $contentType = $response->headers->get('content-type', '');

        $colorizer = collect(config('visit.colorizers'))
            ->map(fn (string $colorizerClassName) => app($colorizerClassName))
            ->first(fn (Colorizer $colorizer) => $colorizer->canColorize($contentType));

        return $colorizer ?? new DummyColorizer();
    }
}
