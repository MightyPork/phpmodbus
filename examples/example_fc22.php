<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPModbus\ModbusMasterUdp;

$ip = filter_var($_GET['ip'], FILTER_VALIDATE_IP) ? $_GET['ip'] : '192.192.15.51';
$unitId = ((int)$_GET['unitid']) ?: 0;
$reference = ((int)$_GET['reference']) ?: 12288;

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