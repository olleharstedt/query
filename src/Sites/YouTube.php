<?php

namespace Query\Sites;

use Query\Pipe;
use function Query\p;

class YouTube extends Base
{
    public function show(string $href): Pipe
    {
        return p()->with("Can't show youtube");

        //TODO
        return "Can't show youtube\n";

        $parts = explode("=", $this->href);
        $parts = explode("&", $parts[1]);
        $link = urldecode($parts[0]) . PHP_EOL;
        //$content = file_get_contents($link);

        $client = Client::createFirefoxClient();
        $client->request('GET', $link);
        $html = $client->getInternalResponse()->getContent();
        //$crawler = new Symfony\Component\DomCrawler\Crawler($html);
        // you can use following to get the whole HTML
        //$crawler->outerHtml();
        // or specific parts
        //echo $crawler->filter('h1')->outerHtml() . PHP_EOL;
        //echo $crawler->filter('h1')->html() . PHP_EOL;
        $crawler = $client->waitFor('h1');
        return json_encode($crawler->filter('h1')->text()) . PHP_EOL;

        //$dom = new DOMDocument();
        //$dom->load($content);
        //$xpath = new DOMXPath($dom);
        //$h1s = $dom->getElementsByTagName("ytd-app");
        //var_dump($h1s);die;
        //$h1s = $dom->getElementsByTagName("h1");
        //echo $h1s[0]->textContent . PHP_EOL;
        //die;
    }
}

