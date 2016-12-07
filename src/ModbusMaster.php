<?php

namespace PHPModbus;

use Exception;
use PHPModbus\Network\ModbusConnection;
use PHPModbus\Packet\MaskWriteRegisterPacket;
use PHPModbus\Packet\ReadCoilsPacket;
use PHPModbus\Packet\ReadInputDiscretesPacket;
use PHPModbus\Packet\ReadMultipleInputRegistersPacket;
use PHPModbus\Packet\ReadMultipleRegistersPacket;
use PHPModbus\Packet\ReadWriteRegistersPacket;
use PHPModbus\Packet\WriteMultipleCoilsPacket;
use PHPModbus\Packet\WriteMultipleRegisterPacket;
use PHPModbus\Packet\WriteSingleCoilPacket;
use PHPModbus\Packet\WriteSingleRegisterPacket;

/**
 * Phpmodbus Copyright (c) 2004, 2013 Jan Krakora
 *
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt.
 *
 * @copyright Copyright (c) 2004, 2013 Jan Krakora
 * @license   PhpModbus license
 * @category  Phpmodbus
 * @tutorial  Phpmodbus.pkg
 * @package   Phpmodbus
 * @version   $id$
 */


/**
 * ModbusMaster
 *
 * This class deals with the MODBUS master
 *
 * Implemented MODBUS master functions:
 *   - FC  1: read coils
 *   - FC  2: read input discretes
 *   - FC  3: read multiple registers
 *   - FC  4: read multiple input registers
 *   - FC  5: write single coil
 *   - FC  6: write single register
 *   - FC 15: write multiple coils
 *   - FC 16: write multiple registers
 *   - FC 22: mask write register
 *   - FC 23: read write registers
 *
 * @author    Jan Krakora
 * @copyright Copyright (c) 2004, 2013 Jan Krakora
 * @package   Phpmodbus
 */
class ModbusMaster
{
    /**
     * @var string Modbus device IP address
     */
    public $host = '192.168.1.1';
    /**
     * @var string gateway port
     */
    public $port = 502;
    /**
     * @var string (optional) client IP address when binding client
     */
    public $client = '';
    /**
     * @var string client port set when binding client to local ip&port
     */
    public $client_port = 502;
    /**
     * @var string ModbusMaster status messages (echo for debugging)
     */
    public $status;
    /**
     * @var float Total response timeout (seconds, decimals allowed)
     */
    public $timeout_sec = 5;
    /**
     * @var float Socket connect timeout (seconds, decimals allowed)
     */
    public $socket_connect_timeout_sec = 1;
    /**
     * @var float Socket read timeout (seconds, decimals allowed)
     */
    public $socket_read_timeout_sec = 0.3; // 300 ms
    /**
     * @var float Socket write timeout (seconds, decimals allowed)
     */
    public $socket_write_timeout_sec = 1;
    /**
     * @var int Endianness codding (0 = little endian = 0, 1 = big endian)
     */
    public $endianness = 0;
    /**
     * @var string Socket protocol (TCP, UDP)
     */
    public $socket_protocol = 'UDP';

    /**
     * ModbusMaster
     *
     * This is the constructor that defines {@link $host} IP address of the object.
     *
     * @param String $host An IP address of a Modbus TCP device. E.g. "192.168.1.1"
     * @param String $protocol Socket protocol (TCP, UDP)
     */
    public function __construct($host, $protocol)
    {
        $this->socket_protocol = $protocol;
        $this->host = $host;
    }

    /**
     * __toString
     *
     * Magic method
     */
    public function __toString()
    {
        return '<pre>' . $this->status . '</pre>';
    }

    /**
     * fc1
     *
     * Alias to {@link readCoils} method
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  int $quantity
     * @return bool[]
     * @throws \Exception
     */
    public function fc1($unitId, $reference, $quantity)
    {
        return $this->readCoils($unitId, $reference, $quantity);
    }

