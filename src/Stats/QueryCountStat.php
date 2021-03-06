<?php

namespace Spatie\Visit\Stats;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;

class QueryCountStat extends Stat
{
    protected int $queriesExecuted = 0;

    public function beforeRequest(Application $app)
    {
        DB::listen(fn () => $this->queriesExecuted++);
    }

    public function getStatResult(): StatResult
    {
        return StatResult::make('Query count')
            ->value($this->queriesExecuted);
    }
}
