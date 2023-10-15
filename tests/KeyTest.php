<?php

use PHPUnit\Framework\TestCase;
use Query\Factory;
use Query\IO;

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../src/functions.query.php';

class KeyTest extends TestCase
{
    /**
     * @covers Query\Factory::make
     */
    public function testKey()
    {
        $f = new Factory(new IO());
        $url = "/url?q=https://stackoverflow.com/rotmos&sa=U&ved=2ahUKEwi04--39PCBAxWhcfEDHdukDCoQFnoECAEQBA&usg=AOvVaw1-CR3wteujT38AIxfp5IFq";
        $res = $f
            ->make()
            ->with($url)
            ->run();
        $this->assertEquals(get_class($res), "Query\Stackoverflow");
    }
}
