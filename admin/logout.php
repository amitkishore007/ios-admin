<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';



$_SESSION = array();
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);

session_unset();
session_destroy();
flash( 'msg', 'You have successfully logged out', 'success' );
header('Location: '. BASEURL .'/exit.php');
die;
?>