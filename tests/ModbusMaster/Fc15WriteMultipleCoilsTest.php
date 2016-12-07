<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class Fc15WriteMultipleCoilsTest extends MockServerTestCase
{
    public function testFc15WriteMultipleCoils()
    {
        $mockResponse = '455000000006000f30000003';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertTrue($modbus->fc15(0, 12288, [1, 0, 1]));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000008000f300000030105', $packetWithoutTransactionId);
    }

    public function testFc15WriteMultipleCoilsWithMultiWordPacket()
    {
        $mockResponse = 'a51100000006000f00000020';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertTrue($modbus->fc15(0, 0,
                [
                    1, 0, 1, 1, 0, 1, 1, 1,
                    1, 1, 1, 1, 0, 0, 0, 0,
                    0, 0, 0, 0, 1, 1, 1, 1,
                    1, 1, 1, 1, 1, 1, 1, 1,
                ]));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('0000000b000f0000002004ed0ff0ff', $packetWithoutTransactionId);
    }
}