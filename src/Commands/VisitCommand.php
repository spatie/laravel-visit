<?php

namespace Spatie\Visit\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Illuminate\Testing\TestResponse;
use Soundasleep\Html2Text;
use Spatie\Visit\Client;
use Spatie\Visit\Colorizers\Colorizer;
use Spatie\Visit\Colorizers\DummyColorizer;
use Spatie\Visit\Exceptions\InvalidMethod;
use Spatie\Visit\Exceptions\InvalidPayload;
use Spatie\Visit\Exceptions\NoUrlSpecified;
use Spatie\Visit\Exceptions\NoUserFound;
use Spatie\Visit\Stats\StatResult;
use Spatie\Visit\Stats\StatsCollection;
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
            {--as-text}
            {--only-response}
            {--only-stats}
        ';

    // filter: json, xpath
    // payload as file
    // display request as curl

    public $description = 'Visit a route';

    public function handle()
    {
        $this->logInUser();

        ['response' => $response, 'statResults' => $statResults] = $this->makeRequest();
        $this->renderResponse($response, $statResults);

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

    /** @return array{response: TestResponse, statResults:array<int, \Spatie\Visit\Stats\StatResult>} */
    protected function makeRequest(): array
    {
        $method = $this->getMethod();

        $url = $this->getUrl();

        $application = app();

        $client = new Client($application);

        if ($this->option('show-exception')) {
            $client->withoutExceptionHandling();
        }

        $stats = StatsCollection::fromConfig();

        $stats->callBeforeRequest($application);

        $response = $method === 'get'
            ? $client->get($url)
            : $client->$method($url, $this->getPayload());

        $stats->callAfterRequest($application);

        $statResults = $stats->getResults();

        return compact('response', 'statResults');
    }

    /**
     * @param \Illuminate\Testing\TestResponse $response
     * @param array<int, StatResult $statResults
     *
     * @return $this
     */
    protected function renderResponse(TestResponse $response, array $statResults): self
    {
        if (! $this->option('only-response-properties')) {
            $this->renderContent($response);
        }

        if (! $this->option('only-response')) {
            $this->renderStats($response, $statResults);
        }

        return $this;
    }

    protected function renderContent(TestResponse $response): self
    {
        $colorizer = $this->getColorizer($response);

        $content = $response->content();

        if ($this->option('as-text')) {
            $content = Html2Text::convert($content,  ['ignore_errors' => true]);

            $this->output->writeln($content);

            return $this;
        }

        if (! $this->option('no-color')) {
            $content = $colorizer->colorize($response->content());
        }

        $this->output->writeln($content);

        return $this;
    }

    /**
     * @param \Illuminate\Testing\TestResponse $response
     * @param array<int, StatResult> $statResults
     *
     * @return $this
     * @throws \Spatie\Visit\Exceptions\NoUrlSpecified
     */
    protected function renderStats(TestResponse $response, array $statResults): self
    {
        $requestPropertiesView = view('visit::stats', [
            'method' => $this->option('method'),
            'url' => $this->getUrl(),
            'statusCode' => $response->getStatusCode(),
            'content' => $response->content(),
            'headers' => $response->headers->all(),
            'showHeaders' => $this->option('show-headers'),
            'headerStyle' => $this->getHeaderStyle($response),
            'statResults' => $statResults,
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
