<?php

namespace Query;

class RunPandoc
{
    private string $from;
    private string $to;
    private string $file;
    public function __construct(string $from, string $to, string $file)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->file = $file;
    }
    public function __invoke()
    {
        $output = '';
        exec("pandoc --from {$this->from} --to {$this->to} {$this->file}", $output);
        return implode("\n", $output);
    }
}
