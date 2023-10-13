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
        return $this->getTextFromNode($body);
    }
}
