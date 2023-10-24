<?php

namespace Query;

use RuntimeException;
use Query\Effects\Effect;
use Query\Effects\Write;
use Query\Effects\Read;
use Query\Effects\CacheWrite;
use Query\Effects\Cache;
use Psr\SimpleCache\CacheInterface;

/**
 * TODO: Add support for filter? Filter at start or filter each step?
 * TODO: Cache
 * TODO: Fork
 * TODO: Use PSR logger interface
 */
class Pipe
{
    /** @var Callable[] List of processes in the pipe */
    private array $callables;

    /** @var mixed Start value for the first callable in the pipe */
    private mixed $start;

    private array $replaceEffectWith = [];
    private array $replaceReadWith   = [];
    private array $replaceWriteWith  = [];

    /** @var ?LoggerInterface */
    private $logger;

    /** @var ?CacheInterface */
    private $cache;

    public function __construct(array $args)
    {
        //error_log("Constructing pipe with " . json_encode($args));
        $this->callables = $args;
    }

    public function with(mixed $start): static
    {
        $this->start = $start;
        return $this;
    }

    public function setLogger(LoggerInterface $logger): static
    {
        $this->logger= $logger;
        return $this;
    }

    public function setCache(CacheInterface $c): static
    {
        $this->cache = $c;
        return $this;
    }

    public function replaceEffectWith(string $effectName, mixed $result): static
    {
        $this->replaceEffectWith[$effectName] = $result;
        return $this;
    }

    public function replaceWriteWith(mixed $result): static
    {
        $this->replaceWriteWith[] = $result;
        return $this;
    }

    public function replaceReadWith(mixed $result): static
    {
        $this->replaceReadWith[] = $result;
        return $this;
    }

    public function run(): mixed
    {
        $arg = $this->start ?? null;
        //error_log("Running pipe with " . json_encode($arg));
        foreach ($this->callables as $callable) {
            if ($this->logger) {
                $this
                    ->logger
                    ->debug(
                        $this->callableToString($callable) . ' - '
                        . substr(json_encode($arg), 0, 200)
                    );
            }
            try {
                if ($callable instanceof Effect
                    && array_key_exists($callable::class, $this->replaceEffectWith)) {
                    $arg = $this->replaceEffectWith[$callable::class];
                } elseif ($callable instanceof Cache) {
                    if (empty($this->cache)) {
                        throw new RuntimeException("Cache not set");
                    }
                    $arg = $callable($this->cache, $arg);
                } elseif ($callable instanceof CacheWrite) {
                    if (empty($this->cache)) {
                        throw new RuntimeException("Cache not set");
                    }
                    $callable->setCache($this->cache);
                    $arg = call_user_func($callable, $arg);
                } elseif ($callable instanceof Read
                    && count($this->replaceReadWith) > 0) {
                    $arg = array_shift($this->replaceReadWith);
                } elseif ($callable instanceof Write
                    && count($this->replaceWriteWith) > 0) {
                    $arg = array_shift($this->replaceWriteWith);
                } else if ($callable instanceof Read && $this->cache !== null) {
                    if (!is_string($arg)) {
                        throw new RuntimeException("Cannot cache with non-string key");
                    }
                    error_log("Using cache for key " . $arg);
                    // TODO: Cache key is wrong here
                    $cachedArg = $this->cache->get(hash('md5', $arg));
                    if ($cachedArg === null) {
                        error_log("Found no cached content");
                        $arg = call_user_func($callable, $arg);
                    } else {
                        $arg = $cachedArg;
                    }
                } else {
                    $arg = call_user_func($callable, $arg);
                }
            } catch (ReturnEarlyException $ex) {
                return $ex->payload;
            }
        }
        return $arg;
    }

    /**
     * Run pipes recurisvely.
     * Also propagating cache, logger from top pipe.
     */
    public function runAll(): mixed
    {
        error_log("runAll");
        $arg = $this->run();
        if ($arg instanceof Pipe) {
            error_log("Setting up child pipe");
            $arg->replaceEffectWith = array_merge($this->replaceEffectWith, $arg->replaceEffectWith);
            $arg->cache  = $this->cache;
            $arg->logger = $this->logger;
            return $arg->runAll();
        } else {
            return $arg;
        }
    }

    public static function abortIfEmpty(mixed $payload): mixed
    {
        if (empty($payload)) {
            throw new ReturnEarlyException(null);
        } else {
            return $payload;
        }
    }

    protected function callableToString(mixed $callable): string
    {
        if (is_array($callable)) {
            return get_class($callable[0]) . '::' . $callable[1];
        } elseif (is_callable($callable)) {
            return $callable::class;
        } else {
            throw new RuntimeException("Not implemented: " . get_class($callable));
        }
    }
}
