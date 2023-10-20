<?php

namespace Query;

use DOMDocument;
use DOMNode;
use DOMElement;
use DOMNodeList;
use Exception;
use RuntimeException;
use InvalidArgumentException;
use Traversable;
use League\HTMLToMarkdown\HtmlConverter;

class Base implements SiteInterface
{
    protected string $href;

    public function __construct(string $href)
    {
        //error_log("Making " . static::class);
        $this->href = $href;
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

    public function pickFirst(?iterable $things): mixed
    {
        foreach ($things as $_) {
            return $_;
        }
        return null;
    }

    public function articleToDom(DOMElement $article): DOMDocument
    {
        $tmpDom = new DOMDocument();
        $root = $tmpDom->createElement('html');
        $root = $tmpDom->appendChild($root);
        $root->appendChild($tmpDom->importNode($article, true));
        return $tmpDom;
    }

    public function articleToString(DOMElement $article): Pipe
    {
        return p(
            $this->articleToDom(...),
            $this->domToMarkdown(...),
            (new FilePutContents('/tmp/tmp.md')),
            (new RunPandoc())->from('markdown')->to('plain')->inputFile('/tmp/tmp.md')
        )->with($article);
    }

    public function domToMarkdown(DOMDocument $dom): string
    {
        $converter = new HtmlConverter(['strip_tags' => true]);
        return $converter->convert($dom->saveHTML());
    }

    public function show(): Pipe
    {
        return p(
            $this->getLink(...),
            new FileGetContents(),
            $this->contentToArticles(...),
            $this->pickFirst(...),
            Pipe::abortIfEmpty(...),
            $this->articleToString(...)
        );

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

    public function getLink(): string
    {
        $parts = explode("=", $this->href);
        if (count($parts) === 1) {
            return $this->href;
        }
        $parts = explode("&", $parts[1]);
        return urldecode($parts[0]);
    }

    public function getDom(): DOMDocument
    {
        $link = $this->getLink();
        error_log("Fetching $link...");
        $content = file_get_contents($link);
        if (empty($content)) {
            throw new RuntimeException("Could not get content from link $link");
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        return $dom;
    }

    public function getTextFromNode(DOMNode $node): string
    {
        $tmpDom = new DOMDocument();
        $root = $tmpDom->createElement('html');
        $root = $tmpDom->appendChild($root);
        $root->appendChild($tmpDom->importNode($node, true));
        $result = file_put_contents("/tmp/queryresult.html", $tmpDom->saveHTML());
        if ($result === false) {
            throw new Exception("Could not write to /tmp file");
        }
        $output = '';
        exec("pandoc --from html --to plain /tmp/queryresult.html", $output);
        return implode("\n", $output);
    }
}
