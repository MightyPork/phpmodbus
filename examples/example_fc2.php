<?php

use PHPModbus\ModbusMaster;

require_once __DIR__ . '/request_input_data.php'; // 'ip', 'unitid','reference','quantity' are read from $_GET

$modbus = new ModbusMaster($ip, 'UDP');

try {
    // FC 2
    // read 2 input bits from address 0x0 (Wago input image)
    $recData = $modbus->readInputDiscretes($unitId, $reference, $quantity);
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