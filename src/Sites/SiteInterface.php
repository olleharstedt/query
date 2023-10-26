<?php

namespace Query\Sites;

use Query\Pipeline;
use function Query\p;

interface SiteInterface
{
    public function show(string $href): Pipeline;
}
