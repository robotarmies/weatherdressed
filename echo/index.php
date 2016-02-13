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


/* Output header */
header('Content-type: application/json');
echo $json;