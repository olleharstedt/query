<?php

namespace Query;

class RunPandoc implements Effect
{
    private ?string $from;
    private ?string $to;
    private ?string $file;
    public function __construct(string $from = null, string $to = null, string $file = null)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->file = $file;
    }
    public function from(string $f):static      {$this->from = $f; return $this;}
    public function to(string $t):static        {$this->to = $t;   return $this;}
    public function inputFile(string $f):static {$this->file = $f; return $this;}
    public function __invoke()
    {
        $output = '';
        exec("pandoc --from {$this->from} --to {$this->to} {$this->file}", $output);
        return implode("\n", $output);
    }
}
