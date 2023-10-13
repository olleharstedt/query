<?php

namespace Query;

use DOMDocument;
use Exception;
use League\HTMLToMarkdown\HtmlConverter;

class Base
{
    protected $href;
    public function __construct($href)
    {
        error_log("Making " . static::class);
        $this->href = $href;
    }

    public function show()
    {
        $buffer = "";
        $link = $this->getLink();
        $buffer .= "Fetching $link...\n";
        $content = @file_get_contents($link);
        if ($content) {
            $dom = new DOMDocument();
            @$dom->loadHTML($content);

            $articles = $dom->getElementsByTagName("article");
            $converter = new HtmlConverter(['strip_tags' => true]);
            if ($articles) {
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
                    system("pandoc --from markdown --to plain /tmp/queryresult.md");

                    //$buffer .= trim(preg_replace('/^\s*$/m', ' ', $markdown)) . PHP_EOL;
                    // TODO: Only show first article
                    return;
                }
            }

            $h1s = $dom->getElementsByTagName("h1");
            if ($h1s && $h1s[0]) {
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
    }

    public function getLink()
    {
        $parts = explode("=", $this->href);
        if (count($parts) === 1) {
            return $this->href;
        }
        $parts = explode("&", $parts[1]);
        return urldecode($parts[0]);
    }

    /**
     * @return DOMDocument
     */
    public function getDom()
    {
        $link = $this->getLink();
        error_log("Fetching $link...");
        $content = file_get_contents($link);
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        return $dom;
    }

    public function getTextFromNode($node)
    {
        $tmpDom = new DOMDocument();
        $root = $tmpDom->createElement('html');
        $root = $tmpDom->appendChild($root);
        $root->appendChild($tmpDom->importNode($node, true));
        $result = file_put_contents("/tmp/queryresult.html", $tmpDom->saveHTML());
        if ($result === false) {
            throw new Exception("Could not write to /tmp file");
        }
        $output;
        exec("pandoc --from html --to plain /tmp/queryresult.html", $output);
        return implode("\n", $output);
    }
}
