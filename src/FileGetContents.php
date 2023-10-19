<?php

namespace Query;

class FileGetContents implements Effect
{
    public function __invoke(string $file): string|false
    {
        return file_get_contents($file);
    }
}
