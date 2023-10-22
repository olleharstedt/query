<?php

namespace Query\Effects;

class GetGoogleFromQuery implements Read
{
    public function __invoke(string $query): string|false
    {
        return file_get_contents("https://google.com/search?q=$query");
    }
}
