5443-Spatial-Databases
======================

5443-Spatial-Databases

## Spatial database Assignment 5:

#### File Extensions

1. Shapefile:

This document defines the shapefile (.shp) spatial data format and describes why shapefiles are important. It lists the tools available in Environmental Systems Research Institute, Inc. (ESRI), software for creating shapefiles directly or converting data into shapefiles from other formats. 
A shapefile stores nontopological geometry and attribute information for the spatial features in a data set. The geometry for a feature is stored as a shape comprising a set of vector coordinates Points, Polygons and Lines.

2. Osm:

The .osm file format is specific to OpenStreetMap. You won’t come across it elsewhere. If you have ever downloaded data using JOSM and saved it as a file, you may have noticed that the file is saved with the extension .osm. If you are a GIS user, you may also have noticed that it is not easy to open these files using software such as QGIS.

3. GeoJSON

The OGR GeoJSON driver maps each object of following types to new OGRFeature object: Point, LineString, Polygon, GeometryCollection, Feature.
According to the GeoJSON Specification, only the Feature object must have a member with name properties. Each and every member of properties is translated to OGR object of type of OGRField and added to corresponding OGRFeature object.
The GeoJSON Specification does not require all Feature objects in a collection must have the same schema of properties. If Feature objects in a set defined by FeatureCollection object have different schema of properties, then resulting schema of fields in OGRFeatureDefn is generated as union of all Feature properties.

4. GPX 

GPX (the GPS eXchange Format) is a data format for exchanging GPS data between programs, and for sharing GPS data with other users. Unlike other data files, which can only be understood by the programs that created them, GPX files actually contain a description of what's inside them, allowing anyone to create a program that can read the data within.

5. KML

KML file specifies a set of features (place marks, images, polygons, 3D models, textual descriptions, etc.) for display in Here Maps, Google Earth, Maps and Mobile, or any other geospatial software implementing the KML encoding. Each place always has a longitude and a latitude. Other data can make the view more specific, such as tilt, heading, altitude, which together define a "camera view" along with a timestamp or timespan.

6. CSV

A CSV is a comma separated values file, which allows data to be saved in a table structured format. CSVs look like a garden-variety spreadsheet but with a .csv extension (Traditionally they take the form of a text file containing information separated by commas, hence the name). CSV files can be used with any spreadsheet program, such as Microsoft Excel, Open Office Calc, or Google Spreadsheets. They differ from other spreadsheet file types in that you can only have a single sheet in a file, they can not save cell, column, or row styling, and can not save forumlas.

7. WKT

Well-known text (WKT) is a text markup language for representing vector geometry objects on a map, spatial reference systems of spatial objects and transformations between spatial reference systems. A binary equivalent, known as well-known binary (WKB) is used to transfer and store the same information on databases, such as PostGIS, Microsoft SQL Server and DB2. The formats were originally defined by the Open Geospatial Consortium (OGC) and described in their Simple Feature Access and Coordinate Transformation Service specifications.



#### Software:

1. QGIS

QGIS is a free and open source GIS program developed by a team volunteers with the support of non-profit organizations. It utilizes a graphical user interface. It is compatible with a variety of operating systems and is available in 32-bit and 64-bit versions. Users of ESRI software will find that many of the features and tools of ArcMap are also available in QGIS. The interface of QGIS is similar to ArcMap. For users of ESRI products, QGIS is a good way to explore open source GIS software.

2. GPSBabel

GPSBabel converts waypoints, tracks, and routes between popular GPS receivers such as Garmin or Magellan and mapping programs like Google Earth or Basecamp. Literally hundreds of GPS receivers and programs are supported. It also has powerful manipulation tools for such data.

3. GDAL

GDAL (Geospatial Data Abstraction Library) is a library for reading and writing raster geospatial data formats, and is released under the permissive X/MIT style free software license by the Open Source Geospatial Foundation. As a library, it presents a single abstractdata model to the calling application for all supported formats. It may also be built with a variety of useful command-line utilities for data translation and processing.

