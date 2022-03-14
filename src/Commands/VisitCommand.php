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
use Spatie\Visit\Filters\DummyFilter;
use Spatie\Visit\Filters\Filter;
use Spatie\Visit\Stats\StatResult;
use Spatie\Visit\Stats\StatsCollection;
use Spatie\Visit\Support\Redirects;
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
            {--follow-redirects}
            {--headers}
            {--no-color}
            {--text}
            {--only-response}
            {--only-stats}
            {--filter=}
        ';

    public $description = 'Visit a route';

    public function handle()
    {
        $this->logInUser();

        ['response' => $response, 'statResults' => $statResults, 'redirects' => $redirects] = $this->makeRequest();

        $this->renderResponse($response, $statResults, $redirects);

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

        $client = new Client($application, Redirects::forUrl($url));

        if ($this->option('follow-redirects')) {
            $client->followingRedirects();
        }

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

        $redirects = $client->getFollowedRedirects();

        return compact('response', 'statResults', 'redirects');
    }

    /**
     * @param \Illuminate\Testing\TestResponse $response
     * @param array<int, StatResult $statResults
     *
     * @return $this
     */
    protected function renderResponse(
        TestResponse $response,
        array $statResults,
        Redirects $redirects,
    ): self {
        if (! $this->option('only-stats')) {
            $this->renderContent($response);
        }

        if (! $this->option('only-response')) {
            $this->renderStats($response, $statResults, $redirects);
        }

        return $this;
    }

    protected function renderContent(TestResponse $response): self
    {
        $content = $response->content();

        if ($filter = $this->option('filter')) {
            $filterClass = $this->getFilter($response, $content);

            $content = $filterClass->filter($response, $content, $filter);
        }

        if ($this->option('text')) {
            $content = Html2Text::convert($content, ['ignore_errors' => true]);

            $this->output->writeln($content);

            return $this;
        }

        if (! $this->option('no-color')) {
            $colorizer = $this->getColorizer($response);

            $content = $colorizer->colorize($content);
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
    protected function renderStats(
        TestResponse $response,
        array $statResults,
        Redirects $redirects,
    ): self {
        $requestPropertiesView = view('visit::stats', [
            'method' => $this->option('method'),
            'url' => $redirects->lastTo(),
            'statusCode' => $response->getStatusCode(),
            'content' => $response->content(),
            'headers' => $response->headers->all(),
            'showHeaders' => $this->option('headers'),
            'redirects' => $redirects->all(),
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

    protected function getFilter(TestResponse $response, string $content): Filter
    {
        $filter = collect(config('visit.filters'))
            ->map(fn (string $filterClassName) => app($filterClassName))
            ->first(fn (Filter $filter) => $filter->canFilter($response, $content));

        return $filter ?? new DummyFilter();
    }
}
