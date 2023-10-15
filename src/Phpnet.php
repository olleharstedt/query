<?php

namespace Query;

use DOMDocument;
use League\HTMLToMarkdown\HtmlConverter;
use RuntimeException;

class Phpnet extends Base
{
    public function show(): Pipe
    {
        return pipe()->with("TODO");

        $dom  = $this->getDom();
        $body = $dom->getElementById("layout-content");
        if (empty($body)) {
            throw new RuntimeException("Found no layout-content");
        }
        return $this->getTextFromNode($body);
    }
}
