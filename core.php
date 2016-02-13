<?php

class WeatherDressed {

    private $_tempIndex = array(
            'hot' => 90,
            'warm' => 75,
            'nice' => 65,
            'cool' => 55,
            'cold' => 45,
            'freezing' => 32
    );

    private $outfit_matrix = array(
        'hot' => 'ssleeve,shorts',
        'warm' => 'ssleeve,pants',
        'nice' => 'lsleeve,pants',
        'cool' => 'lsleeve,pants,sweater',
        'cold' => 'lsleeve,pants,hoodie',
        'freezing' => 'lsleeve,pants,sweater,jacket'
        );

    public function cacheFiles(){

    }

    public function getBackground() {
        $bg_array = $this->getDirectoryList('img/bg');
        return "img/bg/".$bg_array[array_rand($bg_array)];
    }

    public function getDirectoryList($directory) {
            // create an array to hold directory list
            $results = array();
            // create a handler for the directory
            $handler = opendir($directory);
            // open directory and walk through the filenames
            while ($file = readdir($handler)) {
                // if file isn't this directory or its parent, add it to the results
                if ($file != "." && $file != "..") {
                        $results[] = $file;
                }
            }
            // tidy up: close the handler
            closedir($handler);
            // done!
            return $results;
        }

    public function getCurrentWeather() {
        $json_string = file_get_contents("http://api.wunderground.com/api/3d9047991415094c/conditions/q/SC/29464.json");
        $parsed_json = json_decode($json_string);
        $location = $parsed_json->current_observation->display_location->full;
        $temp_f = $parsed_json->current_observation->temp_f;
        $outfit = $this->getOutfit($temp_f, $temp_f);
        $resp = array(
            'location'=>$location,
            'temp'=>$temp_f,
            'outfit'=>$outfit
        );
        return $resp;
    }

    public function getForecast() {
    $json_string = file_get_contents("http://api.wunderground.com/api/3d9047991415094c/forecast/q/SC/Charleston.json");
    $parsed_json = json_decode($json_string);
    $forecast = $parsed_json->forecast->simpleforecast->forecastday;
    $outfits = array();
    foreach ($forecast as $day) {
        $outfits[$day->date->weekday] = $this->getOutfit($day->high->fahrenheit, $day->low->fahrenheit);
    }
    return $outfits;
    }

    public function getForecastByDate($date) {
        $response = NULL;
        $json_string = file_get_contents("http://api.wunderground.com/api/3d9047991415094c/forecast/q/SC/Charleston.json");
        $parsed_json = json_decode($json_string);
        $forecast = $parsed_json->forecast->simpleforecast->forecastday;
        $dom = substr($date,-2);
        foreach ($forecast as $day) {
            if ($day->date->day == $dom){
                $response['condition'] = $day-> conditions;
                $high = $day->high->fahrenheit;
                $low = $day->low->fahrenheit;
                $response['outfit'] = $this->getOutfit($high,$low);
            }
        }
        return $response;
    }

    public function getOutfit($high=null,$low=null,$rain=null,$hum=null,$wind=null) {
    //start with the basic thresholds
        $temp_index = $this->_tempIndex;

        $outfit = NULL;
        $avg_temp = ($high + $low)/2;
        $temp_desc = null;

        foreach($temp_index as $key=>$val) {
            if ($avg_temp < $val || $low < $val) {
                $temp_desc = $key;
            }
        }

        // very basic men's outfit based on temp
        $outfit_matrix = $this->outfit_matrix;
        $outfit = explode(',',$outfit_matrix[$temp_desc]);
        $results = array(
            'outfit'=>$outfit,
            'cond'=>$temp_desc,
            'high'=>$high,
            'low'=>$low
        );
        return $results;
    }

    public function alexaForecast($current = false, $date = NULL) {
        if ($current == true){
            $x = $this->getCurrentWeather();
            $condition = $x['outfit']['cond'];
            $text = $this->alexaText($condition);
            $response = "It is currently $x[temp] degrees in $x[location]. $text";
        } else {
            $x = $this->getForecastByDate($date);
            $condition = $x['outfit']['cond'];
            $text = $this->alexaText($condition,true,$date);
            $response = "It is currently $x[temp] degrees in $x[location]. $text";
        }

        $json = $this->buildResponse($response);
        return $json;
    }

    public function alexaText($condition, $forecast = false, $date = NULL) {
        $text = "Please try your request again.";
        if ($forecast == true) {
            switch ($condition) {
                case "hot":
                    $text = "It's pretty hot outside. Dress nice and cool.";
                    break;
                case "warm":
                    $text = "It's warm out. You'll be fine in a short sleeved shirt.";
                    break;
                case "nice":
                    $text = "It is a perfect temperature today.";
                    break;
                case "cool":
                    $text = "It's cool out. You may want to grab a sweater or light coat";
                    break;
                case "cold":
                    $text = "It's cold out. You will want a jacket. Maybe the puffy coat?";
                    break;
                case "freezing":
                    $text = 'Brrrr. It is freezing outside, so make sure to wear several layers and stay toasty.';
                    break;
                case "raining":
                    $text = "It gon rain. Make sure you have your rain coat and some good shoes.";
                    break;
            }
        } else {
            //fallback to current conditions
            switch ($condition) {
                case "hot":
                    $text = "It's pretty hot outside. Dress nice and cool.";
                    break;
                case "warm":
                    $text = "It's warm out. You'll be fine in a short sleeved shirt.";
                    break;
                case "nice":
                    $text = "It is a perfect temperature today.";
                    break;
                case "cool":
                    $text = "It's cool out. You may want to grab a sweater or light coat";
                    break;
                case "cold":
                    $text = "It's cold out. You will want a jacket. Maybe the puffy coat?";
                    break;
                case "freezing":
                    $text = 'Brrrr. It is freezing outside, so make sure to wear several layers and stay toasty.';
                    break;
                case "raining":
                    $text = "It gon rain. Make sure you have your rain coat and some good shoes.";
                    break;
            }
        }
    return $text;
    }

    public function buildResponse($response){
        $json = '
        {
        "version": "1.0",
        "response": {
            "outputSpeech": {
                "type": "PlainText",
                "text": "'.$response.'"
                },
            "card": {
                "type": "Simple",
                "content": "'.$response.'",
                "title": "Dressed for the weather"
                },
            "reprompt": null,
            "shouldEndSession": true
        },
        "sessionAttributes": null
        }';
        return $json;
    }

}