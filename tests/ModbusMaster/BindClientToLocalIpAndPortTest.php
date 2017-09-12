<?php
namespace Tests\ModbusMaster;

use PHPModbus\ModbusMaster;

class BindClientToLocalIpAndPortTest extends MockServerTestCase
{
    public function testBindClientToLocalIpAndPort()
    {
        $mockResponse = '89130000000400010101';
        $clientData = static::executeWithMockServer($mockResponse, function ($port) {
            $modbus = new ModbusMaster('127.0.0.1', 'TCP');
            $modbus->port = $port;
            // use wildcard as multiple IPs on same machine ala VirtualBox network adapter installed may result ip that you cannot bind to
            // good enough for test that no thing throws exception.
            $modbus->client = '0.0.0.0';
            $modbus->client_port = mt_rand(30000, 40000);

            $this->assertEquals([1], $modbus->readCoils(0, 256, 1));
        });

        $packetWithoutTransactionId = substr($clientData[0], 4);
        $this->assertEquals('00000006000101000001', $packetWithoutTransactionId);
    }
}