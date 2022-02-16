<?php

namespace Spatie\Visit\Commands;

use Illuminate\Console\Command;
use Spatie\Visit\Client;

class VisitCommand extends Command
{
    public $signature = 'laravel-visit {url}';

    public $description = 'Visit a route';

    public function handle()
    {
        Client::make()->get($this->argument('url'))->content();

        return self::SUCCESS;
    }
}