    /**
     * readCoils
     *
     * Modbus function FC 1(0x01) - Read Coils
     *
     * Reads {@link $quantity} of Coils (boolean) from reference
     * {@link $reference} of a memory of a Modbus device given by
     * {@link $unitId}.
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  int $quantity
     * @return bool[]
     * @throws \Exception
     */
    public function readCoils($unitId, $reference, $quantity)
    {
        $this->status .= "readCoils: START\n";

        $receivedData = $this->sendAndReceive(
            function () use ($unitId, $reference, $quantity) {
                return ReadCoilsPacket::build($unitId, $reference, $quantity);
            },
            function ($data) use ($quantity) {
                return ReadCoilsPacket::parse($data, $quantity);
            }
        );

        $this->status .= "readCoils: DONE\n";
        return $receivedData;
    }

    /**
     * printPacket
     *
     * Print a packet in the hex form
     *
     * @param  string $packet
     * @return string
     */
    private function printPacket($packet)
    {
        return 'packet: ' . unpack('H*', $packet)[1] . "\n";
    }

    /**
     * validateResponseCode
     *
     * Checks the Modbus response and throws exception if response contains failure code
     *
     * @param  string $packet
     * @return bool
     * @throws Exception
     */
    private function validateResponseCode($packet)
    {
        if ((ord($packet[7]) & 0x80) > 0) {
            // failure code
            $failure_code = ord($packet[8]);
            // failure code strings
            $failures = array(
                0x01 => 'ILLEGAL FUNCTION',
                0x02 => 'ILLEGAL DATA ADDRESS',
                0x03 => 'ILLEGAL DATA VALUE',
                0x04 => 'SLAVE DEVICE FAILURE',
                0x05 => 'ACKNOWLEDGE',
                0x06 => 'SLAVE DEVICE BUSY',
                0x08 => 'MEMORY PARITY ERROR',
                0x0A => 'GATEWAY PATH UNAVAILABLE',
                0x0B => 'GATEWAY TARGET DEVICE FAILED TO RESPOND',
            );

            $failure_str = 'UNDEFINED FAILURE CODE';
            if (array_key_exists($failure_code, $failures)) {
                $failure_str = $failures[$failure_code];
            }

            throw new ModbusException("Modbus response error code: $failure_code ($failure_str)");
        } else {
            $this->status .= "Modbus response error code: NOERROR\n";
            return true;
        }
    }


    /**
     * fc2
     *
     * Alias to {@link readInputDiscretes} method
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  int $quantity
     * @return bool[]
     * @throws \Exception
     */
    public function fc2($unitId, $reference, $quantity)
    {
        return $this->readInputDiscretes($unitId, $reference, $quantity);
    }

    /**
     * readInputDiscretes
     *
     * Modbus function FC 2(0x02) - Read Input Discretes
     *
     * Reads {@link $quantity} of Inputs (boolean) from reference
     * {@link $reference} of a memory of a Modbus device given by
     * {@link $unitId}.
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  int $quantity
     * @return bool[]
     * @throws \Exception
     */
    public function readInputDiscretes($unitId, $reference, $quantity)
    {
        $this->status .= "readInputDiscretes: START\n";

        $receivedData = $this->sendAndReceive(
            function () use ($unitId, $reference, $quantity) {
                return ReadInputDiscretesPacket::build($unitId, $reference, $quantity);
            },
            function ($data) use ($quantity) {
                return ReadInputDiscretesPacket::parse($data, $quantity);
            }
        );

        $this->status .= "readInputDiscretes: DONE\n";
        return $receivedData;
    }

    /**
     * fc3
     *
     * Alias to {@link readMultipleRegisters} method.
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  int $quantity
     * @return false|array
     * @throws \Exception
     */
    public function fc3($unitId, $reference, $quantity)
    {
        return $this->readMultipleRegisters($unitId, $reference, $quantity);
    }

