<?php

namespace Query\Sites;

use DOMDocument;
use DOMNode;
use DOMElement;
use DOMNodeList;
use Exception;
use RuntimeException;
use InvalidArgumentException;
use Traversable;
use League\HTMLToMarkdown\HtmlConverter;
use Query\Pipeline;
use function Query\pipe;
use function Query\pick_first;
use function Query\abort_at_empty;
use Query\Effects\FileGetContents;
use Query\Effects\FilePutContents;
use Query\Effects\RunPandoc;
use Query\Effects\CacheResult;
use Query\Effects\Cache;

class Base implements SiteInterface
{
    public function contentToDom(?string $content): ?DOMDocument
    {
        if (empty($content)) {
            return null;
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        return $dom;
    }

    public function contentToArticles(string $content): ?DOMNodeList
    {
        if (empty($content)) {
            return null;
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        return $dom->getElementsByTagName("article");
    }

    public function articleToDom(DOMElement $article): DOMDocument
    {
        $tmpDom = new DOMDocument();
        $root = $tmpDom->createElement('html');
        $root = $tmpDom->appendChild($root);
        $root->appendChild($tmpDom->importNode($article, true));
        return $tmpDom;
    }

    public function articleToString(DOMElement $article): Pipeline
    {
        return pipe(
            $this->articleToDom(...),
            $this->domToMarkdown(...),
            (new FilePutContents('/tmp/tmp.md')),
            (new RunPandoc())->from('markdown')->to('plain')->inputFile('/tmp/tmp.md')
        )->from($article);
    }

    public function domToMarkdown(DOMDocument $dom): string
    {
        $converter = new HtmlConverter(['strip_tags' => true]);
        return $converter->convert($dom->saveHTML());
    }

    public function show(string $href): Pipeline
    {
        return pipe(
            $this->getLink(...),
            new Cache(new FileGetContents()),
            $this->contentToArticles(...),
            pick_first(...),
            abort_at_empty(...),
            $this->articleToString(...)
        )->from($href);

        /*
        $buffer = "";
        $link = $this->getLink();
        $buffer .= "Fetching $link...\n";
        $content = @file_get_contents($link);
        if ($content) {
            $dom = new DOMDocument();
            @$dom->loadHTML($content);

            $articles = $dom->getElementsByTagName("article");
            $converter = new HtmlConverter(['strip_tags' => true]);
            if (count($articles) > 0) {
                foreach($articles as $article) {
                    $tmpDom = new DOMDocument();
                    $root = $tmpDom->createElement('html');
                    $root = $tmpDom->appendChild($root);
                    $root->appendChild($tmpDom->importNode($article, true));
                    $markdown = $converter->convert($tmpDom->saveHTML());
                    $result = file_put_contents("/tmp/queryresult.md", $markdown);
                    if ($result === false) {
                        throw new Exception("Could not write to /tmp file");
                    }
                    $output = '';
                    exec("pandoc --from markdown --to plain /tmp/queryresult.md", $output);

                    //$buffer .= trim(preg_replace('/^\s*$/m', ' ', $markdown)) . PHP_EOL;
                    // TODO: Only show first article
                    return implode("\n", $output);
                }
            }

            $h1s = $dom->getElementsByTagName("h1");
            if (isset($h1s[0])) {
                $buffer .= $h1s[0]->textContent . PHP_EOL;
            }
            $ps = $dom->getElementsByTagName("p");
            foreach($ps as $p) {
                $buffer .= trim($p->textContent) . PHP_EOL;
            }

            //$crawler = new Crawler($content);
            //$rawText = $crawler->filter("article")->text('', false);
            //$buffer .= trim($rawText) . PHP_EOL;
        } else {
            printf("Got content %s\n", json_encode($content));
        }
        $buffer .= "DONE\n";
        return $buffer;
         */
    }

    public function getLink(string $href): string
    {
        $parts = explode("=", $href);
        if (count($parts) === 1) {
            return $href;
        }
        $parts = explode("&", $parts[1]);
        return urldecode($parts[0]);
    }

    public function getDom(string $href): Pipeline
    {
        return pipe(
            $this->getLink(...),
            new FileGetContents(),
            $this->contentToDom(...)
        )->from($href);
    }

    /*
    public function getDom(): DOMDocument
    {
        $link = $this->getLink();
        $content = file_get_contents($link);
        if (empty($content)) {
            throw new RuntimeException("Could not get content from link $link");
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        return $dom;
    }
    */

    public function nodeToDOM(DOMNode $node): DOMDocument
    {
        $dom = new DOMDocument();
        $root = $dom->createElement('html');
        $root = $dom->appendChild($root);
        $root->appendChild($dom->importNode($node, true));
        return $dom;
    }

    public function DOMToHtml(DOMDocument $dom): string
    {
        return $dom->saveHTML();
    }

    public function getTextFromNode(DOMNode $node): Pipeline
    {
        // TODO: Should be configurable.
        $tmpFile = '/tmp/queryresult.html';
        return pipe(
            $this->nodeToDOM(...),
            $this->DOMToHtml(...),
            new FilePutContents($tmpFile),
            (new RunPandoc())->from('html')->to('plain')->inputFile($tmpFile)
        );
    }
}
