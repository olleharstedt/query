<?php

namespace Query;

interface LoggerInterface
{
    public function debug(string $message): void;
}
