<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';



if (isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];
    $password = $_POST['p']; // The hashed password.
	//$userId=$_SESSION['user_id'];
	
    if (login($email, $password, $mysqli) == true) {
		flash( 'msg', 'Welcome to Admin Panel', 'success', BASEURL .'/client_list.php');
    } else {
		flash( 'msg', 'Incorrect email/password.', 'error', BASEURL .'/index.php');
    }
} else {
    // The correct POST variables were not sent to this page. 
    echo 'Invalid Request';
}
?>