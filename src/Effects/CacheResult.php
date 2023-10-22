<?php

namespace Query\Effects;

use RuntimeException;
use Psr\SimpleCache\CacheInterface;

final class CacheResult implements CacheWrite
{
    private ?CacheInterface $cache = null;
    private string $key;
    private int $time = 3600;
    public function __construct(string $key)
    {
        $this->key = $key;
    }
    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }
    public function __invoke(mixed $result): mixed
    {
        if (empty($this->cache)) {
            throw new RuntimeException("Cache not set");
        }
        $this->cache->set(hash('md5', $this->key), $result, $this->time);
        return $result;
    }
}
