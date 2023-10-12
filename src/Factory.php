<?php

namespace Query;

class Factory
{
    public function make($href)
    {
        if (ends_with($href, '.pdf')) {
            echo "Skipping PDF\n";
            return new Silent($href);
        }
        if (strpos($href, "/url?q") !== false) {
            $parts = explode("=", $href);
            $key = get_domain($parts[1]);
        } else {
            $key = get_domain($href);
        }
        echo "key = " . json_encode($key);
        /*
        $parts = explode("=", $href);
        if (count($parts) === 1) {
            $parts = explode("/", $href);
            $key = $parts[2] ?? '';
        } else {
            $parts = explode("/", $parts[1]);
            $key = $parts[2] ?? '';
        }
         */
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

