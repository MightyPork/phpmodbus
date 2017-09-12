<?php
namespace Tests\PhpType;

use PHPModbus\PhpType;
use PHPUnit_Framework_TestCase;


class Bytes2Real extends PHPUnit_Framework_TestCase
{
    private $data = [
        0 => 0,
        1 => 0,
        2 => 68,
        3 => 122,
        4 => 0,
        5 => 0,
        6 => 68,
        7 => 250,
        8 => 0,
        9 => 0,
        10 => 63,
        11 => 160,
    ];

    public function testByte2Real()
    {
        $this->assertEquals(1000, PhpType::bytes2float(array_slice($this->data, 0, 4)));
        $this->assertEquals(2000, PhpType::bytes2float(array_slice($this->data, 4, 4)));
        $this->assertEquals(1.25, PhpType::bytes2float(array_slice($this->data, 8, 4)));
    }
}
