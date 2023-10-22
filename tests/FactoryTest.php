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
 * @covers Query\Pipe
 * @covers Query\ends_with
 * @covers Query\get_domain
 * @covers Query\p
 */
class FactoryTest extends TestCase
{
    public function testAbort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $f = new Factory();
        $f->abortAtPdf('bla bla bla.pdf');
    }

    public function testNotAbort(): void
    {
        $f = new Factory();
        $res = $f->abortAtPdf('bla bla bla.pd');
        $this->assertEquals($res, 'bla bla bla.pd');
    }

    public function testGetKey(): void
    {
        $f = new Factory();
        $string = "/url?q=https://www.youtube.com/watch%3Fv%3DhWMMBku1oKo&sa=U&ved=2ahUKEwjGvO2d04KCAxXfSfEDHbQGA58QtwJ6BAgEEAE&usg=AOvVaw3I1KOljH1SIx__GsC9_-Hx";
        $res = $f->getKey($string);
        $this->assertCount(2, $res);
        $this->assertEquals($res[0], 'youtube.com');
    }

    public function testGetNoKey(): void
    {
        $f = new Factory();
        $string = "";
        $res = $f->getKey($string);
        $this->assertCount(2, $res);
        $this->assertEquals($res[0], null);
    }

    public function testMakeThing(): void
    {
        $f = new Factory();
        $res = $f->makeThing(['wikipedia.org', 'url']);
        $this->assertEquals($res::class, 'Query\Sites\Wikipedia');
    }

    public function testMakeUnknownThing(): void
    {
        $f = new Factory();
        $res = $f->makeThing(['foobar.org', 'url']);
        $this->assertEquals($res::class, 'Query\Sites\Unknown');
    }

    public function testMakeThingNoUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $f = new Factory();
        $res = $f->makeThing(['foobar.org', '']);
    }

    public function testMakeThingNoKey(): void
    {
        $f = new Factory();
        $this->expectException(InvalidArgumentException::class);
        $res = $f->makeThing(['', 'https://blabla']);
    }

    public function testMakeUnknown(): void
    {
        $f = new Factory();
        $p = $f->make();
        $res = $p
            ->with("/url?q=https://www.getsafeonline.org/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q")
            ->run();
        $this->assertEquals($res::class, 'Query\Sites\Unknown');
    }

    public function testMakeStackoverflow(): void
    {
        $f = new Factory();
        $p = $f->make();
        $res = $p
            ->with("/url?q=https://stackoverflow.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q")
            ->run();
        $this->assertEquals($res::class, 'Query\Sites\Stackoverflow');
    }
}
