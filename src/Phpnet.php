<?php

namespace Query;

use DOMDocument;
use League\HTMLToMarkdown\HtmlConverter;
use Exception;

class Phpnet extends Base
{
    public function show()
    {
        $dom  = $this->getDom();
        $body = $dom->getElementById("layout-content");
        //$body = $body->getElementsByTagName
        $tmpDom = new DOMDocument();
        $root = $tmpDom->createElement('html');
        $root = $tmpDom->appendChild($root);
        $root->appendChild($tmpDom->importNode($body, true));
        error_log('before file_put_contents');
        $result = file_put_contents("/tmp/queryresult.html", $tmpDom->saveHTML());
        if ($result === false) {
            throw new Exception("Could not write to /tmp file");
        }
        error_log('before system');
        $output;
        exec("pandoc --from html --to plain /tmp/queryresult.html", $output);
        return $output;
    }
}
