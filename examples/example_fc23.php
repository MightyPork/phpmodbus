<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPModbus\ModbusMasterUdp;

require_once __DIR__ . '/request_input_data.php'; // 'ip', 'unitid','reference','quantity' are read from $_GET
$modbus = new ModbusMasterUdp($ip);

// Data to be writen
$data = array(10, -1000, 2000, 3.0);
$dataTypes = array("WORD", "INT", "DINT", "REAL");

try {
    // FC23
    $recData = $modbus->readWriteRegisters($unitId, $reference, $quantity, $reference, $data, $dataTypes);
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