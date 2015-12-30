//jQuery to collapse the navbar on scroll
$(window).scroll(function() {
    if ($(".navbar").offset().top > 50) {
        $(".navbar-fixed-top").addClass("top-nav-collapse");
    } else {
        $(".navbar-fixed-top").removeClass("top-nav-collapse");
    }
});

//jQuery for page scrolling feature - requires jQuery Easing plugin
$(function() {
    $('.page-scroll a').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });
});

//Google Map Skin - Get more at http://snazzymaps.com/
var myOptions = {
    zoom: 11,
    center: new google.maps.LatLng(32.804887,-79.9570884),
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    disableDefaultUI: true,
    styles: [{
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 17
        }]
    }, {
        "featureType": "landscape",
        "elementType": "geometry",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 20
        }]
    }, {
        "featureType": "road.highway",
        "elementType": "geometry.fill",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 17
        }]
    }, {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 29
        }, {
            "weight": 0.2
        }]
    }, {
        "featureType": "road.arterial",
        "elementType": "geometry",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 18
        }]
    }, {
        "featureType": "road.local",
        "elementType": "geometry",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 16
        }]
    }, {
        "featureType": "poi",
        "elementType": "geometry",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 21
        }]
    }, {
        "elementType": "labels.text.stroke",
        "stylers": [{
            "visibility": "on"
        }, {
            "color": "#000000"
        }, {
            "lightness": 16
        }]
    }, {
        "elementType": "labels.text.fill",
        "stylers": [{
            "saturation": 36
        }, {
            "color": "#000000"
        }, {
            "lightness": 40
        }]
    }, {
        "elementType": "labels.icon",
        "stylers": [{
            "visibility": "off"
        }]
    }, {
        "featureType": "transit",
        "elementType": "geometry",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 19
        }]
    }, {
        "featureType": "administrative",
        "elementType": "geometry.fill",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 20
        }]
    }, {
        "featureType": "administrative",
        "elementType": "geometry.stroke",
        "stylers": [{
            "color": "#000000"
        }, {
            "lightness": 17
        }, {
            "weight": 1.2
        }]
    }]
};

//var map = new google.maps.Map(document.getElementById('map'), myOptions);

/* Settings *\
 \*==========*/
var settings = {
    "clock": {
        "showClock": true
    }
};

/*  Clock  *\
 \*=========*/
function updateClock() {
    var currentTime = new Date ();
    var currentHours = currentTime.getHours ();
    var currentMinutes = currentTime.getMinutes ();
    var currentSeconds = currentTime.getSeconds ();

    // Pad the minutes and seconds with leading zeros, if required
    currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
    currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;

    // Choose either "AM" or "PM" as appropriate
    var timeOfDay = (currentHours < 12) ? "AM" : "PM";

    // Convert the hours component to 12-hour format if needed
    currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;

    // Convert an hours component of "0" to "12"
    currentHours = (currentHours == 0) ? 12 : currentHours;

    // Compose the string for display
    var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

    // Fill '#clock' div with time
    $("#clock").html(currentTimeString);
}

$(document).ready(function() {
    /*  Clock  *\
     \*=========*/

    if (settings.clock.showClock) {
        // Add empty '#clock' div
        //$('body').append('<div id="clock"></div>');

        // Update clock
        setInterval('updateClock()', 1000);
    }

    <!-- SIMPLE WEATHER -->
    $.simpleWeather({
        zipcode: '29464',
        unit: 'f',
        success: function(weather) {
            html = '<span class="temp-current">'+weather.temp+'&deg; '+weather.units.temp+'</span> & '+weather.currently;
//    html = '<h2>'+weather.city+', '+weather.region+' '+weather.country+'</h2>';
//            html = '<img class="weather" src="'+weather.image+'">';
//    html += '<p class="temp"><strong>Today\'s High</strong>: '+weather.high+'&deg; '+weather.units.temp+'<br />';
//    html += '<strong>Today\'s Low</strong>: '+weather.low+'&deg; '+weather.units.temp+'</p>';
//            html += '<span class="temp-current"><strong>'+weather.temp+'&deg; '+weather.units.temp+'</strong></span>';
            //html += '<span class="temp"> ('+weather.high+'&deg; '+')';
            //html += 'Low: '+weather.low+'&deg; '+weather.units.temp+'</span>';
            //                html += '<p><strong>Thumbnail</strong>: <img src="'+weather.thumbnail+'"></p>';
//    html += '<p><strong>Wind</strong>: '+weather.wind.direction+' '+weather.wind.speed+' '+weather.units.speed+' <strong>Wind Chill</strong>: '+weather.wind.chill+'</p>';
//    html += '<p><strong>Currently</strong>: '+weather.currently+' - <strong>Forecast</strong>: '+weather.forecast+'</p>';
            //                html += '<p><strong>Humidity</strong>: '+weather.humidity+' <strong>Pressure</strong>: '+weather.pressure+' <strong>Rising</strong>: '+weather.rising+' <strong>Visibility</strong>: '+weather.visibility+'</p>';
            //                html += '<p><strong>Heat Index</strong>: '+weather.heatindex+'</p>';
//                    html += '<span class="sunrise"><strong>Sunrise</strong>: '+weather.sunrise+' - <strong>Sunset</strong>: '+weather.sunset+'</span>';
            //                html += '<p><strong>Tomorrow\'s Date</strong>: '+weather.tomorrow.day+' '+weather.tomorrow.date+'<br /><strong>Tomorrow\'s High/Low</strong>: '+weather.tomorrow.high+'/'+weather.tomorrow.low+'<br /><strong>Tomorrow\'s Forecast</strong>: '+weather.tomorrow.forecast+'<br /> <strong>Tomorrow\'s Image</strong>: '+weather.tomorrow.image+'</p>';
//    html += '<p><strong>Last updated</strong>: '+weather.updated+'</p>';
            //                html += '<p><a href="'+weather.link+'">View forecast at Yahoo! Weather</a></p>';

            $("#weather").html(html);
        },
        error: function(error) {
            $("#weather").html("<p>"+error+"</p>");
        }
    });


});


