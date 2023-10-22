<?php

namespace Query\Sites;

use Query\Pipe;
use function Query\p;

class Silent extends Base
{
    public function show(string $href): Pipe
    {
        // Do nothing.
        return p();
    }
}

