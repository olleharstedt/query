<?php

namespace Query\Effects;

use InvalidArgumentException;

class FilePutContents implements Write
{
    private string $file;
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function __invoke(mixed $content): int|false
    {
        if (!is_string($content)) {
            throw new InvalidArgumentException("Expected string");
        }
        return file_put_contents($this->file, $content);
    }
}
