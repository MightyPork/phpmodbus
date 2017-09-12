<?php

namespace Tests\IecType;

use PHPModbus\IecType;
use PHPUnit\Framework\TestCase;

class IecByteTest extends TestCase
{
    public function testIecByte()
    {
        $this->assertEquals(125, ord(IecType::iecBYTE(125)));
        $this->assertEquals(98, ord(IecType::iecBYTE(98)));
        $this->assertEquals(0, ord(IecType::iecBYTE(0)));
        $this->assertEquals(255, ord(IecType::iecBYTE(255)));
        $this->assertEquals(88, ord(IecType::iecBYTE(88)));
    }
}