<?php

$ipPrefix = '192.192.15.'; // change to '' if you want to allow request to be sent to every possible ip

$ip = '192.192.15.51';
if (isset($_GET['ip']) && filter_var($ipPrefix . $_GET['ip'], FILTER_VALIDATE_IP)) {
    $ip = $ipPrefix . $_GET['ip'];
}

$unitId = isset($_GET['unitid']) ? (int)$_GET['unitid'] : 0;
$reference = isset($_GET['reference']) ? (int)$_GET['reference'] : 12288;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 6;