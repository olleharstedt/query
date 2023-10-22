<?php

namespace Query\Effects;

class FileGetContents implements Read
{
    public function __invoke(string $file): string|false
    {
        //throw new \Exception('FileGetContents');
        return file_get_contents($file);
    }
}
