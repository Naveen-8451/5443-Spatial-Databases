5443-Spatial-Databases
======================

5443-Spatial-Databases

<?php
//Establish connection to database.
$db = new mysqli('localhost', 'snaveen', 'Naveen@123', 'snaveen');

//If no connection, then kill page
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
?>
<?php
if(sizeof($_POST)>0){
	$sql = "
	SELECT fullname, latitude, longitude,
		   69*haversine(latitude,longitude,latpoint, longpoint) AS distance_in_miles
	 FROM military_installations
	 JOIN (
		 SELECT  {$_POST['lat']}  AS latpoint,  {$_POST['lng']} AS longpoint
	   ) AS p
	 ORDER BY distance_in_miles
	 LIMIT 10";

	 $result = $db->query($sql);
	 
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Simple Polygon</title>
    <style>
      html, body, #map-canvas {
        height: 600px;
		width: 800px;
        margin: 0px;
        padding: 0px
      }
    </style>
    <!-- Include Google Maps Api to generate maps -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    
    <!-- Include Jquery to help with simplifying javascript syntax  -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script>

	//Runs when page is done loading
	function initialize() {
	  //Javascript object to help configure google map.
	  var mapOptions = {
		zoom: 5,
		center: new google.maps.LatLng(39.707, -101.503),
		mapTypeId: google.maps.MapTypeId.TERRAIN
	  };

	  //Create google map, place it in 'map-canvas' element, and use 'mapOptions' to 
	  //help configure it
	  var map = new google.maps.Map(document.getElementById('map-canvas'),
		  mapOptions);
		var PolygonCoords = new Array();
		var PolyGon = new Array();

		<?php
		//If we posted a lat lon, then loop through the resulting query rows and create 
		//a google marker for each location.
		if(sizeof($_POST)>0){
			$i=0; $j = 0;
			while($row = $result->fetch_assoc()){
				//[0] => Picatinny Arsenal [fullname] => Picatinny Arsenal [1] => 40.9534721 [latitude] => 40.9534721 [2] => -74.5444565 [longitude] => -74.5444565 [3] => 30.78561294078827 [distance_in_miles] => 30.78561294078827 ) 
				echo"var marker{$i} = new google.maps.Marker({\n";
				echo"position: new google.maps.LatLng({$row['latitude']},{$row['longitude']}),\n";
				echo"map: map,\n";
				echo"title:\"{$row['fullname']}\"\n";
				echo"});\n";
				$i++;
				if($j<$_POST['base']){
				$result1 = $db->query("SELECT asText(SHAPE) as border
                                                                  FROM `military_installations`
                                                                  WHERE fullname = '{$row['fullname']}'");
						$Result = $result1->fetch_assoc();

                       $CoordsArray[$j] = sql_to_coordinates($Result['border']);
					   $j++;
				}
				
			}
			for($i=0;$i<sizeof($CoordsArray);$i++){

                         echo"var Temp = [\n";
                         echo"// Define the LatLng coordinates for the polygon's path.\n";
                         array_shift($CoordsArray[$i]);
                         $line=0;
                         foreach($CoordsArray[$i] as $c){
                                $lat = $c['lat'];
                                $lng = $c['lng'];
                                $lat = str_replace("(","",$lat);
                                $lng = str_replace("(","",$lng);
                                $lat = str_replace(")","",$lat);
                                $lng = str_replace(")","",$lng);
                                echo "new google.maps.LatLng({$lng},{$lat})";
                                if($line < sizeof($CoordsArray[$i])-1){
                                       echo ",\n";
								}else{
                                       echo "\n";
                                }
                                $line++;
                         }
                         echo"];\n";
                         echo"PolygonCoords.push(Temp);\n";

                         echo"// Construct the polygon.\n";
                         echo"PolyGon[{$i}] = new google.maps.Polygon({\n";
                                echo"paths: PolygonCoords[{$i}],\n";
                                echo"strokeColor: '#";random_color();
								echo"',\n";
                                echo"strokeOpacity: 0.8,\n";
                                echo"strokeWeight: 2,\n";
                                echo"fillColor: '#";random_color();
								echo"',\n";
                                echo"fillOpacity: 0.35\n";
                         echo"});\n";

                         echo"PolyGon[{$i}].setMap(map);\n";
                 }
		}
		?>
  	  //Add the "click" event listener to the map, so we can capture
  	  //lat lon from a google map click.
	  google.maps.event.addListener(map, "click", function(event) {
		var lat = event.latLng.lat();
		var lng = event.latLng.lng();
		// populate yor box/field with lat, lng
		//console.write("Lat=" + lat + "; Lng=" + lng);
		$('#lat').val(lat);		//write lat to appropriate form field
		$('#lng').val(lng);		//same with lng
	  });
  
	}

	//Add a listener that runs "initialize" when page is done loading.
	google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
	<div id="FormDiv">
	<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
	Lat:<input type="text" name="lat" id="lat"><br>
	Lng:<input type="text" name="lng" id="lng"><br>
	Number of bases: <input type="text" name="base" id="base"><br>
	<input type="submit" name="submit" value="Get pos and base"> <br>
	</div>
	</form>
    <div id="map-canvas"></div>
  </body>
</html>
<?php
function sql_to_coordinates($blob)
   {
       $blob = str_replace("))", "", str_replace("POLYGON((", "", $blob));
       $coords = explode(",", $blob);
       $coordinates = array();
       foreach($coords as $coord)
       {
           $coord_split = explode(" ", $coord);
           $coordinates[]=array("lat"=>$coord_split[0], "lng"=>$coord_split[1]);
       }
       return $coordinates;
   }

       function random_color_part() {
				$s = str_pad( dechex(mt_rand( 0,255 ) ), 2, '0', STR_PAD_LEFT);
				print_r($s);
               //return $s;
       }

       function random_color() {
               random_color_part() . random_color_part() . random_color_part();
       }
?>

