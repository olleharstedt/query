<?php

namespace Query;

class Pipe
{
    private $callables;
    private $start;

    public function __construct($args)
    {
        $this->callables = $args;
    }

    public function with($start)
    {
        $this->start = $start;
        return $this;
    }

    public function run()
    {
        $arg = $this->start ?? null;
        foreach ($this->callables as $callable) {
            $arg = call_user_func($callable, $arg);
        }
        return $arg;
    }
}
