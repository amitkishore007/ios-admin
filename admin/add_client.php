<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';



loginCheck();
$error_msg = "";
$HighLightedTab = 1;
 
if (isset($_POST['email'], $_POST['p'])) {

	$fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
	$lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
	
    // $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    // $can_create_events = filter_input(INPUT_POST, 'can_create_events', FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_POST, 'can_create_events', FILTER_SANITIZE_NUMBER_INT) : 0;
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    $org_password = $_POST['org_password'];
    if (strlen($password) != 128) {
        $error_msg .= '<p class="error">Invalid password configuration.</p>';
    } 
    $prep_stmt = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $error_msg .= '<p class="error">A user with this email address already exists.</p>';
        }
    } else {
        $error_msg .= '<p class="error">Database error</p>';
    } 
    if (empty($error_msg)) {
        // Create a random salt
        $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
        // Create salted password 
        $password = hash('sha512', $password . $random_salt);
 
        // Insert the new user into the database 
        if ($insert_stmt = $mysqli->prepare("INSERT INTO users (fname,lname, email, password, salt, type,org_password) VALUES (?, ?, ?, ?, ?,?,?)")) {
            $insert_stmt->bind_param('ssssssis',$fname, $lname, $email, $password, $random_salt, $type,$org_password);
            // Execute the prepared query.
            if (!$insert_stmt->execute()) {
                header('Location: ./add_client.php?err=Registration failure: INSERT');
            }
            $insertedID = $insert_stmt->insert_id;

	        if($insertedID > 0)
	        {
	        	WelcomeMail($insertedID);
	        }
        }
		flash( 'msg', 'User Created Successfully', 'success' );
        header('Location: ./client_list.php');
    }
	else
	{
		flash( 'msg', $error_msg, 'error' );
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>StudioBooth - Add Client</title>
	<?php include_once 'head.php';  ?>
	<script type="text/JavaScript" src="<?php echo BASEURL; ?>/js/sha512.js"></script> 
	<script type="text/JavaScript" src="<?php echo BASEURL; ?>/js/forms.js?t=<?=time();?>"></script>	
	<script type="text/JavaScript">
		function ValidateForm()
		{
			if($.trim($("#type").val()) == '')
			{
				alert("Please select client role.");
				return false;
			}
			else
			{
				regformhash($("#registration_form")[0], $("#registration_form")[0].clientname, $("#registration_form")[0].email, $("#registration_form")[0].password, $("#registration_form")[0].confirmpwd)
				return false;
			}
		}
	</script>	
</head>
<body>
	<?php include_once 'header.php'; ?>
	<div class="title-row">
		<a href="<?php echo BASEURL; ?>/client_list.php" class="button fancy title-btn primary">View all Clients</a>
		<h1 class="title">Add New Client</h1>
	</div>
	<div class="editUser">
       	<div class="events content-area" style="padding:0">
	        <ul style="margin-left:20px; list-style-type: disc;">
	            <!-- <li>Usernames may contain only digits, upper and lower case letters and underscores *</li> -->
	            <li>Emails must have a valid email format*</li>
	            <li>Passwords must be at least 6 characters long*</li>
	            <li>Passwords must contain
					<ul style="margin-left:30px; list-style-type: square;">
						<li>At least one upper case letter (A-Z)</li>
						<li>At least one lower case letter (a-z)</li>
						<li>At least one number (0-9)</li>
					</ul>
	            </li>
	            <li>Your password and confirmation must match exactly</li>
	        </ul>
			<div style="clear:both"></div>
			<br>
	        <form action="<?php echo $curPage; ?>" method="post" name="registration_form" id="registration_form" onsubmit="return ValidateForm();">
	        	<input type="hidden" name="type" id="type" value="admin" />
				<fieldset>
					<legend>Add New Client</legend>
					<!-- <div class="field">
						<label for="clientname">Client Role:</label>
						<select name="type" id="type" class="select" required>
							<option value="">[-- Select Client Role --]</option>
							<option value="Superadmin">Super Admin</option>
							<option value="admin">Client</option>
						</select>
					</div> -->
					<div class="field">
						<label for="clientname">First Name:*</label>
						<input type='text' name='fname' id='clientname' required />
					</div>
					<div class="field">
						<label for="clientname">Last Name:*</label>
						<input type='text' name='lname' id='username' required />
					</div>
					<!-- <div class="field">
						<label for="username">Username:*</label>
						<input type='text' name='username' id='username' required />
					</div> -->
					<!-- <input type='hidden' name='username' id='username' /> -->
					<div class="field">
						<label for="email">Email:*</label>
						<input type="text" name="email" id="email" required />
					</div>
					<div class="field">
						<label for="password">Password:*</label>
						<input type="password" name="password" id="password" onblur="$('#org_password').val(this.value);" required />
						<input type="hidden" name="org_password" id="org_password" value="" />
					</div>
					<div class="field">
						<label for="confirmpwd">Confirm password:</label>
						<input type="password" name="confirmpwd" id="confirmpwd" required />
					</div>
					<!-- <div class="field">
						<label for="can_create_events">Can Create Events?</label>
						<input type="checkbox" name="can_create_events" id="can_create_events" value="1" />
						<label for="can_create_events" style="display:inline-block;">Yes</lable>
					</div> -->
					<div class="action">
						<input type="submit" vaLue="Add Client" /> 
					</div>
				</fieldset>
			</form>        
       	<div>
	</div>
	<?php include_once 'footer.php';  ?>
</body>
</html>