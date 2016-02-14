<?php
require_once 'mediabot.php';
$core = new Media_Core();

$json = file_get_contents('php://input');

if (isset($json) && $json !== '' && $json !== NULL ){
    $obj = json_decode($json);
    $requestType = $obj->request->type;
    $intent = $obj->request->intent->name;
    $media = $obj->request->intent->slots->media->value;
    var_dump($obj);
}
else {
    //fallback for testing
    $intent = "HelpIntent";
//    $intent = "GetForecast";
//    $date = '2016-02-14';
}

    if ($intent == "HelpIntent"){
        $json = $core->getHelpResponse();
    } elseif ($intent == "SearchMedia"){
        $json = $core->searchMedia($media);
    } else {
        $json = $core->downloadMedia($entry);
    }

/* Output header */
header('Content-type: application/json');
echo $json;