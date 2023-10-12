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
        $res = $f->make("/url?q=https://stackoverflow.com/rotmos&sa=U&ved=2ahUKEwi04--39PCBAxWhcfEDHdukDCoQFnoECAEQBA&usg=AOvVaw1-CR3wteujT38AIxfp5IFq");
        $this->assertEquals(get_class($res), "Query\Stackoverflow");
    }
}
