<?php

namespace Spatie\Visit\Exceptions;

use Exception;

class NoUserFound extends Exception
{
    public static function make(): self
    {
        return new self('Did not find a user to log in.');
    }
}
