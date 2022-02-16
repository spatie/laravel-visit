<?php

namespace Spatie\Visit\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;
use Spatie\Visit\Client;
use Spatie\Visit\Colorizers\HtmlColorizer;
use function Termwind\render;

class VisitCommand extends Command
{
    public $signature = 'visit {url} {--method=get} {--user=}';

    public $description = 'Visit a route';

    public function handle()
    {
        if ($user = $this->option('user')) {
            $this->logInUser($user);
        }

        $method = $this->getMethod();
        $url = $this->argument('url');

        /** @var  \Illuminate\Http\Response $response */
        $response = Client::make()->$method($this->argument('url'));

        $view = view('visit::response', [
            'method' => $method,
            'url' => $url,
            'statusCode' => $response->getStatusCode(),
            'content' => $response->content(),
        ]);

        render($view);

        $colorizedOutput = (new HtmlColorizer())->colorize($response->content());

        echo $colorizedOutput;

        return $response->isSuccessful() || $response->isRedirect()
            ? self::SUCCESS
            : self::FAILURE;
    }

    protected function getMethod(): string
    {
        $method = $this->option('method');

        // TODO: validate method

        return $method;
    }

    protected function logInUser(string $user)
    {
        $user = is_numeric($user)
            ? User::find($user)
            : User::firstWhere('email', $user);

        if (! $user) {
            throw new Exception('No user found');
        }

        auth()->login($user);
    }
}
