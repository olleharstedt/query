<?php

namespace Query;

use InvalidArgumentException;

/**
 * @psalm-immutable
 */
final class Factory
{
    /**
     * @var array<string, class-string>
     */
    const MAP = [
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

    /**
     * @psalm-mutation-free
     * @todo More like a filter than a pipe process step
     */
    public function abortAtPdf(string $href): string
    {
        if (ends_with($href, '.pdf')) {
            throw new InvalidArgumentException("Can't read PDF");
        }
        // Needed to make pipe work
        return $href;
    }

    /**
     * @psalm-mutation-free
     */
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

    /**
     * @psalm-mutation-free
     */
    public function makeThing(array $args): object
    {
        $key  = $args[0];
        $href = $args[1];
        if (isset(Factory::MAP[$key])) {
            return new (Factory::MAP[$key])($href);
        } else {
            return new Unknown($href);
        }
    }

    /**
     * @psalm-mutation-free
     */
    public function make(): Pipe
    {
        return p(
            $this->abortAtPdf(...),
            $this->getKey(...),
            $this->makeThing(...)
        );
    }
}
