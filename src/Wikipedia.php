<?php

namespace Query;

use DOMDocument;
use League\HTMLToMarkdown\HtmlConverter;
use Exception;

class Wikipedia extends Base
{
    public function show()
    {
        $link = $this->getLink();
        echo "Fetching $link...\n";
        $content = file_get_contents($link);
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $body = $dom->getElementById("mw-content-text");

        $converter = new HtmlConverter(['strip_tags' => true]);
        $tmpDom = new DOMDocument();
        $root = $tmpDom->createElement('html');
        $root = $tmpDom->appendChild($root);
        $root->appendChild($tmpDom->importNode($body, true));
        $markdown = $converter->convert($tmpDom->saveHTML());
        $result = file_put_contents("/tmp/queryresult.md", $markdown);
        if ($result === false) {
            throw new Exception("Could not write to /tmp file");
        }

        system("pandoc --from markdown --to plain /tmp/queryresult.md");

        //echo trim(preg_replace('/^\s*$/m', ' ', $markdown)) . PHP_EOL;

        /*
        $clearText = preg_replace( "/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($body->textContent))) );
        echo trim(preg_replace('/^\s*$/m', ' ', $clearText)) . PHP_EOL;
         */
    }
}
