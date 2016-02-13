<?php
require_once '../core.php';
$core = new WeatherDressed();
//$x = $_REQUEST;
$json = $core->alexa();

//TESTING THE REQUEST
$file = fopen("test.txt","a");
$post_json = $_POST;
$post = json_decode($post_json, true);
foreach($post as $key=>$value) {
    $message = $key . ":" . $value . "\n";
    echo fwrite($file,$message);
}
fclose($file);

$x = '{
  "version": "1.0",
  "response": {
      "outputSpeech": {
      "type": "PlainText",
      "text": ""
    },
    "card": {
      "type": "Simple",
      "content": "'.$post_json.'",
      "title": ""
    },
    "reprompt": null,
    "shouldEndSession": true
  },
  "sessionAttributes": null
}';


/* Output header */
header('Content-type: application/json');
echo $x;