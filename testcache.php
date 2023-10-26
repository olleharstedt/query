<?php

use Query\Factory;
use Query\ErrorLogLogger;
use Query\ParseHtml;
use Query\Effects\FileGetContents;
use Query\Effects\Cache;
use Query\Pipeline;
use function Query\pipe;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/src/functions.query.php';

//ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
ini_set('user_agent','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
error_reporting(E_ALL);
ini_set("display_errors", true);

$config = include(__DIR__ . '/config.php');

function getLink(): string
{
    return 'https://medium.com';
}

function firstLetters(string $s): string
{
    return substr($s, 0, 100);
}

$cache = new \Yiisoft\Cache\File\FileCache('/tmp/testcache');

echo pipe(
    getLink(...),
    new Cache(new FileGetContents()),
    firstLetters(...)
)
    ->setCache($cache)
    ->run();
