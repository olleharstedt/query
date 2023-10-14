<?php

namespace Query;

use DOMNode;
use DOMDocument;
use DOMElement;
use InvalidArgumentException;

class ParseHtml
{
    private Factory $factory;
    private array $options;

    public function __construct(Factory $factory, array $options)
    {
        $this->factory = $factory;
        $this->options = $options;
    }

    public function parse(string $content): void
    {
        if (empty($content)) {
            throw new InvalidArgumentException("content is empty");
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $as = $dom->getElementsByTagName("a");
        $buffer = "";
        $k = 0;
        //foreach (array_slice($as, 0, (int) $this->options['n'] ?? 10) as $i => $a) {
        foreach ($as as $i => $a) {
            if ($k >= (int) ($this->options['n'] ?? 1)) {
                continue;
            }
            $buffer .= $this->processAnchor($a);
            if (!empty($a->textContent)) {
                $k++;
            }
        }
        echo substr($buffer, 0, (int) ($this->options['c'] ?? 2000));
        echo PHP_EOL;
    }

    public function processAnchor(DOMElement $a): string
    {
        //error_log("loop $i");
        if ($a->textContent) {
            $href = $a->getAttribute("href");
            //printf("<a> content = %s, href = %s\n", $a->textContent, $href);
            if (strpos($href, "url") !== 1) {
                return '';
            }
            $t = $this->factory
                ->make()
                ->with($href)
                ->setLogger(new ErrorLogLogger())
                ->run();
            return $t->show();
        } else {
            return '';
        }
    }
}
