<?php

class Quora 
{
}

$map = [
    "www.quora.com" => Query::class
]

$query = '';
for ($i = 1; $i < count($argv); $i++) {
    $query .= ' ' . $argv[$i];
}
//echo $query . PHP_EOL;
$query = urlencode($query);
$content = file_get_contents("https://google.com/search?q=$query");
$dom = new DOMDocument();
@$dom->loadHTML($content);
foreach ($dom->getElementsByTagName("a") as $a) {
    if ($a->textContent) {
        $href = $a->getAttribute("href");
        if (strpos($href, "url") !== 1) {
            continue;
        }
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
