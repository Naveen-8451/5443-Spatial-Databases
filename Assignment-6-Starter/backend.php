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

$sql = "
    (SELECT  
    AsWKB(earth_quakes.SHAPE) AS wkb
    FROM earth_quakes,state_borders WHERE CONTAINS (state_borders.SHAPE, earth_quakes.SHAPE)  
	AND state = (SELECT state FROM `state_borders` WHERE CONTAINS (SHAPE, POINT({$_POST['lng']}, {$_POST['lat']}))))
	union
	(SELECT asWKB(SHAPE) as wkb
		FROM state_borders  
		WHERE CONTAINS(state_borders.SHAPE,POINT({$_POST['lng']}, {$_POST['lat']})))
";

$MyGeo->RunQuery($sql);
$MyGeo->GetSimpleResult();
