<?php

namespace Query\Sites;

interface SiteInterface
{
    public function show(string $href): Pipe;
}
