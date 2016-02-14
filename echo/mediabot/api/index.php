<?php
require_once 'mediabot.php';
$core = new Media_Core();

$json = file_get_contents('php://input');

if (isset($json) && $json !== '' && $json !== NULL ){
    $obj = json_decode($json);
    $requestType = $obj->request->type;
    $intent = $obj->request->intent->name;
    $date = $obj->request->intent->slots->date->value;
}
else {
    //fallback for testing
    $intent = "HelpIntent";
//    $intent = "GetForecast";
//    $date = '2016-02-14';
}

    if ($intent == "HelpIntent"){
        $json = $core->getHelpResponse();
    } elseif ($intent == "GetForecast"){
        $json = $core->alexaForecast(false,$date);
    } else {
        $json = $core->alexaForecast(true);
    }

/* Output header */
header('Content-type: application/json');
echo $json;