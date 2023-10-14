<?php

namespace Query;

class IO
{
    public function fileGetContents($file): string|bool
    {
	    return file_get_contents($file);
    }
}
