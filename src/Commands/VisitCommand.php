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
use function Termwind\render;

class VisitCommand extends Command
{
    public $signature = 'visit {url} {--method=get} {--user=}';

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
            throw new Exception('No user found');
        }

        auth()->login($user);

        return $this;
    }

    protected function getMethod(): string
    {
        $method = $this->option('method');

        // TODO: validate method

        return $method;
    }

    protected function makeRequest(): TestResponse
    {
        $method = $this->getMethod();

        $url = $this->argument('url');

        return Client::make()->$method($url);
    }

    protected function renderResponse(TestResponse $response): self
    {
        $view = view('visit::response', [
            'method' => $this->option('method'),
            'url' => $this->argument('url'),
            'statusCode' => $response->getStatusCode(),
            'content' => $response->content(),
        ]);

        render($view);

        $colorizer = $this->getColorizer($response);

        echo $colorizer->colorize($response->content());

        return $this;
    }

    protected function getColorizer(TestResponse $response): Colorizer
    {
        $contentType = $response->headers->get('content-type');

        $colorizer = collect([
            new JsonColorizer(),
            new HtmlColorizer(),
        ])->first(fn (Colorizer $colorizer) => $colorizer->canColorize($contentType));

        return $colorizer ?? new DummyColorizer();
    }
}
