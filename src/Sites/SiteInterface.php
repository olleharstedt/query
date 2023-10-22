<?php

namespace Query\Sites;

use Query\Pipe;
use function Query\p;

interface SiteInterface
{
    public function show(string $href): Pipe;
}
