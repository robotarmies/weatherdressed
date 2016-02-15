<?php
require_once "imdb.php";

class Media_Core {

    private function _dbConnect(){
        $config = $_SERVER['DOCUMENT_ROOT'] . "/core/config.xml";
        $content = file_get_contents($config);
//        var_dump($content);
        $x = new SimpleXmlElement($content);
        $host = (string)$x->config->host;
        $user = (string)$x->config->user;
        $pass = (string)$x->config->password;
        $database = (string)$x->config->database;
        $connect = mysqli_connect("$host", "$user", "$pass", "$database") or die(mysqli_connect_error());
        return $connect;
    }

    public function dbConnect () {
        return $this->_dbConnect();
    }

    public function dbQuery($sql) {
        return mysqli_query($this->dbConnect(), $sql) or die(mysql_error());
    }

    public function dbUpdate($sql){
        $conn = $this->dbConnect();
        mysqli_query($conn, $sql) or die(mysql_error());
        return $conn->insert_id;
    }

    public function getSearchData($post) {
        //CHECK FOR SEARCH, ELSE SHOW STANDARD LIST
        if (isset($post['search'])){$isSearch = $post['search'];}
        else {$post['search'] = NULL;}
        //CHECK FOR SORT
        if (isset($post['sort'])){$sort = $post['sort'];}
        //GET TYPE FROM POST
        if (isset($post['type'])){$postOptions['type'] = $post['type'];}
        else {$postOptions['type'] = 0;}

        //GET SEARCH QUERY FROM POST
        if (isset($post['title'])){
            $postOptions['title'] = $post['title'];

            if ($postOptions['title'] == ''){
                $isSearch=0;
            }
        }
        //testing variables
        if ($postOptions['type'] == 0) {$type = 0;}
        elseif ($postOptions['type'] == 1) {$type = 1;}

//        var_dump($post);
        if ($post['search'] && $post['title'] !== ''){
            return $post;
        } else {
            return false;
        }
    }

    public function getResultsFromPost($title, $type = 1,$hd = 1 ) {
        $result = NULL;

//        if ($type == 0 && $hd == 0){$table = 'pbay_201';} //MOVIES
//        elseif ($type == 0 && $hd == 1){$table = 'pbay_207';} //HIGH RES MOVIES
//        elseif ($type == 1 && $hd == 1){$table = 'pbay_208';} //HIGH RES TV SHOWS
//        elseif ($type == 1 && $hd == 0){$table = 'pbay_205';} //TV SHOWS

//        $tables = array('pbay_201', 'pbay_207', 'pbay_208', 'pbay_205');
        $tables = array('pbay_207');

        //TODO: Establish a better way to manage these database connections.
        $conn = $this->_dbConnect();
        foreach ($tables as $table){
            $imdbTable = $table."_imdb";
            $sql = "SELECT * FROM $table LEFT JOIN $imdbTable ON $table.id = $imdbTable.media_id";
            $query = mysqli_query($conn, $sql) or die(mysqli_connect_error());
            while ($x = mysqli_fetch_array($query)){
                $searchTitle = strtolower($x['title']);
                $match = strstr($searchTitle, $title);
                if ($match){
                    $x['tableId'] = $table;
                    $result[] = $x;
                }
            }
        }

        //todo: need to add more fallback routines and multiple searching for results
        //todo: use http://thepiratebay.se/search/SEARCH%20QUERY/0/7/0
        $searchEnabled = false;
        if ($searchEnabled){
            if (!$result){
                $result = $this->searchPirateBay($title);
            }
        }

        return $result;
    }

