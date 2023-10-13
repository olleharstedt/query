<?php

namespace Query;

use RuntimeException;

/**
 * TODO: Add support for filter?
 * TODO: Filter at start or filter each step?
 */
class Pipe
{
    private $callables;
    private $start;
    private $logger;

    public function __construct($args)
    {
        $this->callables = $args;
    }

    public function with($start)
    {
        $this->start = $start;
        return $this;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function run()
    {
        $arg = $this->start ?? null;
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

    protected function callableToString($callable)
    {
        if (is_array($callable)) {
            return get_class($callable[0]) . '::' . $callable[1];
        } else {
            throw new RuntimeException("Not implemented");
        }
    }
}
