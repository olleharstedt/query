<?php

namespace Query;

class Silent extends Base
{
    public function show(string $href): Pipe
    {
        // Do nothing.
        return p();
    }
}