    /**
     * readMultipleRegisters
     *
     * Modbus function FC 3(0x03) - Read Multiple Registers.
     *
     * This function reads {@link $quantity} of Words (2 bytes) from reference
     * {@link $referenceRead} of a memory of a Modbus device given by
     * {@link $unitId}.
     *
     * @param  int $unitId usually ID of Modbus device
     * @param  int $reference Reference in the device memory to read data (e.g. in device WAGO 750-841, memory MW0
     *                       starts at address 12288).
     * @param  int $quantity Amounth of the data to be read from device.
     * @return false|array Success flag or array of received data.
     * @throws \Exception
     */
    public function readMultipleRegisters($unitId, $reference, $quantity)
    {
        $this->status .= "readMultipleRegisters: START\n";

        $receivedData = $this->sendAndReceive(
            function () use ($unitId, $reference, $quantity) {
                return ReadMultipleRegistersPacket::build($unitId, $reference, $quantity);
            },
            function ($data) {
                return ReadMultipleRegistersPacket::parse($data);
            }
        );

        $this->status .= "readMultipleRegisters: DONE\n";
        return $receivedData;
    }

    /**
     * fc4
     *
     * Alias to {@link readMultipleInputRegisters} method.
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  int $quantity
     * @return false|array
     * @throws \Exception
     */
    public function fc4($unitId, $reference, $quantity)
    {
        return $this->readMultipleInputRegisters($unitId, $reference, $quantity);
    }

    /**
     * readMultipleInputRegisters
     *
     * Modbus function FC 4(0x04) - Read Multiple Input Registers.
     *
     * This function reads {@link $quantity} of Words (2 bytes) from reference
     * {@link $referenceRead} of a memory of a Modbus device given by
     * {@link $unitId}.
     *
     * @param  int $unitId usually ID of Modbus device
     * @param  int $reference Reference in the device memory to read data.
     * @param  int $quantity Amounth of the data to be read from device.
     * @return false|array Success flag or array of received data.
     * @throws \Exception
     */
    public function readMultipleInputRegisters($unitId, $reference, $quantity)
    {
        $this->status .= "readMultipleInputRegisters: START\n";

        $receivedData = $this->sendAndReceive(
            function () use ($unitId, $reference, $quantity) {
                return ReadMultipleInputRegistersPacket::build($unitId, $reference, $quantity);
            },
            function ($data) {
                return ReadMultipleInputRegistersPacket::parse($data);
            }
        );

        $this->status .= "readMultipleInputRegisters: DONE\n";
        return $receivedData;
    }


    /**
     * fc5
     *
     * Alias to {@link writeSingleCoil} method
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  array $data
     * @return bool
     * @throws \Exception
     */
    public function fc5($unitId, $reference, array $data)
    {
        return $this->writeSingleCoil($unitId, $reference, $data);
    }

    /**
     * writeSingleCoil
     *
     * Modbus function FC5(0x05) - Write Single Register.
     *
     * This function writes {@link $data} single coil at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     * @param  int $unitId usually ID of Modbus device
     * @param  int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at address 12288)
     *                         address 12288)
     * @param  array $data value to be written (TRUE|FALSE).
     * @return bool Success flag
     * @throws \Exception
     */
    public function writeSingleCoil($unitId, $reference, array $data)
    {
        $this->status .= "writeSingleCoil: START\n";

        $receivedData = $this->sendAndReceive(
            function () use ($unitId, $reference, $data) {
                return WriteSingleCoilPacket::build($unitId, $reference, $data);
            },
            function () {
                return WriteSingleCoilPacket::parse();
            }
        );

        $this->status .= "writeSingleCoil: DONE\n";
        return $receivedData;
    }

    /**
     * fc6
     *
     * Alias to {@link writeSingleRegister} method
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  array $data
     * @return bool
     * @throws \Exception
     */
    public function fc6($unitId, $reference, array $data)
    {
        return $this->writeSingleRegister($unitId, $reference, $data);
    }

    /**
     * writeSingleRegister
     *
     * Modbus function FC6(0x06) - Write Single Register.
     *
     * This function writes {@link $data} single word value at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     * @param  int $unitId usually ID of Modbus device
     * @param  int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at address 12288)
     *                         address 12288)
     * @param  array $data Array of values to be written.
     * @return bool Success flag
     * @throws \Exception
     */
    public function writeSingleRegister($unitId, $reference, array $data)
    {
        $this->status .= "writeSingleRegister: START\n";
        $result = $this->sendAndReceive(
            function () use ($unitId, $reference, $data) {
                return WriteSingleRegisterPacket::build($unitId, $reference, $data);
            },
            function () {
                return WriteSingleRegisterPacket::parse();
            }
        );
        $this->status .= "writeSingleRegister: DONE\n";
        return $result;
    }

