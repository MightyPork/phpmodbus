<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class Fc2ReadInputDiscretesTest extends MockServerTestCase
{
    public function testFc1Read1InputDiscrete()
    {
        $mockResponse = '6ae30000000400020101';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertEquals([1], $modbus->readInputDiscretes(0, 256, 1));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000006000201000001', $packetWithoutTransactionId);
    }

    public function testFc1Read3InputDiscretes()
    {
        $mockResponse = 'b5110000000400020103';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertEquals([1, 1, 0], $modbus->fc2(0, 256, 3));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000006000201000003', $packetWithoutTransactionId);
    }
}