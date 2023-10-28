<?php

use Query\Factory;
use Query\ErrorLogLogger;
use Query\ParseHtml;
use Query\Effects\GetGoogleFromQuery;
use Query\Effects\Cache;
use function Query\pipe;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/src/functions.query.php';

//ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
ini_set('user_agent','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
error_reporting(E_ALL);
ini_set("display_errors", true);

$urls = [
    "https://google.com",
    "https://bing.com"
];

$logger = new ErrorLogLogger();

pipe(
    fetchUrl(...),
    htmlToMarkdown(...),
    firstParagraph(...)
)
  ->setLogger($logger);
