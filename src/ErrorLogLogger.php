<?php

namespace Query;

class ErrorLogLogger
{
    public function debug($s)
    {
        error_log($s);
    }
}
