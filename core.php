<?php

class WeatherDressed {

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

    public function getOutfit($high=null,$low=null,$rain=null,$hum=null,$wind=null) {
    //start with the basic thresholds
        $temp_index= array(
            'hot' => 90,
            'warm' => 75,
            'nice' => 65,
            'cool' => 55,
            'cold' => 45,
            'freezing' => 32);

        $outfit = NULL;
        $avg_temp = ($high + $low)/2;
        $temp_desc = null;

        foreach($temp_index as $key=>$val) {
            if ($avg_temp < $val || $low < $val) {
                $temp_desc = $key;
            }
        }

        // very basic men's outfit based on temp
        $outfit_matrix = array(
            'hot' => 'ssleeve,shorts',
            'warm' => 'ssleeve,pants',
            'nice' => 'lsleeve,pants',
            'cool' => 'lsleeve,pants,sweater',
            'cold' => 'lsleeve,pants,hoodie',
            'freezing' => 'lsleeve,pants,sweater,jacket'
        );
        $outfit = explode(',',$outfit_matrix[$temp_desc]);
        $results = array(
            'outfit'=>$outfit,
            'cond'=>$temp_desc,
            'high'=>$high,
            'low'=>$low
        );
        return $results;
    }

    public function alexa() {
        $x = $this->getCurrentWeather();
        $condition = $x['outfit']['cond'];
        $outfit = $x['outfit']['outfit'][2];
        $dress = '';
        //get the suggestion
        if ($outfit == 'hoodie'){
            $dress = 'You should wear a jacket to stay toasty.';
        } elseif ($outfit == 'sweater') {
            $dress = 'You should wear a sweater.';
        } elseif ($outfit == 'jacket') {
            $dress = 'You should wear a warm jacket. Brrrr.';
        }

        $response = "It is currently $x[temp] degrees in $x[location] and $condition out. $dress";
        $json = '{
                  "version": "1.0",
                  "response": {
                    "outputSpeech": {
                      "type": "PlainText",
                      "text": "'.$response.'"
                    },
                    "card": {
                      "type": "Simple",
                      "content": "'.$response.'",
                      "title": "WeatherDressed"
                    },
                    "reprompt": null,
                    "shouldEndSession": true
                  },
                  "sessionAttributes": null
                }';

        return $json;
    }

}