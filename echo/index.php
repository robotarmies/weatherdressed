<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2/12/2016
 * Time: 8:37 PM
 */

$x = $_REQUEST;

$json = '{
  "version": "1.0",
  "response": {
    "outputSpeech": {
      "type": "PlainText",
      "text": "Maybe you should wear your birthday suit."
    },
    "card": {
      "type": "Simple",
      "content": "Maybe You should wear your birthday suit.",
      "title": "WeatherDressed"
    },
    "reprompt": null,
    "shouldEndSession": true
  },
  "sessionAttributes": null
}';

/* Output header */
header('Content-type: application/json');
echo $json;