<?php

namespace Spatie\Visit\Filters;

use Illuminate\Testing\TestResponse;

abstract class Filter
{
    abstract public function canFilter(TestResponse $response, string $content): bool;

    abstract public function filter(TestResponse $response, string $content, string $filter): string;
}
