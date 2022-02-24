<?php

return [
    /*
     * These classes are responsible for colorizing the output.
     */
    'colorizers' => [
        Spatie\Visit\Colorizers\JsonColorizer::class,
        Spatie\Visit\Colorizers\HtmlColorizer::class,
    ],

    /*
     * These stats will be displayed in the response block.
     */
    'stats' => [
        ...Spatie\Visit\Stats\DefaultStatsClasses::all(),
    ]
];
