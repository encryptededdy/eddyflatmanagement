<?php
include 'private.php';
// Set thymezone
date_default_timezone_set('Pacific/Auckland');

// DarkSky API
$json = file_get_contents("https://api.darksky.net/forecast/".$darkskyapi."/-36.8536,174.776?units=si"); 
$response = json_decode($json, true);

// Read the data
$hourlySumamry = $response['hourly']['summary'];
$hourlyIcon = $response['hourly']['icon'];
$weekSumamry = $response['daily']['summary'];
$weekIcon = $response['daily']['icon'];

// Parse ical

require_once 'vendor/autoload.php';

function getNextEventiCal($filename) {
  $cal = new \om\IcalParser();
  $current_date = new DateTime();
  $results = $cal->parseFile($filename);
  foreach ($cal->getSortedEvents() as $r) {
    if ($r['DTSTART'] > $current_date) {
      $next = sprintf('%s at <h2 style="margin: 0;">%s</h2>', $r['SUMMARY'], $r['DTSTART']->setTimezone(new DateTimeZone('Pacific/Auckland'))->format('l h:i A'));
      break;
    }
  }
  return $next;
}

function getNextEventiCalCountdown($filename) {
  $cal = new \om\IcalParser();
  $current_date = new DateTime();
  $results = $cal->parseFile($filename);
  foreach ($cal->getSortedEvents() as $r) {
    if ($r['DTSTART'] > $current_date) {
      $next = sprintf('%d Days (%s)', $r['DTSTART']->setTimezone(new DateTimeZone('Pacific/Auckland'))->diff($current_date)->format('%a'), $r['DTSTART']->format('l'));
      break;
    }
  }
  return $next;
}

$eddynext = getNextEventiCal($eddyurl);
$laurennext = getNextEventiCal('cal/lauren.ics');
$sebnext = getNextEventiCal('cal/seb.ics');
$thomasnext = getNextEventiCal($thomasurl);
$rentnext = getNextEventiCalCountdown($renturl);

?>
<!DOCTYPE html> <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content 
must come *after* these tags -->
    <meta http-equiv="refresh" content="480">
    <title>EddySign</title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries 
-->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="js/skycons.js"></script>
    <script type="text/javascript">
      var tday=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");

      function GetClock(){
      var d=new Date();
      var nday=d.getDay();
      var nhour=d.getHours(),nmin=d.getMinutes(),nsec=d.getSeconds(),ap;

      if(nhour==0){ap=" AM";nhour=12;}
      else if(nhour<12){ap=" AM";}
      else if(nhour==12){ap=" PM";}
      else if(nhour>12){ap=" PM";nhour-=12;}

      if(nmin<=9) nmin="0"+nmin;
      if(nsec<=9) nsec="0"+nsec;

      document.getElementById('clockbox').innerHTML=""+tday[nday]+" "+nhour+":"+nmin+":"+nsec+ap+"";
      }

      window.onload=function(){
      GetClock();
      setInterval(GetClock,1000);
      }
    </script>
  </head>
  <body>
    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1 id="clockbox"></h1>
        <div class="row">
          <div class="col-md-6">
            <p>Today: </p>
            <div class="row">
              <div class="col-md-2"><canvas class="<?php echo $hourlyIcon; ?>" width="50" height="70"></canvas></div>
              <div class="col-md-10"><h3 style="color: #fff; display:inline;"> <?php echo $hourlySumamry; ?></h3></div>
            </div>
          </div>
          <div class="col-md-6">
            <p>This Week: </p>
            <div class="row">
              <div class="col-md-2"><canvas class="<?php echo $weekIcon; ?>" width="50" height="70"></canvas></div>
              <div class="col-md-10"><h3 style="color: #fff; display:inline;"> <?php echo $weekSumamry; ?></h3></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4">
          <p style="font-size: 12pt;">Eddy has <?php echo $eddynext; ?></p>
          <p style="font-size: 12pt;">Lauren has <?php echo $laurennext; ?></p>
          <p style="font-size: 12pt;">Thomas has <?php echo $thomasnext; ?></p>
          <p style="font-size: 12pt;">Seb has <?php echo $sebnext; ?></p>
        </div>
        <div class="col-md-4">
          <p style="font-size: 12pt;">Rent next due:</p>
          <h2 style="margin: 0;"><?php echo $rentnext; ?></h2>
          <br>
       </div>
        <div class="col-md-4">
          <img src="http://www.sitecam.co.nz/auckland_webcam/image.php?id=1" style="max-width: 420px;">
        </div>
      </div>

      <hr>
      <footer>
        <p>Eddy FlatManagementSystem&trade; v1.0. Last refreshed <?php echo date("g:i:s a");?>.<img src="poweredby-oneline.png" style="max-width: 200px; float: right;"></p>
      </footer>
    </div> <!-- /container -->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script 
src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed 
-->
    <script src="js/bootstrap.min.js"></script>
    <script>
      var skycons = new Skycons({"color": "#ffffff"}),
          list  = [
            "clear-day", "clear-night", "partly-cloudy-day",
            "partly-cloudy-night", "cloudy", "rain", "sleet", "snow", "wind",
            "fog"
          ],
          i;

        for(i = list.length; i--; ) {
            var weatherType = list[i],
                elements = document.getElementsByClassName( weatherType );
            for (e = elements.length; e--;){
                skycons.set( elements[e], weatherType );
            }
        }

      skycons.play();
    </script>
  </body>
</html>
