<?php

namespace Spatie\Visit\Stats;

use Illuminate\Contracts\Foundation\Application;

class ModelsRetrievedStat extends Stat
{
    protected int $modelsRetrievedCount = 0;

    public function beforeRequest(Application $app)
    {
        /** @var \Illuminate\Events\Dispatcher $dispatcher */
        $dispatcher = $app['events'];

        $dispatcher->listen('eloquent.retrieved:*', function() {
            $this->modelsRetrievedCount++;
        });
    }


    public function getStatResult(): StatResult
    {
        return StatResult::make('Models retrieved')->value($this->modelsRetrievedCount);
    }
}
