<?php
namespace Tests\PhpType;

use PHPModbus\PhpType;
use PHPUnit\Framework\TestCase;

class PhpTypeArraySizeExceptionsTest extends TestCase
{
    private $data = [
        "0" => 100, // 32098 (DINT)
        "1" => 2,
        "2" => 0,
        "3" => 0,
        "4" => 100, // 32098 (DINT)
        "5" => 2
    ];

    /**
     * @expectedException \Exception
     */
    public function testExceptionWhenSizeShort()
    {
        PhpType::bytes2unsignedInt(array_slice($this->data, 0, 1));
    }

    /**
     * @expectedException \Exception
     */
    public function testExceptionWhenSizeShort3()
    {
        PhpType::bytes2unsignedInt(array_slice($this->data, 0, 3));
    }

    /**
     * @expectedException \Exception
     */
    public function testExceptionWhenSizeLong()
    {
        PhpType::bytes2unsignedInt(array_slice($this->data, 0, 5));
    }

    public function testNoExceptionWhenSize2()
    {
        $this->assertEquals(25602, PhpType::bytes2unsignedInt(array_slice($this->data, 0, 2)));
    }

    public function testNoExceptionWhenSize4()
    {
        $this->assertEquals(25602, PhpType::bytes2unsignedInt(array_slice($this->data, 0, 4)));
    }

}