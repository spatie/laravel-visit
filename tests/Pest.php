<?php

use Spatie\Visit\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function expectOutputContains(string ...$substrings)
{
    $output = Artisan::output();

    collect($substrings)->each(fn(string $substring) => expect($output)->toContain($substring));

}
