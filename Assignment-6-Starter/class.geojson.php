<?php
/**
 * Title:   GeoJson (Requires https://github.com/phayes/geoPHP)
 * Notes:   Query a MySQL table or view and return the results in GeoJSON format, suitable for use in OpenLayers, Leaflet, etc.
 * Author:  Bryan R. McBride, GISP
 * Contact: bryanmcbride.com
 * GitHub:  https://github.com/bmcbride/PHP-Database-GeoJSON
 * Edited:  By Terry Griffin, organized as a CLASS for insructional purposes.
 */

# Include required geoPHP library and define wkb_to_json function
include_once('geoPHP/geoPHP.inc');

class MyGeoJson{
    var $conn;          // connection handle for mysql pdo class
    var $DbHost;        // e.g. localhost
    var $DbName;        // Database name
    var $DbPass;        // Database password
    var $DbUser;        // Database user
    var $GeoJsonArray;  // Result array of GeoJson data
    var $Result;        // Sql Result Handle
    var $Sql;           // Sql query

    public function __construct($db_name,$db_user,$db_pass,$db_host){
        $this->Sql = null;
        $this->Result = null;
        
        # Build GeoJSON feature collection array
        $this->GeoJsonArray = array(
           'type'      => 'FeatureCollection',
           'features'  => array()
        );
        
        $this->DbName = $db_name;
        $this->DbPass = $db_pass;
        $this->DbUser = $db_user;
        $this->DbHost = $db_host;
        $this->Conn = new PDO("mysql:host={$this->DbHost};dbname={$this->DbName}",$this->DbUser,$this->DbPass);
    }
    
    # Set the current query
    public function RunQuery($sql){
        
        $this->Sql = $sql;
        
        # Try query or error
        $this->Result = $this->Conn->query($this->Sql);

        if (!$this->Result) {
            echo 'An SQL error occured.\n';
            print_r($this->Sql);
            exit;
        }
        $this->Conn = NULL; 
    }
    
    public function GetSimpleResult(){ 

        $Data = array();
        $i = 0;
        
        # Loop through rows to build feature arrays
        while ($row = $this->Result->fetch(PDO::FETCH_ASSOC)) {
            $temp = json_decode($this->WkbToJson($row['wkb']));
            //print_r($temp);
            //echo"<br><br>";
            $Data[$i]['Type'] = $temp->type;
            $Data[$i]['Coordinates'] = $temp->coordinates;
            unset($row['wkb']);
            unset($row['SHAPE']);          
            $Data[$i]['properties'] = $row;
            $i++;
        }

       header('Content-type: application/json');
       echo json_encode($Data, JSON_NUMERIC_CHECK); 
    }
    
    # Run the Geo Sql Query
    public function GetGeoJsonResult(){ 

        # Loop through rows to build feature arrays
        while ($row = $this->Result->fetch(PDO::FETCH_ASSOC)) {
            $properties = $row;
            # Remove wkb and geometry fields from properties
            unset($properties['wkb']);
            unset($properties['SHAPE']);
            $feature = array(
                 'type' => 'Feature',
                 'geometry' => json_decode($this->WkbToJson($row['wkb'])),
                 'properties' => $properties
            );
            # Add feature arrays to feature collection array
            array_push($this->GeoJsonArray['features'], $feature);
        }
        
        header('Content-type: application/json');
        echo json_encode($this->GeoJsonArray, JSON_NUMERIC_CHECK);       
    }

    # Build SQL SELECT statement and return the geometry as a WKB element
    private function WkbToJson($wkb) {
        $geom = geoPHP::load($wkb,'wkb');
        return $geom->out('json');
    }
    
}
/////////////////////////////////////////////////////////////////////////////////////
//Main

$MyGeo = new MyGeoJson('5443_SpatialData','5443','5443','localhost');

if(isset($argv[1]) && $argv[1]=='debug' || isset($_GET['debug']) && $_GET['debug']){
    $_POST['lat'] = 33.546;
    $_POST['lng'] = -122.546;
}

$sql1 = "
	SELECT 
		OGR_FID,
		fullname, 
		latitude, 
		longitude,
		NumGeometries(SHAPE) AS Multi,
		AsWKB(SHAPE) as wkb, 
		69*haversine(latitude,longitude,latpoint, longpoint) AS distance_in_miles
	FROM military_installations
	JOIN (
	   SELECT  {$_POST['lat']}  AS latpoint,  {$_POST['lng']} AS longpoint
	) AS p
	ORDER BY distance_in_miles
	LIMIT 5
";

$sql2 = "
    SELECT 
        year,
        month,
        day,
        location, 
        AsWKB(SHAPE) AS wkb,
        69*haversine(latitude,longitude,latpoint, longpoint) AS distance_in_miles
    FROM earth_quakes
	JOIN (
	   SELECT  {$_POST['lat']}  AS latpoint,  {$_POST['lng']} AS longpoint
	) AS p
	ORDER BY distance_in_miles
    LIMIT 10
";

$sql3 = "
    SELECT 
        AsWKB(SHAPE) AS wkb
    FROM railroad
    LIMIT 30
";

$sql = $sql2;

$MyGeo->RunQuery($sql);
//$MyGeo->GetGeoJsonResult();
$MyGeo->GetSimpleResult();

