<?php

use Spatie\Visit\Tests\TestCase;
use \Illuminate\Support\Arr;

uses(TestCase::class)->in(__DIR__);

function expectOutputContains(string ...$substrings)
{
    $output = Artisan::output();

    collect($substrings)->each(fn(string $substring) => expect($output)->toContain($substring));

}
