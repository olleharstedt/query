<?php

namespace Query;

class FilePutContents
{
    private string $file;
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function __invoke(string $content): int|false
    {
        return file_put_contents($this->file, $content);
    }
}
