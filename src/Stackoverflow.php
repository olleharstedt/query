<?php

namespace Query;

class Stackoverflow extends Base
{
    public function show()
    {
        $link = $this->getLink();
        echo "Fetching $link...\n";
        $content = file_get_contents($link);
        if ($content) {
            echo "Got content\n";
            $crawler = new Crawler($content);
            echo "POSTCELL\n";
            $rawText = $crawler->filter("div.postcell")->text('', false);
            echo trim(preg_replace('/^\s*$/m', ' ', $rawText)) . PHP_EOL;
            echo "ANSWERCELLS\n";
            $rawText = $crawler->filter("div.answercell")->text('', false);
            echo trim(preg_replace('/^\s*$/m', ' ', $rawText)) . PHP_EOL;

            //$dom = new DOMDocument();
            //@$dom->loadHTML($content);
            //$mainbar = $dom->getElementById("mainbar");
            //$divs = $mainbar->getElementsByTagName("div");

            //$xpath = new DOMXpath($myDomDocument);
            //$cubesWithCurrencies = $xpath->query('//cube[@currency]');

            //foreach ($divs as $div) {
                //$t = trim(preg_replace('/\s+/', ' ', $div->textContent));
                //if ($t) {
                    //echo $t . PHP_EOL;
                //}
            //}
        } else {
        }
        echo "DONE\n";
    }
}

