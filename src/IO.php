<?php

namespace Query;

class IO
{
    public function fileGetContents($file): string|bool
    {
        return file_get_contents($file);
    }

    public function filePutContents($file, $content): int
    {
        return file_put_contents($file, $content);
    }
}
