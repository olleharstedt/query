<?php

namespace Query;

class ErrorLogLogger implements LoggerInterface
{
    public function debug(string $message): void
    {
        error_log($message);
    }
}
