5443-Spatial-Databases
======================

5443-Spatial-Databases

`````
####HOME WORK1
`````

``````
####Shapefile:
``````
This document defines the shapefile (.shp) spatial data format and describes why shapefiles are important.
It lists the tools available in Environmental Systems Research Institute, Inc.
(ESRI), software for creating shapefiles directly or converting data into shapefiles from other formats.A shapefile stores nontopological geometry and attribute information for the spatial features in a data set.
The geometry for a feature is stored as a shape comprising a set of vector coordinates Points, Polygons and Lines.

````
####Osm:
````
The .osm file format is specific to OpenStreetMap. You won’t come across it elsewhere.
If you have ever downloaded data using JOSM and saved it as a file, you may have noticed that the file is saved with the extension .osm.
If you are a GIS user, you may also have noticed that it is not easy to open these files using software such as QGIS.

`````
####GeoJSON
`````
The OGR GeoJSON driver maps each object of following types to new OGRFeature object:
Point, LineString, Polygon, GeometryCollection, Feature.According to the GeoJSON Specification, only the Feature object must have a member with name properties. Each and every member of properties is translated to OGR object of type of OGRField and added to corresponding OGRFeature object.
The GeoJSON Specification does not require all Feature objects in a collection must have the same schema of properties. If Feature objects in a set defined by FeatureCollection object have different schema of properties, then resulting schema of fields in OGRFeatureDefn is generated as union of all Feature properties.

````
####GPX
````
GPX (the GPS eXchange Format) is a data format for exchanging GPS data between programs, and for sharing GPS data with other users. Unlike other data files, which can only be understood by the programs that created them, GPX files actually contain a description of what's inside them, allowing anyone to create a program that can read the data within.

`````
####KML
`````
KML file specifies a set of features (place marks, images, polygons, 3D models, textual descriptions, etc.) for display in Here Maps, Google Earth, Maps and Mobile, or any other geospatial software implementing the KML encoding. Each place always has a longitude and a latitude. Other data can make the view more specific, such as tilt, heading, altitude, which together define a "camera view" along with a timestamp or timespan.

`````
####CSV
`````
A CSV is a comma separated values file, which allows data to be saved in a table structured format. 
CSVs look like a garden-variety spreadsheet but with a .csv extension (Traditionally they take the form of a text file containing information separated by commas, hence the name).
CSV files can be used with any spreadsheet program, such as Microsoft Excel, Open Office Calc, or Google Spreadsheets. They differ from other spreadsheet file types in that you can only have a single sheet in a file, they can not save cell, column, or row styling, and can not save forumlas.

`````
QGIS
`````
QGIS is a free and open source GIS program developed by a team volunteers with the support of non-profit organizations. It utilizes a graphical user interface.
It is compatible with a variety of operating systems and is available in 32-bit and 64-bit versions.
Users of ESRI software will find that many of the features and tools of ArcMap are also available in QGIS.
The interface of QGIS is similar to ArcMap. 
For users of ESRI products, QGIS is a good way to explore open source GIS software.

`````
GPSBabel
`````
GPSBabel converts waypoints, tracks, and routes between popular GPS receivers such as Garmin or Magellan and mapping programs like Google Earth or Basecamp. Literally hundreds of GPS receivers and programs are supported. It also has powerful manipulation tools for such data.

