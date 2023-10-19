<?php

namespace Query;

use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

class Reddit extends Base
{
    public function show(): Pipe
    {
        return p()->with("TODO");

        $dom = $this->getDom();
        $siteTable = $dom->getElementById("siteTable");
        if (empty($siteTable)) {
            echo $dom->saveHTML();
            throw new RuntimeException("Found no element with id siteTable");
        }
        $commentArea = $siteTable->nextSibling;

        if (empty($commentArea)) {
            throw new RuntimeException("Found no comment area");
        }

        //$crawler = new Symfony\Component\DomCrawler\Crawler($html);
        //$entry = $crawler->filter('.entry.unvoted')->outerHtml();

        $buffer = $this->getTextFromNode($siteTable);
        $buffer .= $this->getTextFromNode($commentArea);
        return $buffer;
    }

    public function getLink(): string
    {
        $href = parent::getLink();
        return str_replace('www.reddit.com', 'old.reddit.com', $href);
    }
}
