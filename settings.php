<?php
ini_set("display_errors", 0);
//error_reporting(E_ALL);
//define("IS_STAGING", true);
if(strtolower(trim($_SERVER['SERVER_NAME'])) == 'localhost')
{
	define("HOST", "localhost");     // The host you want to connect to.
	define("USER", "root");    // The database username. 
	define("PASSWORD", "");    // The database password.
	define("DATABASE", "ios-admin");    // The database name.

	define("DOMAIN", "localhost");
}
else
{
	define("HOST", "localhost");     // The host you want to connect to.
	define("USER", "sbdb_user");    // The database username. 
	define("PASSWORD", "sbdb@pass");    // The database password.
	define("DATABASE", "sbth_ios");    // The database name.
	
	/*if(IS_STAGING == true)
	{
		define("DOMAIN", "staging.studiobooth.us");
	}
	else
	{
		define("DOMAIN", "studiobooth.us");
	}*/
	$S3_Buckets['wrapper'] 		= "sb-wrapper";
	$S3_Buckets['raw-media'] 	= "sb-raw-media";

	define("DOMAIN", $_SERVER['SERVER_NAME']);
	if(trim(strtolower($_SERVER['SERVER_NAME'])) == 'staging.studiobooth.us')
	{
		$S3_Buckets['wrapper'] 		= "sb-staging-wrapper";
		$S3_Buckets['raw-media'] 	= "sb-staging-raw-media";
	}
}
define("ROOT_DIR", __DIR__);

date_default_timezone_set('America/New_York');

define("SECURE", true);
$item_per_page = 5;
session_start();

/* DB CONNECTION */
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);

?>