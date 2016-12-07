<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPModbus\ModbusMasterUdp;

$ip = filter_var($_GET['ip'], FILTER_VALIDATE_IP) ? $_GET['ip'] : '192.192.15.51';
$unitId = ((int)$_GET['unitid']) ?: 0;
$reference = ((int)$_GET['reference']) ?: 12288;
$value = ((int)$_GET['value']) ?: -1000;

$modbus = new ModbusMasterUdp($ip);

try {
    // FC6
    $recData = $modbus->writeSingleRegister($unitId, $reference, [$value]);
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