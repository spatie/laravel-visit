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
     * There classes can filter the content of a response.
     */
    'filters' => [
        Spatie\Visit\Filters\JsonFilter::class,
        Spatie\Visit\Filters\HtmlFilter::class,
    ],

    /*
     * These stats will be displayed in the response block.
     */
    'stats' => [
        ...Spatie\Visit\Stats\DefaultStatsClasses::all(),
    ],
];