    public function sendAndReceive(callable $buildRequest, callable $parseResponse)
    {
        $connection = ModbusConnection::getBuilder()
            ->setHost($this->host)
            ->setPort($this->port)
            ->setProtocol($this->socket_protocol)
            ->setClient($this->client)
            ->setClientPort($this->client_port)
            ->setTimeoutSec($this->timeout_sec)
            ->setReadTimeoutSec($this->socket_read_timeout_sec)
            ->setWriteTimeoutSec($this->socket_write_timeout_sec)
            ->setConnectTimeoutSec($this->socket_connect_timeout_sec)
            ->build();

        try {
            $connection->connect();

            $packet = $buildRequest();
            $this->status .= 'Sending ' . $this->printPacket($packet);
            $connection->send($packet);

            $data = $connection->receive();

            $this->status .= 'Received ' . $this->printPacket($data);

            $this->validateResponseCode($data);

            $this->closeConnection($connection);
            return $parseResponse($data);
        } catch (Exception $e) {
            $this->closeConnection($connection);
            throw $e;
        }
    }

    private function closeConnection(ModbusConnection $connection)
    {
        $this->status .= implode("\n", $connection->getStatusMessages());
        $connection->close();
    }

    /**
     * fc15
     *
     * Alias to {@link writeMultipleCoils} method
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  array $data
     * @return bool
     * @throws \Exception
     */
    public function fc15($unitId, $reference, array $data)
    {
        return $this->writeMultipleCoils($unitId, $reference, $data);
    }

    /**
     * writeMultipleCoils
     *
     * Modbus function FC15(0x0F) - Write Multiple Coils
     *
     * This function writes {@link $data} array at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  array $data
     * @return bool
     * @throws \Exception
     */
    public function writeMultipleCoils($unitId, $reference, array $data)
    {
        $this->status .= "writeMultipleCoils: START\n";

        $receivedData = $this->sendAndReceive(
            function () use ($unitId, $reference, $data) {
                return WriteMultipleCoilsPacket::build($unitId, $reference, $data);
            },
            function () {
                return WriteMultipleCoilsPacket::parse();
            }
        );

        $this->status .= "writeMultipleCoils: DONE\n";
        return $receivedData;
    }

    /**
     * fc16
     *
     * Alias to {@link writeMultipleRegister} method
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  array $data
     * @param  array $dataTypes
     * @return bool
     * @throws \Exception
     */
    public function fc16($unitId, $reference, array $data, array $dataTypes)
    {
        return $this->writeMultipleRegister($unitId, $reference, $data, $dataTypes);
    }

    /**
     * writeMultipleRegister
     *
     * Modbus function FC16(0x10) - Write Multiple Register.
     *
     * This function writes {@link $data} array at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     * @param  int $unitId usually ID of Modbus device
     * @param  int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at address 12288)
     *                         address 12288)
     * @param  array $data Array of values to be written.
     * @param  array $dataTypes Array of types of values to be written. The array should consists of string "INT",
     *                         "DINT" and "REAL".
     * @return bool Success flag
     * @throws \Exception
     */
    public function writeMultipleRegister($unitId, $reference, array $data, array $dataTypes)
    {
        $this->status .= "writeMultipleRegister: START\n";

        $receivedData = $this->sendAndReceive(
            function () use ($unitId, $reference, $data, $dataTypes) {
                return WriteMultipleRegisterPacket::build($unitId, $reference, $data, $dataTypes, $this->endianness);
            },
            function () {
                return WriteMultipleRegisterPacket::parse();
            }
        );

        $this->status .= "writeMultipleRegister: DONE\n";
        return $receivedData;
    }

    /**
     * fc22
     *
     * Alias to {@link maskWriteRegister} method
     *
     * @param  int $unitId
     * @param  int $reference
     * @param  int $andMask
     * @param  int $orMask
     * @return bool
     * @throws \Exception
     */
    public function fc22($unitId, $reference, $andMask, $orMask)
    {
        return $this->maskWriteRegister($unitId, $reference, $andMask, $orMask);
    }

