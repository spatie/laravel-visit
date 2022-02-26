<?php

use Spatie\Visit\Tests\ArtisanOutput;
use Spatie\Visit\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function expectOutputContains(string ...$substrings)
{
    $output = ArtisanOutput::get();

    collect($substrings)->each(fn (string $substring) => expect($output)->toContain($substring));
}

function expectOutputDoesNotContain(string ...$substrings)
{
    $output = ArtisanOutput::get();

    collect($substrings)->each(fn (string $substring) => expect($output)->not()->toContain($substring));
}

function runningOnWindows(): bool
{
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}
