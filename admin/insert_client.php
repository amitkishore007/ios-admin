<?php 
include_once("dbconfig.php");
include_once 'layouts/common.php';



include_once 'function.php';
loginCheck();

if (isset($_POST['add'])) {

	$fullname = addslashes($_POST['fullname']);
	$username = addslashes($_POST['username']);
	$email = addslashes($_POST['email']);
	$password = addslashes($_POST['password']);
	$org_password = $_POST['org_password'];
	$type = addslashes($_POST['type']);
	if ($fullname == '' || $username == '' || $email=='' || $password=='')
		{
			flash( 'msg', 'Please fill in all required fields!', 'error' );
			header('Location: ./add_client.php');
			exit;
		}
	else
	{

	$prep_stmt = "SELECT id FROM users WHERE username = ? LIMIT 1";
	$stmt = $mysqli->prepare($prep_stmt);
	if ($stmt) {
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$stmt->store_result();
		if ($stmt->num_rows == 1) {
	   
			flash( 'msg', 'A User ID already exists.', 'error' );
			header('Location: ./add_client.php');
			exit;
		}
		else
		{
		 $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
		 $password = hash('sha512', $password . $random_salt);
		if ($insert_stmt = $mysqli->prepare("INSERT INTO users (full_name,username,email, password,salt,type,org_password) VALUES (?, ?, ?, ?, ?, ?, ? )")) 
		{
			$insert_stmt->bind_param('sssssss', $fullname,$username, $email, $password, $random_salt, $type, $org_password );
			
			if (!$insert_stmt->execute()) 
			{
				header('Location: ./client_list.php?err=Registration failure: INSERT');
			}
		}
		flash( 'msg', 'User Created Successfully', 'success' );
		header('Location: ./client_list.php');
		}
		}
	} 
	}
	else
	{
		
	}
 ?>