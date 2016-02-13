<?php
require_once '../core.php';
$core = new WeatherDressed();
$x = $_REQUEST;
$json = $core->alexa();

/* Output header */
header('Content-type: application/json');
echo $json;