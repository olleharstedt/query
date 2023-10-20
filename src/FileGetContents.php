<?php

namespace Query;

class FileGetContents implements Effect
{
    public function __invoke(string $file): string|false
    {
        //throw new \Exception('FileGetContents');
        return file_get_contents($file);
    }
}
