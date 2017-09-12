<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class Fc6WriteSingleRegisterTest extends MockServerTestCase
{
    public function testFc6WriteSingleRegister()
    {
        $mockResponse = 'ecd10000000600061000000f';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertTrue($modbus->writeSingleRegister(0, 4096, [15]));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('0000000600061000000f', $packetWithoutTransactionId);
    }

    public function testFc6WriteSingleRegisterWith0()
    {
        $mockResponse = '489c00000006000510000000';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertTrue($modbus->fc6(0, 4096, [0]));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000006000610000000', $packetWithoutTransactionId);
    }
}