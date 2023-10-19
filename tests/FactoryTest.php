<?php

use PHPUnit\Framework\TestCase;
use Query\Factory;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/functions.query.php';

/**
 * XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
 * XDEBUG_MODE=coverage php ./vendor/bin/phpunit --coverage-clover cov.xml
 * php tests/check_coverage.php cov.xml 80
 *
 * @covers Query\Base
 * @covers Query\Factory
 * @covers Query\ends_with
 * @covers Query\get_domain
 */
class FactoryTest extends TestCase
{
    public function testAbort()
    {
        $this->expectException(InvalidArgumentException::class);

        $f = new Factory();
        $f->abortAtPdf('bla bla bla.pdf');
    }

    public function testNotAbort()
    {
        $f = new Factory();
        $res = $f->abortAtPdf('bla bla bla.pd');
        $this->assertEquals($res, 'bla bla bla.pd');
    }

    public function testGetKey()
    {
        $f = new Factory();
        $string = "/url?q=https://www.youtube.com/watch%3Fv%3DhWMMBku1oKo&sa=U&ved=2ahUKEwjGvO2d04KCAxXfSfEDHbQGA58QtwJ6BAgEEAE&usg=AOvVaw3I1KOljH1SIx__GsC9_-Hx";
        $res = $f->getKey($string);
        $this->assertCount(2, $res);
        $this->assertEquals($res[0], 'youtube.com');
    }

    public function testGetNoKey()
    {
        $f = new Factory();
        $string = "";
        $res = $f->getKey($string);
        $this->assertCount(2, $res);
        $this->assertEquals($res[0], null);
    }

    public function testMakeThing()
    {
        $f = new Factory();
        $res = $f->makeThing(['wikipedia.org', 'url']);
        $this->assertEquals($res::class, 'Query\Wikipedia');
    }
}
