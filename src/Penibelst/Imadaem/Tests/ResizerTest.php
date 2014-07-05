<?php
require_once 'src/Penibelst/Imadaem/Resizer.php';

class ResizerTest extends PHPUnit_Framework_TestCase
{
    public function testInfoName()
    {
        $this->assertEquals('info.json', Penibelst\Imadaem\Resizer::INFO);
    }
}
