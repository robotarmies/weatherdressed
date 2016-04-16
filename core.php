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

    //method to cache weather files to reduce api calls
    private function cacheFiles(){

    }

    //method for random background on homepage
    public function getBackground() {
        $bg_array = $this->getDirectoryList('img/bg');
        return "img/bg/".$bg_array[array_rand($bg_array)];
    }

    //method to list files in directory
    private function getDirectoryList($directory) {
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

    //master method
    public function getWeatherDressed(){
        $wdForecast = array();
        $forecast = $this->getForecast();
        foreach ($forecast as $key => $stat){
            $extremes = $this->getHighLows($stat);
            $info = $this->getInfo($stat);
            $outfit = $this->getOutfit($extremes['tempHigh'],$extremes['tempLow'],$extremes['pop'],$extremes['humidity'],$extremes['wind']);
            $wdForecast[$key] = array(
                'info' => $info,
                'extremes' => $extremes,
                'outfit' => $outfit
            );
        }

        return $wdForecast;


    }

    //get current weather conditions
    private function getCurrentWeather($loc = 29464) {
        $json_string = file_get_contents("http://api.wunderground.com/api/3d9047991415094c/conditions/q/SC/$loc.json");
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

    //get hourly 10 day forecast
    private function getForecast($date = NULL) {
    $json_string = file_get_contents("http://api.wunderground.com/api/3d9047991415094c/hourly10day/q/SC/Charleston.json");
    $parsed_json = json_decode($json_string);
    $forecast = $parsed_json->hourly_forecast;
    $outfits = array();
    $results = array();
        if (isset($date) && $date !== NULL){
            $dom = substr($date,-2);
        } else {
            $dom = NULL;
        }

        foreach ($forecast as $day) {
            //set result if date is not set (all) OR if date is set and matches day
            if ($date == NULL || $dom == $day->FCTTIME->mday_padded){
                //restricting results by the hour for better normalization
                if ($day->FCTTIME->hour > 7 && $day->FCTTIME->hour < 23) {
                    $results[$day->FCTTIME->mday_padded][$day->FCTTIME->hour_padded] = $day;
                }
            }
        }

    return $results;
    }

    //method to get highs and lows per day
    private function getHighLows($dailyResults){
        $tempHigh = 0;
        $tempLow = 1000;
        $popHigh = 0;
        $humidityHigh = 0;
        $windHigh = 0;

        foreach ($dailyResults as $hour) {
            $temp = (int)$hour->feelslike->english;
            $pop = (int)$hour->pop;
            $humidity = (int)$hour->humidity;
            $wind = (int)$hour->wspd->english;

            //set daily high and low and biggest difference in temp
            if ($temp > $tempHigh){
                $tempHigh = $temp;
            }
            if ($temp < $tempLow) {
                $tempLow = $temp;
            }

            //check POP
            if ($pop > $popHigh){
                $popHigh = $pop;
            }

            //check humidity
            if ($humidity > $humidityHigh){
                $humidityHigh = $humidity;
            }

            //check wind
            if ($wind > $windHigh){
                $windHigh = $wind;
            }

        }
        $extremes = array(
            'tempHigh' => $tempHigh,
            'tempLow' => $tempLow,
            'pop' => $popHigh,
            'humidity' => $humidityHigh,
            'wind' => $windHigh
        );
        return $extremes;
    }

    //method to get daily aggregrate info
    private function getInfo($hours){
        $dayInfo = array();
        foreach ($hours as $hour){
            $dayInfo['dom'] = $hour->FCTTIME->mday;
            $dayInfo['dow'] = $hour->FCTTIME->weekday_name;
            if (isset($dayInfo['desc'])){
                if(!(strstr($dayInfo['desc'],$hour->wx))) {
                    $dayInfo['desc'] .= ", ".$hour->wx;
                }
            } else {
                $dayInfo['desc'] = $hour->wx;
            }

        }

        return $dayInfo;
    }

    //match requested forecast to temp index
    private function getConditions($high,$low){
        $temp_index = $this->_tempIndex;
        $avg_temp = ($high + $low)/2;
        $temp_desc = null;

        foreach($temp_index as $key=>$val) {
            if ($avg_temp < $val || $low < $val) {
                $temp_desc = $key;
            }
        }
        return $temp_desc;
    }

    //get outfit based on temperature index
    private function getOutfit($high=null,$low=null,$rain=null,$hum=null,$wind=null) {
        $outfit = NULL;
        $temp_desc = $this->getConditions($high,$low);

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

}