<?php

namespace Query;

use DOMDocument;
use League\HTMLToMarkdown\HtmlConverter;
use RuntimeException;

class Phpnet extends Base
{
    public function show(string $href): Pipe
    {
        return p()->with("TODO");

        $dom  = $this->getDom();
        $body = $dom->getElementById("layout-content");
        if (empty($body)) {
            throw new RuntimeException("Found no layout-content");
        }
        return $this->getTextFromNode($body);
    }
}
