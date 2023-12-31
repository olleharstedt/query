<?php

use PHPUnit\Framework\TestCase;
use Query\Factory;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/functions.query.php';

/**
 * XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
 * XDEBUG_MODE=coverage php ./vendor/bin/phpunit --coverage-clover cov.xml
 */
class KeyTest extends TestCase
{
    /**
     * @covers Query\Factory
     * @covers Query\Pipeline
     * @covers Query\ends_with
     * @covers Query\pipe
     * @covers Query\get_domain
     */
    public function testKey(): void
    {
        $f = new Factory();
        $url = "/url?q=https://stackoverflow.com/rotmos&sa=U&ved=2ahUKEwi04--39PCBAxWhcfEDHdukDCoQFnoECAEQBA&usg=AOvVaw1-CR3wteujT38AIxfp5IFq";
        $res = $f
            ->make()
            ->from($url)
            ->run();
        $this->assertEquals(get_class($res), "Query\Sites\Stackoverflow");
    }
}