    /**
     * maskWriteRegister
     *
     * Modbus function FC22(0x16) - Mask Write Register.
     *
     * This function alter single bit(s) at {@link $reference} position of
     * memory of a Modbus device given by {@link $unitId}.
     *
     * Result = (Current Contents AND And_Mask) OR (Or_Mask AND (NOT And_Mask))
     *
     * @param  int $unitId usually ID of Modbus device
     * @param  int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at address
     *                       12288)
     * @param  int $andMask
     * @param  int $orMask
     * @return bool Success flag
     * @throws \Exception
     */
    public function maskWriteRegister($unitId, $reference, $andMask, $orMask)
    {
        $this->status .= "maskWriteRegister: START\n";

        $receivedData = $this->sendAndReceive(
            function () use ($unitId, $reference, $andMask, $orMask) {
                return MaskWriteRegisterPacket::build($unitId, $reference, $andMask, $orMask);
            },
            function ($data) {
                return MaskWriteRegisterPacket::parse($data);
            }
        );

        $this->status .= "maskWriteRegister: DONE\n";
        return $receivedData;
    }


    /**
     * fc23
     *
     * Alias to {@link readWriteRegisters} method.
     *
     * @param  int $unitId
     * @param  int $referenceRead
     * @param  int $quantity
     * @param  int $referenceWrite
     * @param  array $data
     * @param  array $dataTypes
     * @return false|array
     * @throws \Exception
     */
    public function fc23($unitId, $referenceRead, $quantity, $referenceWrite, array $data, array $dataTypes)
    {
        return $this->readWriteRegisters($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes);
    }

    /**
     * readWriteRegisters
     *
     * Modbus function FC23(0x17) - Read Write Registers.
     *
     * This function writes {@link $data} array at reference {@link $referenceWrite}
     * position of memory of a Modbus device given by {@link $unitId}. Simultanously,
     * it returns {@link $quantity} of Words (2 bytes) from reference {@link $referenceRead}.
     *
     * @param  int $unitId usually ID of Modbus device
     * @param  int $referenceRead Reference in the device memory to read data (e.g. in device WAGO 750-841, memory MW0 starts at address 12288). MW0 starts at address 12288).
     *                              MW0 starts at address 12288).
     * @param  int $quantity Amounth of the data to be read from device.
     * @param  int $referenceWrite Reference in the device memory to write data.
     * @param  array $data Array of values to be written.
     * @param  array $dataTypes Array of types of values to be written. The array should consists of string "INT", "DINT" and "REAL".
     *                              "DINT" and "REAL".
     * @return false|array Success flag or array of data.
     * @throws \Exception
     */
    public function readWriteRegisters($unitId, $referenceRead, $quantity, $referenceWrite, array $data, array $dataTypes)
    {
        $this->status .= "readWriteRegisters: START\n";

        $receivedData = $this->sendAndReceive(
            function () use ($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes) {
                return ReadWriteRegistersPacket::build(
                    $unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes, $this->endianness
                );
            },
            function ($data) {
                return ReadWriteRegistersPacket::parse($data);
            }
        );

        $this->status .= "writeMultipleRegister: DONE\n";
        return $receivedData;
    }


    /**
     * Set data receive timeout.
     * Writes property timeout_sec
     *
     * @param float $seconds seconds
     */
    public function setTimeout($seconds)
    {
        $this->timeout_sec = $seconds;
    }

    /**
     * Set socket read/write timeout. Null = no change.
     *
     * @param    float|null $read_timeout_sec data read timeout (seconds, default 0.3)
     * @param    float|null $write_timeout_sec data write timeout (seconds, default 1.0)
     * @internal param float $seconds seconds
     */
    public function setSocketTimeout($read_timeout_sec, $write_timeout_sec)
    {
        // Set read timeout if given
        if ($read_timeout_sec !== null) {
            $this->socket_read_timeout_sec = $read_timeout_sec;
        }

        // Set write timeout if given
        if ($write_timeout_sec !== null) {
            $this->socket_write_timeout_sec = $write_timeout_sec;
        }
    }
}
