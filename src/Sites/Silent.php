<?php

namespace Query\Sites;

class Silent extends Base
{
    public function show(string $href): Pipe
    {
        // Do nothing.
        return p();
    }
}

