<?php
namespace Tests\PhpType;

use PHPModbus\PhpType;
use PHPUnit\Framework\TestCase;

class Bytes2UnSignedIntTest extends TestCase
{
    private $data = [
        0xFF, // -1
        0xFF,
        0xFF,
        0xFF,
        0, // 0
        0,
        0,
        0,
        0, // 1
        0x01,
        0,
        0,
        0, // minus max
        0,
        0x80,
        0x0,
        0xFF, // plus max
        0xFF,
        0x7F,
        0xFF,
    ];

    public function testByte2SignedInt()
    {
        $this->assertEquals(65535, PhpType::bytes2unsignedInt(array_slice($this->data, 0, 2)));
        $this->assertEquals(4294967295, PhpType::bytes2unsignedInt(array_slice($this->data, 0, 4)));

        $this->assertEquals(0, PhpType::bytes2unsignedInt(array_slice($this->data, 4, 4)));
        $this->assertEquals(1, PhpType::bytes2unsignedInt(array_slice($this->data, 8, 4)));
        $this->assertEquals(2147483648, PhpType::bytes2unsignedInt(array_slice($this->data, 12, 4)));
        $this->assertEquals(2147483647, PhpType::bytes2unsignedInt(array_slice($this->data, 16, 4)));
    }
}
