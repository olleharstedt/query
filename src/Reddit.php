<?php

namespace Query;

use Exception;
use Symfony\Component\DomCrawler\Crawler;

class Reddit extends Base
{
    public function show()
    {
        $dom = $this->getDom();
        $siteTable = $dom->getElementById("siteTable");
        if (empty($siteTable)) {
            echo $dom->saveHTML();
            throw new Exception("Found no element with id siteTable");
        }
        $commentArea = $siteTable->nextSibling;

        //$crawler = new Symfony\Component\DomCrawler\Crawler($html);
        //$entry = $crawler->filter('.entry.unvoted')->outerHtml();

        $buffer = $this->getTextFromNode($siteTable);
        $buffer .= $this->getTextFromNode($commentArea);
        return $buffer;
    }
}
