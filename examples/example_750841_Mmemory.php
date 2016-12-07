<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPModbus\ModbusMaster;
use PHPModbus\PhpType;

$ip = filter_var($_GET['ip'], FILTER_VALIDATE_IP) ? $_GET['ip'] : '192.192.15.51';
$unitId = ((int)$_GET['unitid']) ?: 0;
$reference = ((int)$_GET['reference']) ?: 12288;
$quantity = ((int)$_GET['quantity']) ?: 6;

$modbus = new ModbusMaster($ip, 'UDP');

try {
    // FC 3
    $recData = $modbus->readMultipleRegisters($unitId, $reference, $quantity);
} catch (Exception $e) {
    echo $modbus;
    echo $e;
    exit;
}

?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <title>WAGO 750-841 M-memory dump</title>
</head>
<body>
<h1>Dump of M-memory from WAGO 750-84x series coupler.</h1>
<ul>
    <li>PLC: 750-84x series</li>
    <li>IP: <?php echo $ip ?></li>
    <li>Modbus module ID: <?php echo $unitId ?></li>
    <li>Modbus memory reference: <?php echo $reference ?></li>
    <li>Modbus memory quantity: <?php echo $quantity ?></li>
</ul>

<h2>M-memory dump</h2>

<table border="1px">
    <tr>
        <td>WORD address</td>
        <td>Int16</td>
        <td>UInt16</td>
        <td>high byte</td>
        <td>low byte</td>
        <td>high bits</td>
        <td>low bits</td>
    </tr>
    <?php
    for ($i = 0, $max = count($recData); $i < $max; $i += 2) {
        ?>
        <tr>
            <td><?php echo $reference+($i/2) ?></td>
            <td><?php echo PhpType::bytes2signedInt([$recData[$i], $recData[$i+1]]) ?></td>
            <td><?php echo PhpType::bytes2unsignedInt([$recData[$i], $recData[$i+1]]) ?></td>
            <td><?php echo $recData[$i] ?></td>
            <td><?php echo $recData[$i + 1] ?></td>
            <td><?php echo sprintf("%08d", decbin($recData[$i])) ?></td>
            <td><?php echo sprintf("%08d", decbin($recData[$i + 1])) ?></td>
        </tr>
        <?php
    }
    ?>
</table>

<h2>Modbus class status</h2>

<pre><?= $modbus ?></pre>
<h2>Data</h2>
<pre><?= print_r($recData); ?></pre>
</body>
</html>
