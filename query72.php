<?php

//use Symfony\Component\Panther\Client;
//use Symfony\Component\DomCrawler\Crawler;
use Query\Factory;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/src/functions.query.php';

//ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
ini_set('user_agent','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

$config = include(__DIR__ . '/config.php');

$query = '';
for ($i = 1; $i < count($argv); $i++) {
    $query .= ' ' . $argv[$i];
}
//echo $query . PHP_EOL;
$query = urlencode($query);

//https://www.googleapis.com/customsearch/v1?[parameters]
//parseJson(getJsonFromApi($query, $config));
parseHtml(getGoogleFromQuery($query));

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

            echo "(Click q to quit)\n";
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
    foreach ($json['items'] as $i => $item) {
        $j = $i + 1;
        echo "Process item\n";
        $href = $item['link'];
        //echo $href . PHP_EOL;
        $t = $fac->make($href);
        $t->show();
        echo PHP_EOL;
        echo "(You're viewing result {$j})\n";
        echo "(Click q to quit, space to continue)\n";
        back:
        readline_callback_handler_install("", function () { echo "here\n"; });
        readline_callback_read_char();
        $c = readline_info('line_buffer');
        if ($c === "q") {
            exit;
        } elseif ($c === " ") {
            continue;
        } else {
            goto back;
        }
    }
}

function getGoogleFromQuery(string $query)
{
    return file_get_contents("https://google.com/search?q=$query");
}

function getJsonFromApi(string $query, $config): array
{
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
    $error = curl_error($ch);
    curl_close($ch);
    if ($content ) {
        return json_decode($content, true);
    } else {
        throw new Exception($error);
    }
}

/**
 * NOTES
 * https://www.phparch.com/books/web-scraping-with-php-2nd-edition/
 */
