<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2/12/2016
 * Time: 8:37 PM
 */

$x = $_REQUEST;
$resp_array = array(
        "version" => "1.0",
        "response" => array(
            "outputSpeech" => array(
                "type" => "Plaintext",
                "text" => "How about your birthday suit."
            )),
        "card" => array(),
        "reprompt" => null,
        "shouldEndSession" => true,
        "sessionAttributes" => null,
        );
$json = '{
  "version": "1.0",
  "response": {
    "outputSpeech": {
      "type": "PlainText",
      "text": "Turning up the Nest to an average temperature of 74 degrees."
    },
    "card": {
      "type": "Simple",
      "content": "Telling Nest to set to 74 degrees fahrenheit.",
      "title": "Nest Control - Setting Nest Temp"
    },
    "reprompt": null,
    "shouldEndSession": true
  },
  "sessionAttributes": null
}';

/* Output header */
header('Content-type: application/json');
echo json_encode($json);