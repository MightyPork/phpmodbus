<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class Fc4ReadMultipleInputRegistersTest extends MockServerTestCase
{
    public function testFc4Read1Word()
    {
        $mockResponse = '8180000000050003020003'; // respond with 1 WORD (2 bytes) [0, 3]
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertEquals([0, 3], $modbus->readMultipleInputRegisters(0, 256, 1));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000006000401000001', $packetWithoutTransactionId);
    }

    public function testFc4Read3Words()
    {
        $mockResponse = 'e4710000000900030693e000040000'; // respond with 3 WORD (3*2 bytes) [147, 224, 0, 4, 0, 0]
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertEquals([147, 224, 0, 4, 0, 0], $modbus->fc4(0, 268, 3));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('000000060004010c0003', $packetWithoutTransactionId);
    }
}