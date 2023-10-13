<?php

namespace Query;

use Exception;

class Factory
{
    public static $map = [
        "www.quora.com"      => Query::class,
        "www.youtube.com"    => YouTube::class,
        "support.google.com" => Silent::class,
        "stackoverflow.com"  => Stackoverflow::class,
        "wikipedia.org"      => Wikipedia::class,
        "en.wikipedia.org"   => Wikipedia::class,
        "google.com"         => Silent::class,
        "www.php.net"        => Phpnet::class,
        "php.net"            => Phpnet::class,
        "www.reddit.com"     => Reddit::class,
        "reddit.com"         => Reddit::class
    ];

    public function abortAtPdf($href)
    {
        if (ends_with($href, '.pdf')) {
            error_log("Skipping PDF");
            //return new Silent($href);
            throw new Exception("Can't read PDF");
        }
        // Needed to make pipe work
        return $href;
    }

    public function getKey($href)
    {
        if (strpos($href, "/url?q") !== false) {
            $parts = explode("=", $href);
            $key = get_domain($parts[1]);
        } else {
            $key = get_domain($href);
        }
        return $key;
    }

    public function makeThing($key)
    {
        if (isset(self::$map[$key])) {
            return new self::$map[$key]($href);
        } else {
            return new Unknown($href);
        }
    }

    public function make()
    {
        // PHP 7.2 friendly pipe
        // TODO: Replace with (...) notation
        return pipe(
            [$this, "abortAtPdf"],
            [$this, "getKey"],
            [$this, "makeThing"]
        );

        /*
        if (ends_with($href, '.pdf')) {
            error_log("Skipping PDF");
            return new Silent($href);
        }
        if (strpos($href, "/url?q") !== false) {
            $parts = explode("=", $href);
            $key = get_domain($parts[1]);
        } else {
            $key = get_domain($href);
        }
        //echo "key = " . json_encode($key);
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
        /*
        $map = [
            "www.quora.com"      => Query::class,
            "www.youtube.com"    => YouTube::class,
            "support.google.com" => Silent::class,
            "stackoverflow.com"  => Stackoverflow::class,
            "wikipedia.org"      => Wikipedia::class,
            "en.wikipedia.org"   => Wikipedia::class,
            "google.com"         => Silent::class,
            "www.php.net"        => Phpnet::class,
            "php.net"            => Phpnet::class,
            "www.reddit.com"     => Reddit::class,
            "reddit.com"         => Reddit::class
        ];
        if (isset($map[$key])) {
            return new $map[$key]($href);
        } else {
            return new Unknown($href);
        }
         */
    }
}

