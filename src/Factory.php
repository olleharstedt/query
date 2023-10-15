<?php

namespace Query;

use Exception;

class Factory
{
    /** @var array */
    public static $map = [
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

    private IO $io;

    public function __construct(IO $io)
    {
        $this->io = $io;
    }

    public function abortAtPdf(string $href): string
    {
        if (ends_with($href, '.pdf')) {
            error_log("Skipping PDF");
            //return new Silent($href);
            throw new Exception("Can't read PDF");
        }
        // Needed to make pipe work
        return $href;
    }

    public function getKey(string $href): array
    {
        if (strpos($href, "/url?q") !== false) {
            $parts = explode("=", $href);
            $key = get_domain($parts[1]);
        } else {
            $key = get_domain($href);
        }
        return [$key, $href];
    }

    public function makeThing(array $args): object
    {
        $key  = $args[0];
        $href = $args[1];
        if (isset(self::$map[$key])) {
            return new self::$map[$key]($href, $this->io);
        } else {
            return new Unknown($href, $this->io);
        }
    }

    /**
     * @psalm-mutation-free
     */
    public function make(): Pipe
    {
        // PHP 7.2 friendly pipe
        // TODO: Replace with (...) notation
        return pipe(
            [$this, "abortAtPdf"],
            [$this, "getKey"],
            [$this, "makeThing"]
        );
    }
}

