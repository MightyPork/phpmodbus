<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class Fc23ReadWriteRegistersTest extends MockServerTestCase
{
    public function testFc23ReadWriteRegisters()
    {
        $mockResponse = '9aa80000000f00170c000afc1807d0000000004040';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            $data = array(10, -1000, 2000, 3.0);
            $dataTypes = array("INT", "INT", "DINT", "REAL");

            $this->assertEquals([0, 10, 252, 24, 7, 208, 0, 0, 0, 0, 64, 64], $modbus->fc23(0, 12288, 6, 12288, $data, $dataTypes));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000017001730000006300000060c000afc1807d0000000004040', $packetWithoutTransactionId);
    }
}