4. ArcGIS

ArcGIS is a geographic information system (GIS) for working with maps and geographic information. It is used for: creating and using maps; compiling geographic data; analyzing mapped information; sharing and discovering geographic information; using maps and geographic information in a range of applications; and managing geographic information in a database.

#### Definitions:

1. Point

A point is an exact position or location on a plane surface. It is important to understand that a point is not a thing, but a place. We indicate the position of a point by placing a dot with a pencil. This dot may have a diameter of, say, 0.2mm, but a point has no size. No matter how far you zoomed in, it would still have no width. Since a point is a place, not a thing, it has no dimensions.
Points are usually named by using an upper-case single letter. In the figure above, the points P,Q and R are shown. In this web site, points are shown either as a black dot or with a somewhat larger orange halo. This indicates the point can be dragged with a mouse.

2. Curve

In mathematics, an abstract term used to describe the path of a continuously moving point (see continuity). Such a path is usually generated by an equation. The word can also apply to a straight line or to a series of line segments linked end to end. A closed curve is a path that repeats itself, and thus encloses one or more regions. Simple examples include circles, ellipses, and polygons. Open curves such as parabolas, hyperbolas, and spirals have infinite length

3. LineString

A linestring is a Curve with linear interpolation between Points.

MultiCurve:

The multi-curves framework is often implemented in a way to recycle to one curve formulas; there are no fundamental reasons behind that choice. Here we present different approaches to the multi-curves framework. They vary by the choice of building blocks


4. MultiPolygon: 

A MultiPolygon instance is a collection of zero or more Polygon instances. The boundaryis defined by the two exterior rings and the three interior rings. The boundary is defined by the two exterior at a tangent point.
 Multilinestring:
Relations of type multilinestring are used to represent one on the ground feature made of one or multiple line strings made of even more ways compatible and as define by the Open Geospatial Consortium.

5. Surface Polygon:

A polygonal surface can be thought of as a surface composed of polygonal faces. The polygons must all be of the same type (triangles, quadrilaterals or whatever).
If the polygons are of higher order than triangles, the user must take care that the vertices of each polygon all lie in a plane. Otherwise, no planar polygon will pass through the vertices. If the nonplanarity is significant, then the display will not look right at all.

#### Relations:

1. Touch:

The pattern matrices show us that the touch predicate returns TRUE when the interiors of the geometry dont intersect and the boundary of either geometry intersects the others interior or boundary.
The touch predicate returns TRUE if the boundary of one geometry intersects the interior of the
other but the interiors do not intersect.

2. Overlap: 

Overlap compares two geometries of the same dimension and returns t (TRUE) if their intersection set results in a geometry different from both but of the same dimension.
Overlap returns t (TRUE) only for geometries of the same dimension and only when their intersection set results in a geometry of the same dimension. In other words, if the intersection of two polygons results in polygon, then overlap returns t (TRUE).
This pattern matrix applies to polygon/polygon, multipoint and multipolygon overlays. For these combinations the overlap predicate returns TRUE if the interior of both geometries intersects the others interior and exterior.

3. cross:

This cross predicate pattern matrix applies to multipoint/linestring, multipoint/multilinestring, multipoint/polygon, multipoint/multipolygon, linestring/polygon, and linestring/multipolygon. The matrix states that the interiors must intersect and that at least the interior of the primary (geometry a) must intersect the exterior of the secondary (geometry b).

4. Within: 

Within returns t (TRUE) if the first geometry is completely within the second geometry. Within tests for the exact opposite result of contains.
Within returns t (TRUE) if the first geometry is completely inside the second geometry. The boundary and interior of the first geometry are not allowed to intersect the exterior of the second geometry and the first geometry may not equal the second geometry.
The within predicate pattern matrix states that the interiors of both geometries must intersect and that the interior and boundary of the primary geometry (geometry a) must not intersect the exterior of the secondary (geometry b).



