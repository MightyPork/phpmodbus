<?php
namespace Tests\IecType;

use PHPModbus\IecType;
use PHPUnit\Framework\TestCase;

class IecDIntTest extends TestCase
{
    private static function unPackDInt2HexString($value, $bigEndian = 0)
    {
        return unpack('H*', IecType::iecDINT($value, $bigEndian))[1];
    }

    public function testIecDintEndianingOff()
    {
        $this->assertEquals('00000000', self::unPackDInt2HexString(0));
        $this->assertEquals('00010000', self::unPackDInt2HexString(1));
        $this->assertEquals('ffffffff', self::unPackDInt2HexString(-1));
        $this->assertEquals('ffff7fff', self::unPackDInt2HexString(pow(2, 31) - 1));
        $this->assertEquals('00008000', self::unPackDInt2HexString(-pow(2, 31)));
    }

    public function testIecDintEndianingOn()
    {
        $this->assertEquals('00000000', self::unPackDInt2HexString(0, 1));
        $this->assertEquals('00000001', self::unPackDInt2HexString(1, 1));
        $this->assertEquals('ffffffff', self::unPackDInt2HexString(-1, 1));
        $this->assertEquals('7fffffff', self::unPackDInt2HexString(pow(2, 31) - 1, 1));
        $this->assertEquals('80000000', self::unPackDInt2HexString(-pow(2, 31), 1));
    }
}
