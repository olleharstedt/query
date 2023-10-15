<?php

namespace Query;

class FilePutContents
{
    private $file;
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function __invoke(string $content)
    {
        return file_put_contents($this->file, $content);
    }
}
