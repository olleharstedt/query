<?php

namespace Query\Sites;

use DOMDocument;
use League\HTMLToMarkdown\HtmlConverter;
use RuntimeException;
use Query\Pipeline;
use function Query\pipe;

class Phpnet extends Base
{
    public function show(string $href): Pipeline
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
