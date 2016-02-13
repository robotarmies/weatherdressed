<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2/12/2016
 * Time: 8:37 PM
 */
require_once '../core.php';
$core = new WeatherDressed();
$x = $_REQUEST;

$json = $core->alexa();

/* Output header */
header('Content-type: application/json');
echo $json;