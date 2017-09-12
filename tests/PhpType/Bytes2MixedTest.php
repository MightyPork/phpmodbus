<?php
namespace Tests\PhpType;

use PHPModbus\PhpType;
use PHPUnit\Framework\TestCase;

class PhpTypeBytes2Mixed extends TestCase
{
    private $data = [
        "0" => 125, // 32098 (DINT)
        "1" => 98,
        "2" => 0,
        "3" => 0,
        "4" => 0,  // 0 (DINT)
        "5" => 0,
        "6" => 0,
        "7" => 0,
        "8" => 0,  // 0 (DINT)
        "9" => 0,
        "10" => 0,
        "11" => 0,
        "12" => 255, // -1 (DINT)
        "13" => 255,
        "14" => 255,
        "15" => 255,
        "16" => 158, // -25000 (INT)
        "17" => 88,
        "18" => 97, // 25000 (INT)
        "19" => 168
    ];

    public function testUnsignedInt()
    {
        $this->assertEquals(32098, PhpType::bytes2unsignedInt(array_slice($this->data, 0, 4)));
    }

    public function testSignedInt()
    {
        $this->assertEquals(0, PhpType::bytes2signedInt(array_slice($this->data, 4, 4)));
        $this->assertEquals(0, PhpType::bytes2signedInt(array_slice($this->data, 8, 4)));
        $this->assertEquals(-1, PhpType::bytes2signedInt(array_slice($this->data, 12, 4)));
        $this->assertEquals(-25000, PhpType::bytes2signedInt(array_slice($this->data, 16, 2)));
        $this->assertEquals(25000, PhpType::bytes2signedInt(array_slice($this->data, 18, 2)));
    }
}
