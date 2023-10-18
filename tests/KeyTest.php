<?php

use PHPUnit\Framework\TestCase;
use Query\Factory;
use Query\IO;

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../src/functions.query.php';

/**
 * XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
 * XDEBUG_MODE=coverage php ./vendor/bin/phpunit --coverage-clover cov.xml tests
 */
class KeyTest extends TestCase
{
    /**
     * @covers Query\Factory::make()
     * @covers Query\Factory::abortAtPdf()
     * @covers Query\Factory::getKey()
     * @covers Query\Factory::makeThing()
     * @covers Query\Factory::__construct()
     * @covers Query\Base::__construct()
     * @covers Query\Pipe::__construct()
     * @covers Query\Pipe::run()
     * @covers Query\Pipe::with()
     * @covers Query\ends_with
     * @covers Query\pipe
     * @covers Query\get_domain
     */
    public function testKey()
    {
        $io = $this->createStub(IO::class);

        $f = new Factory($io);
        $url = "/url?q=https://stackoverflow.com/rotmos&sa=U&ved=2ahUKEwi04--39PCBAxWhcfEDHdukDCoQFnoECAEQBA&usg=AOvVaw1-CR3wteujT38AIxfp5IFq";
        $res = $f
            ->make()
            ->with($url)
            ->run();
        $this->assertEquals(get_class($res), "Query\Stackoverflow");
    }
}
