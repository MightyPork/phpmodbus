<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class Fc16WriteMultipleRegistersTest extends MockServerTestCase
{
    public function testFc16WriteMultipleRegisters()
    {
        $mockResponse = 'facf00000006001030000005';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertTrue($modbus->fc16(0, 12288, [-1,100001,1.3], ['INT', 'DINT', 'REAL']));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('000000110010300000050affff86a1000166663fa6', $packetWithoutTransactionId);
    }
}