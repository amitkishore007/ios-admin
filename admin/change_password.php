<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';



loginCheck();
$error_msg = "";
$HighLightedTab = 6;
if(isset($_POST['p']))
{
    $OldPassword = trim(@$_POST['OldPassword']);
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    $org_password = $_POST['org_password'];

    if (strlen($password) != 128) {
        $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }
    $prep_stmt = "SELECT id,password,salt FROM users WHERE id = '".$_SESSION['user_id']."' LIMIT 1";
	$result = $mysqli->query($prep_stmt);
	while($row = $result->fetch_assoc())
	{
		$db_password = trim($row['password']);
		$salt		= trim($row['salt']);
	}
	$OldPassword = hash('sha512', hash('sha512',$OldPassword) . $salt);
	if($db_password != $OldPassword)
	{
		$error_msg .= '<p class="error">Incorrect Current Password.</p>';
	}
    if (empty($error_msg))
	{
        // Create a random salt
        $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
        // Create salted password 
        $password = hash('sha512', $password . $random_salt);

		$update_stmt = "UPDATE users SET password = '".$password."', salt = '".$random_salt."',org_password='".$org_password."' WHERE id = '".$_SESSION['user_id']."'";
		$result = $mysqli->query($update_stmt);

		flash( 'msg', 'Password changed successfully.', 'success' );
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
	<title>StudioBooth - Change Password</title>
	<?php include_once 'head.php';  ?>
	<script type="text/JavaScript" src="<?php echo BASEURL; ?>/js/sha512.js"></script> 
	<script type="text/JavaScript" src="<?php echo BASEURL; ?>/js/forms.js"></script>
	<script type="text/JavaScript">
		function ValidateChanagePassForm()
		{
			var OldPassword = $.trim($("#OldPassword").val());
			var NewPassword = $.trim($("#NewPassword").val());
			var ConfirmPassword = $.trim($("#ConfirmPassword").val());

			var Error = "";
			if(OldPassword == "" || OldPassword == null)
			{
				Error += "Please enter your current password.\n";
			}
			if(NewPassword == "" || NewPassword == null)
			{
				Error += "Please enter your new password.\n";
			}
			else
			{
				if(ConfirmPassword == "" || ConfirmPassword == null)
				{
					Error += "Please confirm your new password.\n";
				}
				else
				{
					if(NewPassword != ConfirmPassword)
					{
						Error += "New password & confirm password do not matched.\n";
					}
					else
					{
						if (NewPassword.length < 6) 
						{
							Error += "New passwords must be at least 6 characters long. Please try again.\n";
						}
						var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
						if (!re.test(NewPassword))
						{
							Error += "New password must contain at least one number, one lowercase and one uppercase letter.  Please try again.\n";
						}
					}
				}
			}
			if($.trim(Error) == "" || $.trim(Error) == null)
			{
				// Create a new element input, this will be our hashed password field. 
				var p = document.createElement("input");

				// Add the new element to our form. 
				$("#changepass_form")[0].appendChild(p);
				p.name = "p";
				p.type = "hidden";
				p.value = hex_sha512(NewPassword);

				$("#NewPassword").val("");
				$("#ConfirmPassword").val("");
				return true;
			}
			else
			{
				alert(Error);
				return false;
			}
		}
	</script>
</head>
<body>
	<?php include_once 'header.php'; ?>
	<div class="title-row">
		<h1 class="title">Change Password</h1>
	</div>
    <ul style="margin-left:20px; list-style-type: disc;">
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
	<br>
    <form action="" method="post" id="changepass_form" name="changepass_form" onsubmit="return ValidateChanagePassForm();">
		<fieldset>
			<legend>Change Password</legend>
			<div class="field">
				<label for="OldPassword">Current Password:</label>
				<input type="password" name="OldPassword" id="OldPassword"/>
			</div>
			<div class="field">
				<label for="NewPassword">New Password:</label>
				<input type="password" name="NewPassword" id="NewPassword" onblur="$('#org_password').val(this.value);" />
				<input type="hidden" name="org_password" id="org_password" value="" />
			</div>
			<div class="field">
				<label for="ConfirmPassword">Confirm Password:</label>
				<input type="password" name="ConfirmPassword" id="ConfirmPassword" />
			</div>
			<div class="action">
				<input type="submit" value="Change Password" /> 
			</div>
		</fieldset>
    </form>
	<?php include_once 'footer.php';  ?>	
</body>
</html>