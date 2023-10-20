<?php

namespace Query;

use Exception;

class ReturnEarlyException extends Exception
{
    public $payload;
    public function __construct(mixed $payload)
    {
        $this->payload = $payload;
    }
}