    public function getAllResults($sort = 2) {
        //TODO: Update for all movie types after refactoring
        $showDuplicates = false;
        $output = array();
        $tables = array(
            'movies' => 'pbay_201',
            'high res movies' => 'pbay_207',
        );

        $types = array(
            '0' => 'pbay_201',
            '2' => 'pbay_207',
        );
        $sortArray = array(
            0 => " ORDER BY date_uploaded DESC", //newest first
            1 => " ORDER BY date_uploaded ASC", //oldest first
            2 => " ORDER BY seeders DESC", //most seeds
            3 => " ORDER BY seeders ASC", //least seeds
            4 => " ORDER BY title ASC", //alphabetical
            5 => " ORDER BY title DESC", //reverse alphabetical
            6 => " ORDER BY updated_on DESC", //last updated
            7 => " ORDER BY rating DESC"
        );

        foreach ($tables as $table => $tableName){
            $imdbTable = $tableName."_imdb";
            $params = $sortArray[$sort];
            $sql = "SELECT * FROM $tableName LEFT JOIN $imdbTable ON $tableName.id = $imdbTable.media_id".$params;
            $query = mysqli_query($this->_dbConnect(), $sql) or die(mysql_error());
            $results = NULL;
            $ids = NULL;
            while ($row = mysqli_fetch_array($query)){
                if ($ids !== NULL){
                    if (in_array($row['imdb_id'], $ids) && $row['imdb_id'] !== null){
//                        $row['flag_duplicate'] = true;
                    }
                }
                $ids[] = $row['imdb_id'];

                if (!isset($row['flag_duplicate']) && $showDuplicates == false) {
                    $row['tableId'] = substr($tableName,-3);
                    $results[] = $row;
                }
            }
            $output[] = $results;

            //TODO: should we query the db here for an imdb entry? If not found (based on ID), run a query and update info.
            //TODO: Probably better to handle this each time we add the movie, but this could be an ok fallback.
            //TODO: $this->checkMovieInfo($x, $imdbTable);
        }

        return $output;
    }

    public function getSavedList($type) {
//    if ($type == 0){$table = 'movies';} //MOVIES
//    elseif ($type == 1){$table = 'tvshows';} //TV
        $table = 'movies';
        $imdbTable = "pbay_201_imdb";
        $results = NULL;
        $query = mysql_query("SELECT * FROM $table LEFT JOIN $imdbTable ON $table.imdb_id = $imdbTable.imdb_id ") or die(mysql_error());
        while ($x = mysql_fetch_array($query)){
            $results[] = $x;
        }
        if (!$results){return false;}
        else {
            $output = '';
            foreach ($results as $result) {
//            $titleArray = explode('2012',$result['title']);

//            $movieArray = $imdb->getMovieInfo($titleArray[0]);
//            $poster = $movieArray['poster_small'];
//            var_dump($movieArray);
//            $output .= "<img src='$poster' align='left' style='height:90px; border:1px;' hspace='5'>";
                $output .= "<strong>". $result['title']."</strong><br/>";
//            $output .= "IMDB Rating: ". $movieArray['rating']."<br/>";
                $output .= "Uploaded on ".$result['uploaded_on']."<br />";
                $output .= "<a href='magnet".$result['link']."'>Start Download</a> | ";
                $output .= "<a href='includes/addToList.php?remove=1&category=".$type."&id=".$result['id']."'>Remove</a><hr>";
            }

            return $output;
        }
    }

    public function getSearchText($type){
        $array = array(
            0 => 'Search for Movies',
            1 => 'Search for TV Shows',
            2 => 'Search for High Res Movies',
            3 => 'Search for High Res TV Shows',
        );
        $text = "Robot Armies Media Bot: ".$array[$type];
        return $text;
    }

    public function matchTitle($searchTitle, $title){
        $title = strtolower($title);
        $searchTitle = strtolower($searchTitle);
        if ((strpos($title, $searchTitle)) !== FALSE){
            return true;
        } else {
            return false;
        }
    }

    public function searchPirateBay($url = null){
        $raw = file_get_contents($url);
        $pbay_array = array();

        //remove line breaks, extra spaces, etc.
        $newlines = array("\t", "\n", "\r", "\x20\x20", "\0", "\x0B");
        $content = str_replace($newlines, "", html_entity_decode($raw));

        //pull the content table
        $start = strpos($content, '<table id="searchResult">');
        $end = strpos($content, '</table>', $start) + 8;
        $table = substr($content, $start, $end - $start);

        //assign variables
        $title = "TITLE";
        $date = 'DATE';
        $link = 'LINK';
        //strip rows from the table
        preg_match_all("|<tr>(.*)</tr>|U", $table, $rows);

        //pull data from rows
        $i = 0;
        foreach ($rows[0] as $row) {
            $data = array();
            if ((strpos($row, '<th')) === false) {
                //extract table data cell with data from row
                preg_match_all("|<td(.*)</td>|U", $row, $cells);
                if (isset($cells[0][1])){

                    $seeders = $cells[0][2];
                    preg_match_all('|<td align="right">(.*)</td>|U', $seeders, $seedArray);
                    $data['seed'] = $seedArray[1][0];
                    $leechers = $cells[0][3];
                    preg_match_all('|<td align="right">(.*)</td>|U', $leechers, $leechArray);
                    $data['leech'] = $leechArray[1][0];


                    $cell = $cells[0][1];
                    preg_match_all('|<div class="detName">(.*)</div>|U', $cell, $title);
                    $title = $title[0][0];
                    $title = strip_tags($title);
                    $data['title'] = str_replace(".", " ", $title);


                    preg_match_all('|<a href="magnet(.*)" title=|U', $cell, $link);
                    $data['link'] = $link[1][0];
                    preg_match_all('|detDesc">Uploaded(.*),|U', $cell, $date);
                    $dateChanges = array(
                        'Today','Y-day'
                    );
                    $data['uploaded'] = str_replace($dateChanges, date('m-d'), $date[1][0]);
                }
            }
            $pbay_array[] = $data;
        }
        return $pbay_array;
    }

