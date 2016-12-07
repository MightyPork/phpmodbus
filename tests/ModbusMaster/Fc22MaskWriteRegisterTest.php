<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class Fc22MaskWriteRegisterTest extends MockServerTestCase
{
    public function testFc22MaskWriteRegister()
    {
        $mockResponse = 'd4350000000800163000fffb0004';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $bitValue = true;
            $bitNumber = 2;
            $andMask =  0xFFFF ^ pow(2, $bitNumber) ;
            $orMask =  0x0000 ^ (pow(2, $bitNumber) * $bitValue ) ;

            $this->assertTrue($modbus->fc22(0, 12288, $andMask, $orMask));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('0000000800163000fffb0004', $packetWithoutTransactionId);
    }
}