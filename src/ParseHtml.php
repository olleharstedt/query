<?php

namespace Query;

use DOMNode;
use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use Query\Sites\SiteInterface;
use Query\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class ParseHtml
{
    private Factory $factory;
    private CacheInterface $cache;
    private LoggerInterface $logger;
    private array $options;
    private int $k = 0;

    public function __construct(
        Factory $factory,
        CacheInterface $c,
        LoggerInterface $l,
        array $options
    ) {
        $this->factory = $factory;
        $this->cache   = $c;
        $this->logger   = $l;
        $this->options = $options;
    }

    public function parse(string $content): string
    {
        if (empty($content)) {
            throw new InvalidArgumentException("content is empty");
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $as = $dom->getElementsByTagName("a");
        $buffer = "";
        //foreach (array_slice($as, 0, (int) $this->options['n'] ?? 10) as $i => $a) {
        foreach ($as as $i => $a) {
            if ($this->k >= (int) ($this->options['n'] ?? 1)) {
                continue;
            }
            $buffer .= $this->processAnchor($a);
        }
        return substr($buffer, 0, (int) ($this->options['c'] ?? 3000)) . PHP_EOL;
    }

    public function processAnchor(DOMElement $a): ?string
    {
        //error_log("loop");
        if ($a->textContent) {
            $href = $a->getAttribute("href");
            //printf("<a> content = %s, href = %s\n", $a->textContent, $href);
            if (strpos($href, "url") !== 1) {
                //error_log("return 1 " . $href);
                return '';
            }
            /** @var SiteInterface */
            $t = $this->factory
                ->make()
                ->from($href)
                ->setLogger($this->logger)
                ->setCache($this->cache)
                ->run();
            $this->k++;
            error_log(get_class($t));
            $showPipe = $t->show($href);
            $showPipe->setCache($this->cache);
            $showPipe->setLogger($this->logger);
            error_log(get_class($showPipe));
            return $showPipe->runAll();
        } else {
            //error_log("return 2");
            return '';
        }
    }
}
