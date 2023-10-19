<?php

//use Symfony\Component\Panther\Client;
//use Symfony\Component\DomCrawler\Crawler;
use Query\Factory;
use Query\ErrorLogLogger;
use Query\ParseHtml;
use Query\GetGoogleFromQuery;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/src/functions.query.php';

//ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
ini_set('user_agent','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
error_reporting(E_ALL);
ini_set("display_errors", true);

$config = include(__DIR__ . '/config.php');

$query = '';
for ($i = 1; $i < count($argv); $i++) {
    if (strpos($argv[$i], "-") !== false) {
        continue;
    }
    $query .= ' ' . $argv[$i];
}
//echo $query . PHP_EOL;
$query = urlencode($query);

$opt = getopt("n::c::");
printf("query = %s\n", $query);

//https://www.googleapis.com/customsearch/v1?[parameters]
//parseJson(getJsonFromApi($query, $config));
//parseHtml(Query\getGoogleFromQuery($query), $opt);
$parser = new ParseHtml(new Factory(), $opt);
$buffer = $parser->parse((new GetGoogleFromQuery())($query));
echo $buffer;

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

/**
 * NOTES
 * https://www.phparch.com/books/web-scraping-with-php-2nd-edition/
 */
