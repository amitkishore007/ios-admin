<?php
ini_set("display_errors", 0);
/*error_reporting(E_ALL);*/
if(strtolower(trim($_SERVER['SERVER_NAME'])) == 'localhost')
{
	define("BASEURL", "http://localhost/ios-admin/admin");
	define("BASEPATH", getenv("DOCUMENT_ROOT")."/ios-admin/admin");
	define("FFMPEGPATH", "../ffmpeg/");
	define("GifCreatorPATH", "../GifCreator/src/GifCreator/GifCreator.php");
	define("HOST", "localhost");     // The host you want to connect to.
	define("USER", "root");    // The database username. 
	define("PASSWORD", "");    // The database password.
	define("DATABASE", "ios-admin");    // The database name.
}
else
{
	/********** Demo Server Link *********/
	define("DOMAIN", $_SERVER['SERVER_NAME']);
	define("BASEURL", "http://".DOMAIN."/admin");
	define("BASEPATH", getenv("DOCUMENT_ROOT")."/admin");
	define("FFMPEGPATH", "/var/www/html/ffmpeg");
	define("GifCreatorPATH", "/var/www/html/GifCreator/src/GifCreator/GifCreator.php");
	/********** Demo Server Link *********/

	/********** Demo Server Mysql Info *********/
	define("HOST", "localhost");     // The host you want to connect to.
	define("USER", "sbdb_user");    // The database username. 
	define("PASSWORD", "sbdb@pass");    // The database password.
	define("DATABASE", "sbth_ios");    // The database name.
	/********** Demo Server Mysql Info *********/
}
if(trim(strtolower($_SERVER['SERVER_NAME'])) == 'staging.studiobooth.us')
{
	define("S3BUCKET","http://s3-us-west-2.amazonaws.com/sb-staging-wrapper/");
}
else
{
	define("S3BUCKET","http://s3-us-west-2.amazonaws.com/sb-wrapper/");	
}

date_default_timezone_set('America/New_York');

define("SECURE", true);
$item_per_page = 5;
session_start();

/* DB CONNECTION */
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
include_once("Zebra_Pagination.php");
?>