<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPModbus\ModbusMasterUdp;

$ip = filter_var($_GET['ip'], FILTER_VALIDATE_IP) ? $_GET['ip'] : '192.192.15.51';
$unitId = ((int)$_GET['unitid']) ?: 0;
$reference = ((int)$_GET['reference']) ?: 0;

$modbus = new ModbusMasterUdp($ip);

// Data to be written - supports both 0/1 and booleans (true, false)
$data = array(
    1, 0, 1, 1, 0, 1, 1, 1,
    1, 1, 1, 1, 0, 0, 0, 0,
    0, 0, 0, 0, 1, 1, 1, 1,
    1, 1, 1, 1, 1, 1, 1, 1,
);

try {
    // FC15
    $recData = $modbus->writeMultipleCoils($unitId, $reference, $data);
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