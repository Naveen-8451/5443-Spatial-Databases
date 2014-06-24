###Assignment 2
  
### My OGR command


``````
ogr2ogr -f "MySQL" MySQL:"snaveen,host=localhost,user=snaveen,password=Naveen@123,port=3036" TM_WORLD_BORDERS-0.3.shp -nln World_Borders -update -overwrite -lco engine=MYISAM

``````

``````
#Table structure for table `world_borders`
``````

CREATE TABLE IF NOT EXISTS `world_borders` (
  `OGR_FID` int(11) NOT NULL AUTO_INCREMENT,
  `SHAPE` geometry NOT NULL,
  `fips` varchar(2) DEFAULT NULL,
  `iso2` varchar(2) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `un` decimal(3,0) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `area` decimal(7,0) DEFAULT NULL,
  `pop2005` decimal(10,0) DEFAULT NULL,
  `region` decimal(3,0) DEFAULT NULL,
  `subregion` decimal(3,0) DEFAULT NULL,
  `lon` double(8,3) DEFAULT NULL,
  `lat` double(7,3) DEFAULT NULL,
  UNIQUE KEY `OGR_FID` (`OGR_FID`),
  SPATIAL KEY `SHAPE` (`SHAPE`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=247 ;
