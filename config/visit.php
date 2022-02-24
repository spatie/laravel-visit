<?php

use Spatie\Visit\Colorizers\HtmlColorizer;
use Spatie\Visit\Colorizers\JsonColorizer;
use Spatie\Visit\Stats\DefaultStatsClasses;

return [
    /*
     * These classes are responsible for colorizing the output.
     */
    'colorizers' => [
        JsonColorizer::class,
        HtmlColorizer::class,
    ],

    'stats' => [
        ...DefaultStatsClasses::all(),
    ]
];
