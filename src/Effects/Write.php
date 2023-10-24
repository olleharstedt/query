<?php

namespace Query\Effects;

interface Write extends Effect
{
    public function __invoke(mixed $arg): mixed;
}
