<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPModbus\ModbusMasterUdp;

require_once __DIR__ . '/request_input_data.php'; // 'ip', 'unitid','reference','quantity' are read from $_GET
$value = isset($_GET['value']) ? ((bool)$_GET['value']) : false;

$modbus = new ModbusMasterUdp($ip);

try {
    // Write single coil - FC5
    $recData = $modbus->writeSingleCoil($unitId, $reference, [$value]);
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
