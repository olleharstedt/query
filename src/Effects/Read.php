<?php

namespace Query\Effects;

interface Read extends Effect
{
    public function __invoke(mixed $arg): mixed;
}
