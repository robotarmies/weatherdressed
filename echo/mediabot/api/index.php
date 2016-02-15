<?php
require_once 'mediabot.php';
$core = new Media_Core();

$json = file_get_contents('php://input');

if (isset($json) && $json !== '' && $json !== NULL ){
    $obj = json_decode($json);
    $requestType = $obj->request->type;
    $intent = $obj->request->intent->name;
    $media = $obj->request->intent->slots->media->value;
}
else {
    //fallback for testing
//    $intent = "HelpIntent";
    $intent = "SearchMedia";
    $media = 'ride along';
}

    if ($intent == "HelpIntent"){
        $json = $core->getHelpResponse();
    } elseif ($intent == "SearchMedia"){
        $json = $core->searchMedia($media);
    } else {
        $json = $core->ChooseMedia($media);
    }

/* Output header */
header('Content-type: application/json');
echo $json;