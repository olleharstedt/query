<?php

namespace Query;

use Symfony\Component\DomCrawler\Crawler;

class Stackoverflow extends Base
{
    public function processContent(string $content): string
    {
        $buffer = "";
        $buffer .= "Got content\n";
        $crawler = new Crawler($content);
        $buffer .= "POSTCELL\n";
        $rawText = $crawler->filter("div.postcell")->text('', false);
        $buffer .= trim(preg_replace('/^\s*$/m', ' ', $rawText)) . PHP_EOL;
        $buffer .= "ANSWERCELLS\n";
        $rawText = $crawler->filter("div.answercell")->text('', false);
        $buffer .= trim(preg_replace('/^\s*$/m', ' ', $rawText)) . PHP_EOL;
        return $buffer;
    }

    public function show(string $href): Pipe
    {
        return p(
            $this->getLink(...),
            new FileGetContents(),
            $this->processContent(...)
        )->with($href);

        $buffer = "";
        $link = $this->getLink();
        $buffer .= "Fetching $link...\n";
        $content = file_get_contents($link);
        if ($content) {
            $buffer .= "Got content\n";
            $crawler = new Crawler($content);
            $buffer .= "POSTCELL\n";
            $rawText = $crawler->filter("div.postcell")->text('', false);
            $buffer .= trim(preg_replace('/^\s*$/m', ' ', $rawText)) . PHP_EOL;
            $buffer .= "ANSWERCELLS\n";
            $rawText = $crawler->filter("div.answercell")->text('', false);
            $buffer .= trim(preg_replace('/^\s*$/m', ' ', $rawText)) . PHP_EOL;

            //$dom = new DOMDocument();
            //@$dom->loadHTML($content);
            //$mainbar = $dom->getElementById("mainbar");
            //$divs = $mainbar->getElementsByTagName("div");

            //$xpath = new DOMXpath($myDomDocument);
            //$cubesWithCurrencies = $xpath->query('//cube[@currency]');

            //foreach ($divs as $div) {
                //$t = trim(preg_replace('/\s+/', ' ', $div->textContent));
                //if ($t) {
                    //$buffer .= $t . PHP_EOL;
                //}
            //}
        } else {
            error_log("No content");
        }
        $buffer .= "DONE\n";
        return $buffer;
    }
}
