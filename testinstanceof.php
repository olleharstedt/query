<?php

use Query\Sites\Base;
use Query\Factory;
use Query\ErrorLogLogger;
use Query\ParseHtml;
use Query\Effects\GetGoogleFromQuery;
use Query\Effects\Cache;
use Query\Effects\FileGetContents;
use function Query\pipe;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/src/functions.query.php';
ini_set('user_agent','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
error_reporting(E_ALL);
ini_set("display_errors", true);

$cache = new \Yiisoft\Cache\File\FileCache('/tmp/querycache');

//$callable = new Cache(new FileGetContents('asd'));
//var_dump($c instanceof \Query\Effects\Effect);
//die;
//$replaceEffectWith = ['Query\Effects\Cache' => 'moo'];

/*
var_dump($callable::class);
var_dump(array_key_exists($callable::class, $replaceEffectWith));
var_dump(current(class_implements($callable)));
var_dump(current(class_implements($callable)));
if ($callable instanceof \Query\Effects\Effect
    && (array_key_exists($callable::class, $replaceEffectWith)
    || array_key_exists(current(class_implements($callable)), $replaceEffectWith))
) {
    die('here');
}
die;
*/

$href = '/url?q=https://medium.com';
$b = new Base();
$result = $b
    ->show($href)
    ->setCache($cache)
    ->replaceEffect('Query\Effects\Cache', 'moo')
    ->runAll();
var_dump($result);
