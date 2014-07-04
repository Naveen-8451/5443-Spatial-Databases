<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Bounding Box</title>
    <style>
		section {
			width: 90%;
			height: 700px;
			background: #C0C0C0;
			margin: auto;
			padding: 5px;
		}
		div#map-canvas {
			width: 80%;
			height: 600px;
			float: left;
		}
		div#form-stuff {
			margin-left: 15%;
			padding: 20px;
			height: 600px;

		}
    </style>
    <!-- Include Google Maps Api to generate maps -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>

    <!-- Include Jquery to help with simplifying javascript syntax  -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="./geo.js"></script>
    <script>

        //Add a listener that runs "initialize" when page is done loading.
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
  <section>
    <div id="map-canvas"></div>
	<div id="form-stuff">
		<form id="Form1">
		Query Number: <input type="text" name="QueryNum" id="QueryNum" value="1"><br>
		Data Sources:<br>
        <?php
            $Conn = new PDO("pgsql:host=localhost;dbname=5443","5443","5443");
            $sql = "SELECT * FROM pg_catalog.pg_tables WHERE schemaname = 'public'";
            $result = $Conn->query($sql);
            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                if(!in_array($row['tablename'],array('tm_world_borders','spatial_ref_sys')))
               	  echo"&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"sources[]\" id=\"{$row['tablename']}\" value=\"{$row['tablename']}\"> {$row['tablename']}<br>";
            }
		
        ?>
    	&nbsp;&nbsp;&nbsp;Lat1: <input type="text" name="lat1" id="lat1"><br>
    	&nbsp;&nbsp;&nbsp;Lon1: <input type="text" name="lon1" id="lon1"><br>
    	&nbsp;&nbsp;&nbsp;Lat2: <input type="text" name="lat2" id="lat2"><br>
    	&nbsp;&nbsp;&nbsp;Lon2: <input type="text" name="lon2" id="lon2"><br>
        <a href="#" id="submit_form">Submit</a><br>
        </form>
	</div>
    <center>

    </center>
  </section>
  </body>
</html>
