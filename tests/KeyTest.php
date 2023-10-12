<?php

use PHPUnit\Framework\TestCase;
use Query\Factory;

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../src/functions.query.php';

class KeyTest extends TestCase
{
    public function testKey()
    {
        $f = new Factory();
        $res = $f->make("https://stackoverflow.com/questions/57199457/psr-for-class-names-and-filenames-of-similar-classes");
        $this->assertEquals(get_class($res), "Query\Stackoverflow");
    }
}
