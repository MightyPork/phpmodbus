<?php
namespace Tests\ModbusMaster;

use InvalidArgumentException;
use PHPModbus\IOException;
use PHPModbus\ModbusException;
use PHPModbus\ModbusMaster;
use PHPModbus\ModbusMasterTcp;

class ModbusExceptionTest extends MockServerTestCase
{
    public function testThrowProtocolMismatchException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown socket protocol, should be 'TCP' or 'UDP'");

        $modbus = new ModbusMaster('127.0.0.1', 'Mismatch');
        $modbus->readCoils(0, 256, 1);
    }

    public function testPortClosedException()
    {
        $this->expectException(IOException::class);
        $this->expectExceptionMessage('socket_connect() failed. Reason: ()No connection could be made because the target machine actively refused it.');

        $modbus = new ModbusMasterTcp('127.0.0.1');
        $modbus->setSocketTimeout(0.2, 0.2);
        $modbus->readCoils(0, 256, 1);
    }

    public function testTimeoutException()
    {
        $this->expectException(\RuntimeException::class);

        $mockResponse = '89130000000400010101'; // respond with 1 byte (00000001 bits set) [1]
        static::executeWithMockServer($mockResponse, function ($port) {
            $this->expectExceptionMessage("Watchdog time expired [ 0.5 sec ]!!! Connection to 127.0.0.1:{$port} is not established.");

            $modbus = new ModbusMaster('127.0.0.1', 'UDP');
            $modbus->port = $port;
            $modbus->setTimeout(0.5);
            $modbus->setSocketTimeout(0.2, 0.2);

            $modbus->readCoils(0, 256, 1);
        }, 'UDP', 1);
    }


    public function testThrowIllegalDataValueException()
    {
        $this->expectException(ModbusException::class);
        $this->expectExceptionMessage('Modbus response error code: 3 (ILLEGAL DATA VALUE)');

        $mockResponse = 'da8700000003008303'; // respond with 1 WORD (2 bytes) [0, 3]
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;

            //can not query more than 124 WORDs. Wago response is ILLEGAL DATA VALUE
            $this->assertEquals([0, 3], $modbus->readMultipleRegisters(0, 256, 140));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('0000000600030100008c', $packetWithoutTransactionId);
    }
}