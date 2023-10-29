<?php

namespace Query\Effects;

use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

class Cache implements Effect
{
    private $callable;

    public function __construct(callable $c)
    {
        $this->callable = $c;
    }
    public function __invoke(CacheInterface $cache, mixed $arg): mixed
    {
        //error_log('invoking cache');
        if (!is_string($arg)) {
            throw new InvalidArgumentException("Cache key must be a string but got " . gettype($arg));
        }
        $key = hash('fnv1a32', $arg);
        $cachedResult = $cache->get($key);
        if ($cachedResult !== null) {
            return $cachedResult;
        } else {
            $result = call_user_func($this->callable, $arg);
            $cache->set($key, $result, 6300);
            return $result;
        }
    }
}
