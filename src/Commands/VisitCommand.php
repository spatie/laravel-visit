<?php

namespace Spatie\Visit\Commands;

use Illuminate\Console\Command;

class VisitCommand extends Command
{
    public $signature = 'laravel-visit';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
