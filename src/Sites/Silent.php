<?php

namespace Query\Sites;

use Query\Pipeline;
use function Query\pipe;

class Silent extends Base
{
    public function show(string $href): Pipeline
    {
        // Do nothing.
        return pipe();
    }
}

