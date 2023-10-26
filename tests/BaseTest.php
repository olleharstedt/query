<?php

use PHPUnit\Framework\TestCase;
use Query\Sites\Base;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/functions.query.php';

/**
 * XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
 * XDEBUG_MODE=coverage php ./vendor/bin/phpunit --coverage-clover cov.xml
 * php tests/check_coverage.php cov.xml 80
 *
 * @covers Query\Base
 * @covers Query\FilePutContents
 * @covers Query\Pipeline
 * @covers Query\RunPandoc
 * @covers Query\p
 * @covers Query\ReturnEarlyException
 */
class BaseTest extends TestCase
{
    public function testContentToArticles(): void
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $b = new Base();
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
        $c = $b->contentToArticles($content) ?? [];
        $this->assertCount(1, $c);
    }

    public function testContentToTwoArticles(): void
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $b = new Base();
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
        $c = $b->contentToArticles($content) ?? [];
        $this->assertCount(2, $c);
    }

    public function testContentToNoArticles(): void
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $b = new Base();
        $content = <<<HTML
            <html>
            <head></head>
            <body>
            </body>
            </html>
        HTML;
        $c = $b->contentToArticles($content) ?? [1, 2, 3];
        $this->assertCount(0, $c);
    }

    public function testPickFirst(): void
    {
        $href = '/url?q=https://medium.com';
        $b = new Base();
        $c = $b->pickFirst([1, 2, 3]);
        $this->assertEquals(1, $c);
    }

    public function testArticleToString(): void
    {
        $href = '/url?q=https://medium.com';
        $d = new DOMElement('article');
        $b = new Base();
        $s = $b->articleToString($d)
           ->replaceEffect('Query\Effects\FilePutContents', '')
           ->replaceEffect('Query\Effects\RunPandoc', 'some content')
           ->run();
        $this->assertEquals('some content', $s);
    }

    public function testArticleToStringEmpty(): void
    {
        $href = '/url?q=https://medium.com';
        $d = new DOMElement('article');
        $b = new Base();
        $s = $b->articleToString($d)
           ->replaceEffect('Query\Effects\FilePutContents', '')
           ->replaceEffect('Query\Effects\RunPandoc', '')
           ->run();
        $this->assertEquals('', $s);
    }

    public function testDomToMarkdown(): void
    {
        $d = new DOMDocument();
        $html = $d->createElement('html');
        $p = $d->createElement('p');
        $p->textContent = 'Bla bla';
        $html->appendChild($p);
        $d->appendChild($html);

        $b = new Base();
        $md = $b->domToMarkdown($d);
        $this->assertEquals('Bla bla', $md);
    }

    public function testShowNull(): void
    {
        $href = '/url?q=https://medium.com';
        $b = new Base();
        $result = $b
            ->show($href)
            ->replaceEffect('Query\Effects\Cache', 'bla bla bla')
            ->runAll();
        $this->assertNull($result);
    }

    public function testShowBasic(): void
    {
        $href = '/url?q=https://medium.com';
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
        $b = new Base();
        $result = $b
            ->show($href)
            ->replaceEffect('Query\Effects\Cache', $content)  // First file_get_contents
            ->replaceEffect('Query\Effects\FilePutContents', '')
            ->replaceEffect('Query\Effects\RunPandoc', 'Some article')
            ->runAll();
        $this->assertEquals('Some article', $result);
    }

    public function testGetLink(): void
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $b = new Base();
        $link = $b->getLink($href);
        $this->assertEquals('https://medium.com/checkawebsite/', $link);
    }

    public function testGetLinkEmpty(): void
    {
        $href = '';
        $b = new Base();
        $link = $b->getLink($href);
        $this->assertEmpty($link);
    }

    public function testGetDom(): void
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $b = new Base();
        $content = <<<HTML
            <html>
          <body>
            <p>Some content</p>
          </body>
        </html>
        HTML;

        $result = $b
            ->getDom($href)
            ->replaceEffect('Query\Effects\Read', $content)
            ->runAll();

        $d = new DOMDocument();
        $html = $d->createElement('html');
        $body = $d->createElement('body');
        $p = $d->createElement('p');
        $p->textContent = 'Some content';
        $body->appendChild($p);
        $html->appendChild($body);
        $d->appendChild($html);

        $this->assertEquals($d, $result);
    }

    public function testGetDomEmpty(): void
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $b = new Base();
        $result = $b
            ->getDom($href)
            ->replaceEffect('Query\Effects\Read', null)
            ->runAll();
        $this->assertEquals('', $result);
    }

    /*
    public function testNodeToDOM(): void
    {
        $href = '/url?q=https://medium.com/checkawebsite/&sa=U&ved=2ahUKEwjy_7PY1oKCAxWCQvEDHVBqA1YQFnoECAIQAg&usg=AOvVaw2AKolUT0FelA3t0w9iZD-Q';
        $d = new DOMNode();
        $b = new Base();
    }
    */
}
