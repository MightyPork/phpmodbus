<?php
namespace Tests\PhpType;

use PHPModbus\PhpType;
use PHPUnit\Framework\TestCase;

class PhpTypeArrayExceptionWithTextArrayTest extends TestCase
{
    private $data = [
        "0" => 100, // 32098 (DINT)
        "1" => "e",
        "2" => 0,
        "3" => 0
    ];

    /**
     * @expectedException \Exception
     */
    public function testExceptionWhenSize2ContainsString()
    {
        PhpType::bytes2unsignedInt(array_slice($this->data, 0, 2));
    }

    /**
     * @expectedException \Exception
     */
    public function testExceptionWhenSize4ContainsString()
    {
        PhpType::bytes2unsignedInt(array_slice($this->data, 0, 4));
    }
}
