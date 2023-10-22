<?php

use PHPUnit\Framework\TestCase;
use Query\Factory;

require __DIR__.'/../vendor/autoload.php';

class PhpnetCodeTest extends TestCase
{
    public function testGetoptExample(): void
    {
        $this->markTestSkipped();

        $html = '<span style="color: #000000"><span style="color: #0000BB"><?php<br></br></span><span style="color: #FF8000">// Script example.php<br></br></span><span style="color: #0000BB">$options</span><span style="color: #007700">= </span><span style="color: #0000BB">getopt</span><span style="color: #007700">(</span><span style="color: #DD0000">"f:hp:"</span><span style="color: #007700">);<br></br></span><span style="color: #0000BB">var_dump</span><span style="color: #007700">(</span><span style="color: #0000BB">$options</span><span style="color: #007700">);<br></br></span><span style="color: #0000BB">?></span></span>';
        $result = file_put_contents("/tmp/queryresult.md", $html);
        system("pandoc --from html --to plain /tmp/queryresult.md");
    }
}
