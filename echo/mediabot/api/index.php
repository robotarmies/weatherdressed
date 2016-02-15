<?php
require_once 'mediabot.php';
$core = new Media_Core();

$json = file_get_contents('php://input');

if (isset($json) && $json !== '' && $json !== NULL ){
    $obj = json_decode($json);
    $requestType = $obj->request->type;
    $intent = $obj->request->intent->name;
    $media = $obj->request->intent->slots->media->value;
    $entry = $obj->request->intent->slots->entry->value;
}
else {
    //fallback for testing
//    $intent = "HelpIntent";
    $intent = "SearchMedia";
    $media = 'star wars';
}

    if ($intent == "HelpIntent"){
        $json = $core->getHelpResponse();
    } elseif ($intent == "SearchMedia"){
        $json = $core->searchMedia($media);
    } else {
        $media =
        $json = $core->ChooseMedia($entry);
    }

/* Output header */
header('Content-type: application/json');
echo $json;