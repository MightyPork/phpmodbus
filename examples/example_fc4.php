<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPModbus\ModbusMasterUdp;

require_once __DIR__ . '/request_input_data.php'; // 'ip', 'unitid','reference','quantity' are read from $_GET

$modbus = new ModbusMasterUdp($ip);

try {
	// Read input discretes - FC 4
	$recData = $modbus->readMultipleInputRegisters($unitId, $reference, $quantity);
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