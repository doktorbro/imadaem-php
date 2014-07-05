<?php

require '../Imadaem.php';

class ImadaemTest extends PHPUnit_Framework_TestCase
{
    public function testInfoName()
    {
        $this->assertEquals('info.json', Imadaem::INFO);
    }
}
