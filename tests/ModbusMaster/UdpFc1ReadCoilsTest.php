<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;
use PHPModbus\ModbusMasterUdp;

class UdpFc1ReadCoilsTest extends MockServerTestCase
{
    public function testUdpFc1Read1Coil()
    {
        $mockResponse = '89130000000400010101';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMasterUdp('127.0.0.1');
            $modbus->port = $port;

            usleep(150000); // no idea how to fix this. wait for server to "warm" up or modbus UDP socket will timeout. does not occur with TCP
            $this->assertEquals([1], $modbus->readCoils(0, 256, 1));
        }, 'UDP');

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000006000101000001', $packetWithoutTransactionId);
    }
}