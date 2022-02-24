<?php

namespace Spatie\Visit\Stats;

use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class Runtime extends Stat
{
    protected Stopwatch $stopwatch;

    protected ?StopwatchEvent $stopwatchEvent = null;

    public function __construct()
    {
        $this->stopWatch = new Stopwatch(true);
    }

    public function beforeRequest(Application $app)
    {
        $this->stopWatch->start('default');
    }

    public function afterRequest(Application $app)
    {
        $this->stopwatchEvent = $this->stopWatch->stop('default');
    }

    public function getStatResult(): StatResult
    {
        $duration = $this->stopwatchEvent->getDuration();

        return StatResult::make('Duration')
            ->value($duration . 'ms');
    }
}
