<?php

namespace Query;

class Silent extends Base
{
    public function show(): Pipe
    {
        // Do nothing.
        return p();
    }
}

