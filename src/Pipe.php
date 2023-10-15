<?php

namespace Query;

use RuntimeException;

/**
 * TODO: Add support for filter?
 * TODO: Filter at start or filter each step?
 */
class Pipe
{
    private array $callables;
    private mixed $start;

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
            $arg = call_user_func($callable, $arg);
        }
        return $arg;
    }

    public function runAll(): mixed
    {
        $arg = $this->run();
        if ($arg instanceof Pipe) {
            return $arg->runAll();
        } else {
            return $arg;
        }
    }

    /** @psalm-mutation-free */
    protected function callableToString(mixed $callable): string
    {
        if (is_array($callable)) {
            return get_class($callable[0]) . '::' . $callable[1];
        } else {
            throw new RuntimeException("Not implemented");
        }
    }
}
