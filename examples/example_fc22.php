<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPModbus\ModbusMasterUdp;

require_once __DIR__ . '/request_input_data.php'; // 'ip', 'unitid','reference','quantity' are read from $_GET

$modbus = new ModbusMasterUdp($ip);

// Data to be writen
$bitValue = true;
$bitNumber = 2;
$andMask = 0xFFFF ^ pow(2, $bitNumber);
$orMask = 0x0000 ^ (pow(2, $bitNumber) * $bitValue);

try {
	// FC22
    $recData = $modbus->maskWriteRegister($unitId, $reference, $andMask, $orMask);
} catch (Exception $e) {
	// Print error information if any
	echo $modbus;
	echo $e;
	exit;
}

echo '<h1>Status</h1><pre>';
print_r($modbus);
echo '</pre>';
echo '<h1>Data</h1><pre>';
print_r($recData);
echo '</pre>';