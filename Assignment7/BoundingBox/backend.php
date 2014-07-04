<?php
error_reporting(0);

//Establish connection to database.
$Conn = new PDO("pgsql:host=localhost;dbname=5443","5443","5443");

if(isset($argv[1]) && $argv[1]=='debug'){
	$_POST = unserialize(file_get_contents("array.out"));
	print_r($_POST);
}

$fp = fopen('array.out','w');
fwrite($fp,serialize($_POST));
fclose($fp);


$fp = fopen('error.log','w');
fwrite($fp,time()."\n");
$out = print_r($_POST,true);
fwrite($fp,$out);


if(isset($argv[1]) && $argv[1]=='debug' || $_GET['debug']){
	$_POST['lat1'] = 33.546;
	$_POST['lon1'] = -122.546;
	$_POST['earthQuakes'] = true;
	$debug = true;
}

switch($_POST['QueryNum']){
	case 1:
		$Data = InsideRect($_POST);
		break;
	case 2:
		$Data = ContactRect($_POST);
		break;
	case 3:
		break;
	case 4:
	$Data = InsideRect($_POST);
		break;
	case 5:
		$Data = circle($_POST);
		break;
	case 6:
		break;
}

echo json_encode($Data);





function circle($post){
	global $fp;
	global $Conn;
	
	$Lat1 = $post['lat1'];
	$Lon1 = $post['lon1'];
	$Lat2 = $post['lat2'];
	$Lon2 = $post['lon2'];
	
	$Points = array();
	
	foreach($post['sources'] as $source){
		$sql = "
			SELECT ST_AsGeoJSON(wkb_geometry) AS wkb
			FROM {$source}
			WHERE wkb_geometry @ ST_MinimumBoundingCircle(ST_MakeEnvelope({$Lon1}, {$Lat1},{$Lon2},{$Lat2}))
			
		";
		//echo $sql."\n";
		//fwrite($fp,print_r($sql,true));
		$result = $Conn->query($sql);
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			//fwrite($fp,print_r("Hello\n",true));
			/* list($crap,$point) = explode(';',$row['p']);
			$Coords = substr($point,6,strlen($point)-7);
			$Coords = explode(' ',$Coords); */
			//fwrite($fp,print_r($row['wkb'],true));
			$Points[] = $row['wkb'];
		}
	}
	fwrite($fp,print_r($Points,true));
	//print_r($Points);
	return $Points;
}

function InsideRect($post){
	global $fp;
	global $Conn;
	
	$Lat1 = $post['lat1'];
	$Lon1 = $post['lon1'];
	$Lat2 = $post['lat2'];
	$Lon2 = $post['lon2'];
	
	$Points = array();
	
	foreach($post['sources'] as $source){
		$sql = "
			SELECT ST_AsGeoJSON(wkb_geometry) AS wkb
			FROM {$source}
			WHERE wkb_geometry @ ST_MakeEnvelope({$Lon1}, {$Lat1},{$Lon2},{$Lat2})
		";
		//echo $sql."\n";
		//fwrite($fp,print_r($sql,true));
		$result = $Conn->query($sql);
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			//fwrite($fp,print_r("Hello\n",true));
			/* list($crap,$point) = explode(';',$row['p']);
			$Coords = substr($point,6,strlen($point)-7);
			$Coords = explode(' ',$Coords); */
			//fwrite($fp,print_r($row['wkb'],true));
			$Points[] = $row['wkb'];
		}
	}
	fwrite($fp,print_r($Points,true));
	//print_r($Points);
	return $Points;
}

function ContactRect($post){
	global $fp;
	global $Conn;
	
	$Lat1 = $post['lat1'];
	$Lon1 = $post['lon1'];
	$Lat2 = $post['lat2'];
	$Lon2 = $post['lon2'];
	
	$Points = array();
	
	foreach($post['sources'] as $source){
	// I tried using ST_TOUCH an dfound not working so Updated. 
		$sql = "
			SELECT ST_AsGeoJSON(ST_SetSRID(wkb_geometry,4326)) AS wkb
			FROM {$source}
			WHERE ST_Intersects(wkb_geometry , ST_MakeEnvelope({$Lon1}, {$Lat1},{$Lon2},{$Lat2},4326))
		";
		//echo $sql."\n";
		fwrite($fp,print_r($sql,true));
		$result = $Conn->query($sql);
		while($row = $result->fetch(PDO::FETCH_ASSOC)){
			fwrite($fp,print_r("Hello\n",true));
			/* list($crap,$point) = explode(';',$row['p']);
			$Coords = substr($point,6,strlen($point)-7);
			$Coords = explode(' ',$Coords); */
			fwrite($fp,print_r($row['wkb'],true));
			$Points[] = $row['wkb'];
		}
	}
	fwrite($fp,print_r($Points,true));
	//print_r($Points);
	return $Points;
}

function sql_to_coordinates($blob)
{
	$blob = str_replace("))", "", str_replace("POLYGON((", "", $blob));
	$coords = explode(",", $blob);
	$coordinates = array();
	foreach($coords as $coord)
	{
		$coord_split = explode(" ", $coord);
		$coordinates[]=array(str_replace("\n","",$coord_split[0]),str_replace("\n","",$coord_split[1]));
	}
	return $coordinates;
}

function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}

function getFeatures(){
    $sql = "SELECT * FROM pg_catalog.pg_tables WHERE schemaname = 'public'";
    $result = $db->query($sql);
    $TableArray = array();
    while($row = $result->fetch(PDO::FETCH_ASSOC)){
        $TableArray[] = $row['tablename'];
    }
    echo json_encode($TableArray);
}
fclose($fp);
