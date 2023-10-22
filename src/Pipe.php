<?php

namespace Query;

use RuntimeException;

/**
 * TODO: Add support for filter? Filter at start or filter each step?
 * TODO: Cache
 * TODO: Fork
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

    /** @psalm-mutation-free */
    public function __construct(array $args)
    {
        //error_log("Constructing pipe with " . json_encode($args));
        $this->callables = $args;
    }

    /** @psalm-mutation-free */
    public function with(mixed $start): static
    {
        $clone = clone $this;
        $clone->start = $start;
        return $clone;
    }

    /** @psalm-mutation-free */
    public function setLogger(LoggerInterface $logger): static
    {
        $clone = clone $this;
        $clone->logger= $logger;
        return $clone;
    }

    /** @psalm-mutation-free */
    public function replaceEffectWith(string $effectName, mixed $result): static
    {
        $c = clone $this;
        $c->replaceEffectWith[$effectName] = $result;
        return $c;
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
                        . json_encode($arg)
                    );
            }
            //var_dump($callable instanceof Effect);
            //var_dump($callable::class);

            if ($callable instanceof Effect
                && array_key_exists($callable::class, $this->replaceEffectWith)) {
                $arg = $this->replaceEffectWith[$callable::class];
            } elseif ($callable instanceof Read
                      && count($this->replaceReadWith) > 0) {
                $arg = array_shift($this->replaceReadWith);
            } elseif ($callable instanceof Write
                      && count($this->replaceWriteWith) > 0) {
                $arg = array_shift($this->replaceWriteWith);
            } else {
                try {
                    $arg = call_user_func($callable, $arg);
                } catch (ReturnEarlyException $ex) {
                    return $ex->payload;
                }
            }
        }
        return $arg;
    }

    public function runAll(): mixed
    {
        $arg = $this->run();
        if ($arg instanceof Pipe) {
            $arg->replaceEffectWith = array_merge($this->replaceEffectWith, $arg->replaceEffectWith);
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

    /** @psalm-mutation-free */
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
