<?php

namespace Query;

class IO
{
    public function fileGetContents(string $file): string|bool
    {
        return file_get_contents($file);
    }

    public function filePutContents(string $file, string $content): int
    {
        return file_put_contents($file, $content);
    }
}
