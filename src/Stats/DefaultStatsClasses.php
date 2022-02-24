<?php

namespace Spatie\Visit\Stats;

class DefaultStatsClasses
{
    public static function all(): array
    {
        return [
            RuntimeStat::class,
            QueryCountStat::class,
        ];
    }
}
