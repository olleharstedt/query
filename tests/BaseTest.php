<?php

use PHPUnit\Framework\TestCase;
use Query\Base;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/functions.query.php';

/**
 * XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
 * XDEBUG_MODE=coverage php ./vendor/bin/phpunit --coverage-clover cov.xml
 * php tests/check_coverage.php cov.xml 80
 *
 * @covers Query\Base
 * @covers Query\FilePutContents
 * @covers Query\Pipe
 * @covers Query\RunPandoc
 * @covers Query\p
 */
class BaseTest extends TestCase
{
    public function testContentToArticles()
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $b = new Base($href);
        $content = <<<HTML
            <html>
            <head></head>
            <body>
            <article>
            <p>Some article</p>
            </article>
            </body>
            </html>
        HTML;
        $c = $b->contentToArticles($content);
        $this->assertCount(1, $c);
    }

    public function testContentToTwoArticles()
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $b = new Base($href);
        $content = <<<HTML
            <html>
            <head></head>
            <body>
            <article>
            <p>Some article</p>
            </article>
            <article>
            <p>Some second article</p>
            </article>
            </body>
            </html>
        HTML;
        $c = $b->contentToArticles($content);
        $this->assertCount(2, $c);
    }

    public function testContentToNoArticles()
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $b = new Base($href);
        $content = <<<HTML
            <html>
            <head></head>
            <body>
            </body>
            </html>
        HTML;
        $c = $b->contentToArticles($content);
        $this->assertCount(0, $c);
    }

    public function testPickFirst()
    {
        $href = '/url?q=https://medium.com';
        $b = new Base($href);
        $c = $b->pickFirst([1, 2, 3]);
        $this->assertEquals(1, $c);
    }

    public function testArticleToString()
    {
        $href = '/url?q=https://medium.com';
        $d = new DOMElement('article');
        $b = new Base($href);
        $s = $b->articleToString($d)
           ->replaceEffectWith('Query\FilePutContents', '')
           ->replaceEffectWith('Query\RunPandoc', 'some content')
           ->run();
        $this->assertEquals('some content', $s);
    }

    public function testArticleToStringEmpty()
    {
        $href = '/url?q=https://medium.com';
        $d = new DOMElement('article');
        $b = new Base($href);
        $s = $b->articleToString($d)
           ->replaceEffectWith('Query\FilePutContents', '')
           ->replaceEffectWith('Query\RunPandoc', '')
           ->run();
        $this->assertEquals('', $s);
    }
}
