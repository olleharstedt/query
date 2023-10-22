<?php

namespace Query\Effects;

interface Effect
{
    public function __invoke(mixed $arg): mixed;
}
