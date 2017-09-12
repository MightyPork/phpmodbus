<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class Fc1ReadCoilsTest extends MockServerTestCase
{
    public function testFc1Read1Coil()
    {
        $mockResponse = '89130000000400010101'; // respond with 1 byte (00000001 bits set) [1]
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertEquals([1], $modbus->readCoils(0, 256, 1));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000006000101000001', $packetWithoutTransactionId);
    }

    public function testFc1Read3Coils()
    {
        $mockResponse = '31be0000000400010103'; // respond with 1 byte (00000011 bits set) [1, 1, 0]
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $this->assertEquals([1, 1, 0], $modbus->fc1(0, 256, 3));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000006000101000003', $packetWithoutTransactionId);
    }
}