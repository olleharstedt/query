<?php

use Symfony\Component\Panther\Client;

require __DIR__.'/vendor/autoload.php';

class Base
{
    protected $href;
    public function __construct($href)
    {
        $this->href = $href;
    }
}

class Quora extends Base
{
}

class YouTube extends Base
{
    public function show()
    {
        $parts = explode("=", $this->href);
        $parts = explode("&", $parts[1]);
        $link = urldecode($parts[0]) . PHP_EOL;
        //$content = file_get_contents($link);

        $client = Client::createFirefoxClient();
        $client->request('GET', $link);
        $html = $client->getInternalResponse()->getContent();
        $crawler = new Symfony\Component\DomCrawler\Crawler($html);
        // you can use following to get the whole HTML
        //$crawler->outerHtml();
        // or specific parts
        //echo $crawler->filter('h1')->outerHtml() . PHP_EOL;
        //echo $crawler->filter('h1')->html() . PHP_EOL;
        echo json_encode($crawler->filter('h1')->text()) . PHP_EOL;
        die;

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

class Unknown extends Base
{
}

class Factory
{
    public function make($href)
    {
        $parts = explode("=", $href);
        $parts = explode("/", $parts[1]);
        $map = [
            "www.quora.com" => Query::class,
            "www.youtube.com" => YouTube::class
        ];
        if (isset($map[$parts[2] ?? ''])) {
            return new $map[$parts[2]]($href);
        } else {
            return new Unknown($href);
        }
    }
}

$query = '';
for ($i = 1; $i < count($argv); $i++) {
    $query .= ' ' . $argv[$i];
}
//echo $query . PHP_EOL;
$query = urlencode($query);
$content = file_get_contents("https://google.com/search?q=$query");
$dom = new DOMDocument();
@$dom->loadHTML($content);
$fac = new Factory();
foreach ($dom->getElementsByTagName("a") as $a) {
    if ($a->textContent) {
        $href = $a->getAttribute("href");
        if (strpos($href, "url") !== 1) {
            continue;
        }
        echo $href . PHP_EOL;
        $t = $fac->make($href);
        $t->show();
        echo $a->textContent . PHP_EOL;
        $parent = $a->parentNode;
        //$sibling = $parent->nextSibling;
        //echo PHP_EOL;
        //echo $parent->textContent . PHP_EOL;
        $sibling = $a->nextSibling;
        if ($sibling) {
            echo $sibling->textContent . PHP_EOL;
        }
        echo $href . PHP_EOL;
        echo PHP_EOL;
        //if (strpos($href
    }
}
