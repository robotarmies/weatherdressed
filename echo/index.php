<?php
require_once '../core.php';
$core = new WeatherDressed();
$json = file_get_contents('php://input');
$obj = json_decode($json);
$requestType = $obj->request->type;
$intent = $obj->request->intent->name;
if ($intent = "HelpIntent"){
    $json = "Need some Help info here.";
}
elseif ($intent == "getForecast"){
    $date = $obj->request->intent->slots->date->value;
    $json = $core->alexa();
} else {
    $json = $core->alexa();
}

/* Output header */
header('Content-type: application/json');
echo $json;