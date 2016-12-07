<?php
namespace Tests\IecType;

use PHPModbus\IecType;
use PHPUnit\Framework\TestCase;

class IecIntTest extends TestCase
{
    private static function unPackInt2HexString($value)
    {
        return unpack('H*', IecType::iecINT($value))[1];
    }

    public function testIecInt()
    {
        $this->assertEquals('0000', self::unPackInt2HexString(0));
        $this->assertEquals('0001', self::unPackInt2HexString(1));
        $this->assertEquals('ffff', self::unPackInt2HexString(-1));
        $this->assertEquals('7fff', self::unPackInt2HexString(pow(2, 15) - 1));
        $this->assertEquals('8000', self::unPackInt2HexString(-pow(2, 15)));
    }
}