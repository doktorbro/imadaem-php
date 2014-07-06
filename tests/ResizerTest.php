<?php

class ResizerTest extends PHPUnit_Framework_TestCase
{
    public function testInfoName()
    {
        $this->assertEquals('info.json', Penibelst\Imadaem\Resizer::INFO);
    }
}
