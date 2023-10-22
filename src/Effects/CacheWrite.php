<?php

namespace Query\Effects;

use Psr\SimpleCache\CacheInterface;

interface CacheWrite extends Write
{
    public function setCache(CacheInterface $cache): void;
}
