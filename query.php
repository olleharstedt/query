<?php

use Symfony\Component\Panther\Client;
use Symfony\Component\DomCrawler\Crawler;

require __DIR__.'/vendor/autoload.php';

//ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
ini_set('user_agent','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

$config = include(__DIR__ . '/config.php');

class Base
{
    protected $href;
    public function __construct($href)
    {
        echo "Making " . static::class . "\n";
        $this->href = $href;
    }

    public function show()
    {
        $link = $this->getLink();
        echo "Fetching $link...\n";
        $content = @file_get_contents($link);
        if ($content) {
            $dom = new DOMDocument();
            @$dom->loadHTML($content);
            $h1s = $dom->getElementsByTagName("h1");
            if ($h1s && $h1s[0]) {
                echo $h1s[0]->textContent . PHP_EOL;
            }
            $ps = $dom->getElementsByTagName("p");
            foreach($ps as $p) {
                echo trim($p->textContent) . PHP_EOL;
            }
        }
        echo "DONE\n";
    }

    protected function getLink()
    {
        $parts = explode("=", $this->href);
        if (count($parts) === 1) {
            return $this->href;
        }
        $parts = explode("&", $parts[1]);
        return urldecode($parts[0]);
    }
}

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
            echo $crawler->filter("div.postcell")->text() . PHP_EOL;
            echo "ANSWERCELLS\n";
            echo $crawler->filter("div.answercell")->text(true) . PHP_EOL;

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

class Quora extends Base
{
}

class YouTube extends Base
{
    public function show()
    {
        echo "Can't show youtube\n";
        return;

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

class Silent extends Base
{
    public function show()
    {
        // Do nothing.
    }
}

class Factory
{
    public function make($href)
    {
        $parts = explode("=", $href);
        if (count($parts) === 1) {
            $parts = explode("/", $href);
            $key = $parts[2] ?? '';
        } else {
            $parts = explode("/", $parts[1]);
            $key = $parts[2] ?? '';
        }
        $map = [
            "www.quora.com" => Query::class,
            "www.youtube.com" => YouTube::class,
            "support.google.com" => Silent::class,
            "stackoverflow.com" => Stackoverflow::class,
        ];
        if (isset($map[$key])) {
            return new $map[$key]($href);
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

//https://www.googleapis.com/customsearch/v1?[parameters]
$useragent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
$ch = curl_init("");
$params = [
    'key' => $config['key'],
    'cx'  => $config['cx'],
    'q'   => $query
];

$url = "https://www.googleapis.com/customsearch/v1?" . http_build_query($params);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent); // set user agent
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$content = curl_exec($ch);
//var_dump(curl_error($ch));
curl_close($ch);
if ($content) {
    $result = json_decode($content, true);
    parseJson($result);
}

//$content = file_get_contents("https://google.com/search?q=$query");
function parseHtml($content)
{
    $dom = new DOMDocument();
    @$dom->loadHTML($content);
    $fac = new Factory();
    foreach ($dom->getElementsByTagName("a") as $a) {
        if ($a->textContent) {
            $href = $a->getAttribute("href");
            if (strpos($href, "url") !== 1) {
                continue;
            }
            //echo $href . PHP_EOL;
            $t = $fac->make($href);
            $t->show();
            //echo $a->textContent . PHP_EOL;
            $parent = $a->parentNode;
            //$sibling = $parent->nextSibling;
            //echo PHP_EOL;
            //echo $parent->textContent . PHP_EOL;
            $sibling = $a->nextSibling;
            if ($sibling) {
                //echo $sibling->textContent . PHP_EOL;
            }
            //echo $href . PHP_EOL;
            echo PHP_EOL;
            //if (strpos($href


            //$answer = fgets(STDIN);

            //$handle = fopen ("php://stdin","r");
            //$line = fgets($handle);
            //fclose($handle);

            // Get the standard input stream.
            //$stdin = fopen("php://stdin", "r");
            // Read a single character from the stream.
            //$keystroke = fread($stdin, 1);
            // Close the stream.
            //fclose($stdin);

            readline_callback_handler_install("", function () { echo "here\n"; });
            readline_callback_read_char();
            $c = readline_info('line_buffer');
            if ($c === "q") {
                exit;
            }
        }
    }
}

// $json coming from google api
function parseJson(array $json)
{
    echo "Parse json\n";
    $fac = new Factory();
    foreach ($json['items'] as $item) {
        echo "Process item\n";
        $href = $item['link'];
        //echo $href . PHP_EOL;
        $t = $fac->make($href);
        $t->show();
        echo PHP_EOL;
        readline_callback_handler_install("", function () { echo "here\n"; });
        readline_callback_read_char();
        $c = readline_info('line_buffer');
        if ($c === "q") {
            exit;
        }
    }
}

/**
 * NOTES
 * https://www.phparch.com/books/web-scraping-with-php-2nd-edition/
 */
