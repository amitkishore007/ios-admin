<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';



loginCheck();
$uid=$_GET['client_id'];
$update_user = $mysqli->query("Delete from users  WHERE ID='$uid'");
if($update_user){
	flash( 'msg', 'Client Deleted Successfully', 'success', BASEURL .'/client_list.php');
}else{
   flash('msg', 'Failed to save Event', 'error', BASEURL .'/add_client.php');
}
?>