<?php

namespace Query\Effects;

class FilePutContents implements Write
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
