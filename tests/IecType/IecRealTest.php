<?php
namespace Tests\IecType;

use PHPModbus\IecType;
use PHPUnit\Framework\TestCase;

class IecRealTest extends TestCase
{
    private static function unPackReal2HexString($value, $bigEndian = 0)
    {
        return unpack('H*', IecType::iecREAL($value, $bigEndian))[1];
    }

    public function testIecRealEndianingOff()
    {
        $this->assertEquals('00000000', self::unPackReal2HexString(0));
        $this->assertEquals('00003f80', self::unPackReal2HexString(1));
        $this->assertEquals('0000c000', self::unPackReal2HexString(-2));
        $this->assertEquals('aaab3eaa', self::unPackReal2HexString(0.333333333333));
        $this->assertEquals('000041c8', self::unPackReal2HexString(25));
    }

    public function testIecRealEndianingOn()
    {
        $this->assertEquals('00000000', self::unPackReal2HexString(0, 1));
        $this->assertEquals('3f800000', self::unPackReal2HexString(1, 1));
        $this->assertEquals('c0000000', self::unPackReal2HexString(-2, 1));
        $this->assertEquals('3eaaaaab', self::unPackReal2HexString(0.333333333333, 1));
        $this->assertEquals('41c80000', self::unPackReal2HexString(25, 1));
    }
}
