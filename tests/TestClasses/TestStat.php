<?php

namespace Spatie\Visit\Tests\TestClasses;

use Spatie\Visit\Stats\Stat;
use Spatie\Visit\Stats\StatResult;

class TestStat extends Stat
{

    public function getStatResult(): StatResult
    {
        return StatResult::make('test stat name')->value('test stat value');
    }
}