    public function generateSaveLink($result, $type){
        $link = 'includes/addToList.php?category='.$type;
        $downloadLink = urlencode($result['link']);
        $link .= "&title=$result[title]&link=$downloadLink&upload_on=$result[date_uploaded]";
        return $link;
    }

    public function getSeedRatio($result) {
        $seed = $result['seeders'];
        $leech = $result['leechers'];
        $ratio = $seed/$leech;
        if ($ratio > 1.5){
            return 'good';
        } else {
            return 'bad';
        }
    }

    public function getSortLabel($sort) {
        $sortArray = array(
            0 => "newest first", //newest first
            1 => "oldest first", //oldest first
            2 => "most seeds", //most seeds
            3 => "least seeds", //least seeds
            4 => "alphabetical", //alphabetical
            5 => "reverse alphabetical", //reverse alphabetical
            6 => "last updated", //last updated
            7 => "highest rated", //last updated
        );
        return $sortArray["$sort"];
    }

    public function getDirectoryList ($directory)
    {
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

    public function getSerData($array, $y = null){
        $workArray = unserialize($array);
        $first = true;
        //setting simple of array of terms NOT to add when building output
        $doNotUse = array(
            'See full cast and crew',
        );
        if ($workArray){
            foreach ($workArray as $x){

                if ($first){
                    $y = $x;
                    $first = false;
                } elseif (in_array($x, $doNotUse)){
                    continue;
                } else {
                    $y .= ', '.$x;
                }
            }
        }

        return trim($y);
    }

    public function checkMovieInfo($entry = null) {
        $imdb = new Imdb();
        $j = 0;
        $id  = $entry['id'];
        $imdbTable = "pbay_".$entry['code']."_imdb";
        $query = $this->dbConnect()->query("SELECT * FROM $imdbTable WHERE media_id = $id");
        if ($query->num_rows == 0) {
            $j++;
            $this->updateMovieInfoFromTitle($id, $entry['title'],$entry['code']);
        }
        else {

        }
        return true;
    }

    public function updateMovieInfoFromTitle($id, $title, $code) {
        $del = array('2015','2014','2013','2012','2011','2010','2009','2008','2007','2006','2005','2004','2003','2002','2001','2000',
            '(', '[', 'DVD', 'BR', 'CAM',
            'TS', '1080', '720', 'BlueRay',
            'S0', 'S1', 'S2', 'S3',
            '1x', '2x', '3x', '4x', '5x', '6x', '7x', '8x', '9x', '10x'
        );
        $titleArray = explode( $del[0], str_replace($del, $del[0], $title) );
        $searchTitle = trim($titleArray[0]);
        $imdb = new Imdb();
        $movieArray = $imdb->getMovieInfo($searchTitle);
        if (isset($movieArray['title_id'])){
            $title_id = $movieArray['title_id'];

            $bad = array(':',' -',"'");
            $y = str_replace($bad, '', $movieArray['title']);
//                        $title = urlencode($y);
            $title = $y;
            //strip everything for image title
            $bad = array(':','-',"'"," ");
            $z = str_replace($bad, '', $movieArray['title']);
            $imageTitle = urlencode($z);

            $year = $movieArray['year'];
            $rating = $movieArray['rating'];
            $mpaa_rating = $movieArray['mpaa_rating'];
            $plot = $movieArray['plot'];
            $imdb_url = $movieArray['imdb_url'];
            $poster = NULL;

            //SAVE THE IMAGES
            //@todo: do we need all of these images
            if ($movieArray['poster'] !== ''){
                if (strstr($movieArray['poster'],'b.scorecardresearch.com')) {
                    $title_id = NULL;
                } else {
                    $content = file_get_contents($movieArray['poster']);
                    $poster = $code . "-" . $id . "-" . $title_id . ".jpg";
                    $filename = "../media/imdb/".$poster;
                    $x = file_put_contents($filename, $content);
                }
            }
//                        var_dump($movieArray);
            //now we need to process the attributes for the db
            foreach ($movieArray as $attrName=>$attrVal){
                //check if movie attribute is an array of string
                if (is_array($attrVal)){
                    //check to see if an empty array
                    if (count($attrVal) == 0) {
                        $$attrName = NULL;
                    } else {
                        $y = NULL;
                        foreach($attrVal as $x){
                            str_replace('"', "", $y);
                            $y[] = $x;
                        };
                        $$attrName = serialize($y);
                        $y = NULL;
                    }

                }
            }

            $imdbTable = "pbay_" . $code . "_imdb";
            $sql = "INSERT INTO $imdbTable (media_id, imdb_id, imdb_title, year, rating, genres, directors, writers, stars, cast, mpaa_rating, plot, poster, imdb_url) VALUES ('$id', '$title_id', '$title', '$year', '$rating', '$genres', '$directors', '$writers', '$stars', '$cast', '$mpaa_rating', '$plot', '$poster', '$imdb_url')";
            $query = $this->dbUpdate($sql);
            return true;
        }
        else {
            return false;
        }
    }

    public function sendMessage($count,$insertCount,$added) {
        //@todo: convert this to function that grabs user name, or admin
        //$to = 'james@robotarmies.com';
        //$subject = 'Feeds Updated';
        //$message = "$count Total\r\n"."Records Added: $insertCount\r\n";
        //if ($added){
        //    foreach($added as $title){
        //        $message .= $title."\r\n";
        //    }
        //}
        //$headers = 'From: media.bot';
        //$mail = mail($to, $subject, $message, $headers);
    }

    public function getMatch($title, $table){
        //check database for title match
        $sql = "SELECT * FROM $table WHERE title = '$title'";
        $query = mysqli_query($this->dbConnect(), $sql) or die(mysql_error());
        if ($query) {
            $savedData = $query->fetch_assoc();
            if ($savedData) {
                return $savedData['id'];
            }
        }
        //else return false
        return false;
    }

    public function getFeedList() {
        $table = 'movie_feed';
        $results = NULL;
        $sql = "SELECT * FROM $table";
        $query = mysqli_query($this->dbConnect(), $sql) or die(mysql_error());
        while ($row = mysqli_fetch_array($query)){
            $results[] = $row;
        }
        return $results;
    }

    public function searchMedia($media) {
        $one = "star wars 1";
        $two = "star wars 2";
        $three = "star wars 3";
        $response = "I found 3 results for $media. Which one would you like to download: $one, $two, or $three";
        $prompt = "Which one would you like to download: $one, $two, or $three";
        return $this->buildResponse($response, $response, $prompt);
    }

    public function chooseMedia($media) {
        $one = "star wars 1";
        $two = "star wars 2";
        $three = "star wars 3";
        $response = "Now downloading Star Wars.";
        $prompt = "Anything else?";
        return $this->buildResponse($response, $response, $prompt);
    }

    //ALEXA COMMANDS
    public function getHelpResponse(){
        $help_text = "The media bot will help you keep your PLEX library updated. Just ask it to search for a movie to see if it is available for download. For more information please visit media dot row bot armies dot com.";
        $help_card = "The media bot will help you keep your PLEX library updated. Just ask it to search for a movie to see if it is available for download.";
        return $this->buildResponse($help_text, $help_card);
    }

    public function buildResponse($response, $card, $reprompt = null){
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
                "content": "'.$card.'",
                "title": "MediaBot"
                },
            "reprompt":  {
                "outputSpeech": {
                    "type": "PlainText",
                    "text": "'.$reprompt.'"
                }
            },
            "shouldEndSession": ';
        if ($reprompt !== null) {
            $json .= "false";
        } else {
            $json .= "true";
        }
        $json .= '
        },
        "sessionAttributes": null
        }';
        return $json;
    }

}
