<?php

namespace Query\Effects;

use InvalidArgumentException;

class FileGetContents implements Read
{
    public function __invoke(mixed $file): string|false
    {
        if (!is_string($file)) {
            throw new InvalidArgumentException("Expected string");
        }
        //throw new \Exception('FileGetContents');
        return file_get_contents($file);
    }
}
