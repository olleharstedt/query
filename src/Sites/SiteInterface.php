<?php

namespace Query;

interface SiteInterface
{
    public function show(string $href): Pipe;
}
