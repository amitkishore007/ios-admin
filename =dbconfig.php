<?php
include_once("settings.php");
if(strtolower(trim($_SERVER['SERVER_NAME'])) == 'localhost')
{
	/**** Please update dbconfig.php file in admin & wrapper folder too ***/
	define("BASEURL", "http://localhost/ios-admin/admin");
	define("BASEPATH", getenv("DOCUMENT_ROOT")."/ios-admin/admin");
	define("FFMPEGPATH", "../ffmpeg/");
	define("GifCreatorPATH", "../GifCreator/src/GifCreator/GifCreator.php");
}
else
{
	/**** Please update dbconfig.php file in root & admin folder too ***/

	define("BASEURL", "http://".DOMAIN);
	define("BASEPATH", getenv("DOCUMENT_ROOT")."");
	define("FFMPEGPATH", "/var/www/html/ffmpeg");
	define("GifCreatorPATH", "/var/www/html/GifCreator/src/GifCreator/GifCreator.php");
}

define("S3BUCKET","http://s3-us-west-2.amazonaws.com/".$S3_Buckets['wrapper']."/");
?>