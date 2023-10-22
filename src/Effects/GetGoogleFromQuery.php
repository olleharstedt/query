<?php

namespace Query\Effects;

use InvalidArgumentException;

class GetGoogleFromQuery implements Read
{
    public function __invoke(mixed $query): string|false
    {
        if (!is_string($query)) {
            throw new InvalidArgumentException("Expected string");
        }
        return file_get_contents("https://google.com/search?q=$query");
    }
}
