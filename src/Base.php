<?php

namespace Query;

use DOMDocument;
use League\HTMLToMarkdown\HtmlConverter;

class Base
{
    protected $href;
    public function __construct($href)
    {
        echo "Making " . static::class . "\n";
        $this->href = $href;
    }

    public function show()
    {
        $link = $this->getLink();
        echo "Fetching $link...\n";
        $content = @file_get_contents($link);
        if ($content) {
            $dom = new DOMDocument();
            @$dom->loadHTML($content);

            $articles = $dom->getElementsByTagName("article");
            $converter = new HtmlConverter(['strip_tags' => true]);
            foreach($articles as $article) {
                $tmpDom = new DOMDocument();
                $root = $tmpDom->createElement('html');
                $root = $tmpDom->appendChild($root);
                $root->appendChild($tmpDom->importNode($article, true));
                $markdown = $converter->convert($tmpDom->saveHTML());
                echo trim(preg_replace('/^\s*$/m', ' ', $markdown)) . PHP_EOL;
                // TODO: Only show first article
                return;
            }

            $h1s = $dom->getElementsByTagName("h1");
            if ($h1s && $h1s[0]) {
                echo $h1s[0]->textContent . PHP_EOL;
            }
            $ps = $dom->getElementsByTagName("p");
            foreach($ps as $p) {
                echo trim($p->textContent) . PHP_EOL;
            }

            //$crawler = new Crawler($content);
            //$rawText = $crawler->filter("article")->text('', false);
            //echo trim($rawText) . PHP_EOL;
        }
        echo "DONE\n";
    }

    protected function getLink()
    {
        $parts = explode("=", $this->href);
        if (count($parts) === 1) {
            return $this->href;
        }
        $parts = explode("&", $parts[1]);
        return urldecode($parts[0]);
    }
}