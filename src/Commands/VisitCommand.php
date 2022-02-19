<?php

namespace Spatie\Visit\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Illuminate\Testing\TestResponse;
use Spatie\Visit\Client;
use Spatie\Visit\Colorizers\Colorizer;
use Spatie\Visit\Colorizers\DummyColorizer;
use Spatie\Visit\Colorizers\HtmlColorizer;
use Spatie\Visit\Colorizers\JsonColorizer;
use Spatie\Visit\Exceptions\InvalidMethod;
use function Termwind\render;

class VisitCommand extends Command
{
    public $signature = '
        visit {url}
            {--method=get}
            {--show-headers}
            {--payload=}
            {--user=}
            {--no-color}
            {--only-response}
            {--hide-response}
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
        if (!$user = $this->option('user')) {
            return $this;
        }

        $user = is_numeric($user)
            ? User::find($user)
            : User::firstWhere('email', $user);

        if (!$user) {
            throw new Exception('No user found');
        }

        auth()->login($user);

        return $this;
    }

    protected function getMethod(): string
    {
        $method = strtolower($this->option('method'));

        $validMethodNames = collect(['get', 'post', 'put', 'patch', 'delete']);

        if (!$validMethodNames->contains($method)) {
            throw InvalidMethod::make($method, $validMethodNames);
        }

        return $method;
    }

    public function getPayload(): array
    {
        $payloadString = $this->option('payload');

        if (is_null($payloadString)) {
            return [];
        }

        $payload = json_decode($payloadString, true);

        if (is_null($payload)) {
            throw new Exception("You should pass valid JSON to the `payload option`");
        }

        return $payload;
    }

    protected function makeRequest(): TestResponse
    {
        $method = $this->getMethod();

        $url = $this->argument('url');

        $client = Client::make();

        return $method === 'get'
            ? $client->get($url)
            : $client->$method($url, $this->getPayload());
    }

    protected function renderResponse(TestResponse $response): self
    {
        if (! $this->option('hide-response')) {
            $this->renderContent($response);
        }

        if (!$this->option('only-response')) {
            $this->renderResponseProperties($response);
        }

        return $this;
    }

    protected function renderContent(TestResponse $response): self
    {
        $colorizer = $this->getColorizer($response);

        $content = $response->content();

        if (!$this->option('no-color')) {
            $content = $colorizer->colorize($response->content());
        }

        echo $content;

        return $this;
    }

    protected function renderResponseProperties(TestResponse $response): self
    {
        $requestProperties = view('visit::responseProperties', [
            'method' => $this->option('method'),
            'url' => $this->argument('url'),
            'statusCode' => $response->getStatusCode(),
            'content' => $response->content(),
            'headers' => $response->headers->all(),
            'showHeaders' => $this->option('show-headers'),
            'bgColor' => $this->getHeaderBackgroundColor($response)
        ]);

        render($requestProperties);

        return $this;
    }

    protected function getHeaderBackgroundColor(TestResponse $response): string
    {
        if ($response->isSuccessful() || $response->isRedirect()) {
            return 'bg-green-800';
        }

        return 'bg-red-800';
    }

    protected function getColorizer(TestResponse $response): Colorizer
    {
        $contentType = $response->headers->get('content-type', '');

        $colorizer = collect([
            new JsonColorizer(),
            new HtmlColorizer(),
        ])->first(fn(Colorizer $colorizer) => $colorizer->canColorize($contentType));

        return $colorizer ?? new DummyColorizer();
    }
}
