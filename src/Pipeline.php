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
class Pipeline
{
    /** @var Callable[] List of processes in the pipe */
    private array $callables;

    /** @var mixed Start value for the first callable in the pipe */
    private mixed $start;

    /** @var array Class or interface to mixed result to be returned */
    private array $replaceEffectWith = [];

    /** @var ?LoggerInterface */
    private $logger;

    /** @var ?CacheInterface */
    private $cache;

    /** @var bool If set to true, will not throw exception if a Cache effect happens without a set $cache property */
    //private $ignoreCache = false;

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

    public function replaceEffect(string $effectName, mixed $result): static
    {
        $this->replaceEffectWith[$effectName] = $result;
        return $this;
    }

    public function run(): mixed
    {
        $arg = $this->start ?? null;
        foreach ($this->callables as $callable) {
            $this->doLogging($callable, $arg);

            try {
                /*
                if (get_class($callable) === 'Closure') {
                    $refl = new \ReflectionFunction($callable);
                    error_log($refl->getName());
                } else {
                    error_log(get_class($callable));
                }
                 */
                //error_log('implements: ' . (current(class_implements($callable)) ?? []));
                //error_log(json_encode($this->replaceEffectWith));

                if ($callable instanceof \Query\Effects\Effect
                    && array_key_exists($callable::class, $this->replaceEffectWith)
                ) {
                    $arg = $this->replaceEffectWith[$callable::class];
                } elseif ($callable instanceof \Query\Effects\Effect
                          // TODO: array filter instead of current
                          && array_key_exists(current(class_implements($callable)), $this->replaceEffectWith)
                ) {
                    $arg = $this->replaceEffectWith[current(class_implements($callable))];
                } elseif ($callable instanceof Cache) {
                    if (empty($this->cache)) {
                        throw new RuntimeException("Cache not set");
                    }
                    if ($this->logger) {
                        $this->logger->debug("[Cache called]");
                    }
                    $arg = $callable($this->cache, $arg);
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
        //error_log("runAll");
        $arg = $this->run();
        if ($arg instanceof Pipeline) {
            //error_log("Setting up child pipe");
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
        } elseif (get_class($callable) === 'Closure') {
            $refl = new \ReflectionFunction($callable);
            return $refl->getName();
        } elseif (is_callable($callable)) {
            return $callable::class;
        } else {
            throw new RuntimeException("Not implemented: " . get_class($callable));
        }
    }

    protected function doLogging(callable $callable, mixed $arg): void
    {
        if ($this->logger) {
            $this
                ->logger
                ->debug(
                    $this->callableToString($callable) . ' - '
                    . substr(json_encode($arg), 0, 200)
                );
        }
    }
}
