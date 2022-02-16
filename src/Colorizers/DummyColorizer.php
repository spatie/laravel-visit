<?php

namespace Spatie\Visit\Colorizers;

class DummyColorizer extends Colorizer
{
    public function colorize(string $content): string
    {
        return $content;
    }
}
