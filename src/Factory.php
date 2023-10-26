<?php

namespace Query;

use InvalidArgumentException;
use Query\Pipeline;
use function Query\p;
use Query\Effects\FileGetContents;
use Query\Effects\FilePutContents;
use Query\Effects\RunPandoc;
use Query\Sites\Unknown;

/**
 * @psalm-immutable
 */
final class Factory
{
    /**
     * @var array<string, class-string>
     */
    const MAP = [
        "www.youtube.com"    => Sites\YouTube::class,
        "support.google.com" => Sites\Silent::class,
        "stackoverflow.com"  => Sites\Stackoverflow::class,
        "wikipedia.org"      => Sites\Wikipedia::class,
        "en.wikipedia.org"   => Sites\Wikipedia::class,
        "google.com"         => Sites\Silent::class,
        "www.php.net"        => Sites\Phpnet::class,
        "php.net"            => Sites\Phpnet::class,
        "www.reddit.com"     => Sites\Reddit::class,
        "reddit.com"         => Sites\Reddit::class
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

        if (empty($key)) {
            throw new InvalidArgumentException("No key");
        }

        if (empty($href)) {
            throw new InvalidArgumentException("No href");
        }

        if (isset(Factory::MAP[$key])) {
            return new (Factory::MAP[$key])();
        } else {
            return new Unknown();
        }
    }

    /**
     * @psalm-mutation-free
     */
    public function make(): Pipeline
    {
        return pipe(
            $this->abortAtPdf(...),
            $this->getKey(...),
            $this->makeThing(...)
        );
    }
}
