<?php
    require_once ('core.php');
    $core = new WeatherDressed();
    error_reporting(3);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/icon" href="img/favicon.ico">

    <title>Weather Dressed | Robot Armies Development Site</title>

    <!-- Bootstrap Core CSS -->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- Fonts -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href='http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto:400|Englebert' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Poppins' rel='stylesheet' type='text/css'>

    <!-- Custom Theme CSS -->
    <link href="css/grayscale.css" rel="stylesheet">

    <!-- Custom Random BG script -->
    <style>
    .intro {background: url(<?php echo $core->getBackground() ?>) no-repeat top center scroll !important;}
    </style>

</head>

<body id="page-top" data-spy="scroll" data-target=".navbar-custom">

    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#page-top">
                    <i class="fa fa-clock-o"></i>
<!--                    <span class="light">--><?php //echo date('l, F jS Y') ?><!-- / </span>-->
<!--                    <div id="clock">4:20:00 PM</div> / Currently-->
                    <div id="weather" align="center">69° F & Sunny</div>
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
<!--                    <li class="hidden">-->
<!--                        <a href="#page-top"></a>-->
<!--                    </li>-->
<!--                    <li class="page-scroll">-->
<!--                        <a href="#work">Weather</a>-->
<!--                    </li>-->
<!--                    <li class="page-scroll">-->
<!--                        <a href="#feeds">Feeds</a>-->
<!--                    </li>-->
<!--                    <li class="page-scroll">-->
<!--                        <a href="#etc">Etc.</a>-->
<!--                    </li>-->
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <section class="intro">
        <div class="intro-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">

                       <h1 class="brand-heading">Weather Dressed</h1>
                        <p class="intro-text">An application that makes sure you're the best dressed human on the planet, no matter what the weather is.</p>
                        <div class="page-scroll">
                            <a href="#work" class="btn btn-circle">
                                <i class="fa fa-angle-double-down animated"></i>
                            </a>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="work" class="container content-section text-center">
        <div class="row">

            <div class="col-md-12 text-left">
                <div class="service-item">
                    <i class="service-icon fa fa-cloud cl-icon"></i>
                    <h3 class="cl-feed">Weather Dressed</h3>
                    <div class="feed outfit_grid">
                        <?php
                        $forecast = $core->getWeatherDressed();
                        foreach ($forecast as $day){
                            echo "<div class='col-md-3 entry'>";
                            echo "<h4>".$day['info']['dow']."</h4><h5>(<i>".ucfirst($day['outfit']['cond'])."</i>)</h5>";
                            echo "High: ".$day['extremes']['tempHigh'].", Low: ".$day['extremes']['tempLow']."<br/>";
                            echo "Condition: ".$day['info']['desc']."<br/>";
                            foreach ($day['outfit']['outfit'] as $article){
                                echo "<div id='outfit_$article' class='outfit'></div>";
                            }
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <!-- Callout -->
    <div id="about" class="callout content-section">
        <div class="vert-text">
            <h2>About Weather Dressed</h2>
            <p>This is just some placeholder text.</p>
          </div>
    </div>
    <!-- /Callout -->

    <!-- Core JavaScript Files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

    <!-- Google Maps API Key - You will need to use your own API key to use the map feature -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDYuzmdEv6OGJxZVtu9PkMsZSAJ6-GI--M&sensor=false"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/grayscale.js"></script>
    <script type="text/javascript" src="js/jquery.simpleWeather.js"></script>

</body>
<footer>
    <div class="footer">copyright © 2016 robot armies</div>
</footer>
</html>
