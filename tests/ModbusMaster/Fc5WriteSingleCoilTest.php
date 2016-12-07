<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class Fc5WriteSingleCoilTest extends MockServerTestCase
{
    public function testFc5WriteSingleCoilWith1()
    {
        $mockResponse = '952d0000000600051000ff00';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertTrue($modbus->writeSingleCoil(0, 4096, [1]));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('0000000600051000ff00', $packetWithoutTransactionId);
    }

    public function testFc5WriteSingleCoilWith0()
    {
        $mockResponse = '489c00000006000510000000';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertTrue($modbus->fc5(0, 4096, [0]));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000006000510000000', $packetWithoutTransactionId);
    }
}