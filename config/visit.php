<?php

use Spatie\Visit\Colorizers\HtmlColorizer;
use Spatie\Visit\Colorizers\JsonColorizer;

return [
    /*
     * These classes are responsible for colorizing the output.
     */
    'colorizers' => [
        JsonColorizer::class,
        HtmlColorizer::class,
    ],
];
