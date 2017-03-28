<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';



loginCheck();
$HighLightedTab = 1;
if (isset($_GET))
{
    $client_id = filter_input(INPUT_GET, 'client_id', FILTER_SANITIZE_NUMBER_INT) ? filter_input(INPUT_GET, 'client_id', FILTER_SANITIZE_NUMBER_INT) : 0;
	if($client_id){
		if ($result=$mysqli->query("SELECT * FROM users WHERE id = $client_id LIMIT 1")){
			
			if(!$result->num_rows){
				flash( 'msg', 'Event not found', 'error', BASEURL .'/client_list.php');
			}else{
				$clients = $result->fetch_assoc();
				
			}
		}else{
			flash( 'msg', 'Event not found', 'error', BASEURL .'/client_list.php');
		}
	}else{
		flash( 'msg', 'Event not specified', 'error', BASEURL .'/client_list.php');
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>StudioBooth - Edit Client</title>
	<?php include_once 'head.php';  ?>
	<script type="text/JavaScript" src="<?php echo BASEURL; ?>/js/sha512.js"></script> 
	<script type="text/JavaScript" src="<?php echo BASEURL; ?>/js/forms.js"></script>
	<script type="text/JavaScript">
		function ValidateForm()
		{
			var password = $.trim($("#password").val());
			var confirmpwd = $.trim($("#confirmpwd").val());

			var Error = "";
			if(password != "")
			{
				if(confirmpwd == "" || confirmpwd == null)
				{
					Error += "Please confirm your new password.\n";
				}
				else
				{
					if(password != confirmpwd)
					{
						Error += "New password & confirm password do not matched.\n";
					}
					else
					{
						if (password.length < 6) 
						{
							Error += "New passwords must be at least 6 characters long. Please try again.\n";
						}
						var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
						if (!re.test(password))
						{
							Error += "New password must contain at least one number, one lowercase and one uppercase letter.  Please try again.\n";
						}
					}
				}
			}
			if($.trim(Error) == "" || $.trim(Error) == null)
			{
				if(password != "")
				{
					// Create a new element input, this will be our hashed password field. 
					var p = document.createElement("input");

					// Add the new element to our form. 
					$("#registration_form")[0].appendChild(p);
					p.name = "p";
					p.type = "hidden";
					p.value = hex_sha512(password);
				}
				
				/*$("#password").val("");
				$("#confirmpwd").val("");*/
				return true;
			}
			else
			{
				alert(Error);
				return false;
			}
		}
		$(window).load(function(){
			$('#org_password').val($.trim($("#password").val()));
		});
	</script>
</head>
<body>
<?php include_once 'header.php'; ?>
<div class="title-row">
			<a href="<?php echo BASEURL; ?>/client_list.php" class="button fancy title-btn primary">View all clients</a>
			<h1 class="title">Edit Client</h1>
		</div>
	<div class="editUser">
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
       <br />
        <form action="<?php echo BASEURL; ?>/update_user.php" method="post" name="registration_form" id="registration_form" onsubmit="return ValidateForm();">
			<input type="hidden" name="uid"  value="<?php echo $clients['id'];?>">
			<input type="hidden" name="type" id="type" value="<?php echo trim($clients['type']); ?>" />
			<fieldset>
				<!-- <div class="field">
					<label for="clientname">Client Role:</label>
					<select name="type" id="type" class="select" required>
						<option value="">[-- Select Client Role --]</option>
						<option value="Superadmin" <?php if(strtolower(trim($clients['type'])) == 'superadmin'){ echo "selected";}?>>Super Admin</option>
						<option value="admin" <?php if(strtolower(trim($clients['type'])) == 'admin'){ echo "selected";}?>>Client</option>
					</select>
				</div> -->
				<div class="field">
					<label for="clientname">Client Name*</label>
					<input type="text" name="clientname" id="clientname" maxlength="100"  required="required" value="<?php echo $clients['full_name'];?>">
				</div>			
				<!-- <div class="field">
					<label for="username">UserName*</label>
					<input type="text" name="username" id="username" maxlength="100"  required="required" value="<?php echo $clients['username'];?>" required>
				</div> -->
				<input type="hidden" name="username" id="username" maxlength="100" value="<?php echo $clients['username'];?>" required>
				<div class="field">
					<label for="email">Email*</label>
					<input type="email" name="email" id="email" maxlength="100"  required="required" value="<?php echo $clients['email'];?>" required>
				</div>
				<div class="field">
					<label for="password">Password: (optional)</label>
					<input type="<?php if($_SESSION['type']== 'Superadmin'){ ?>text<?php }else{ ?>password<?php }?>" name="password" id="password" value="<?php echo $clients['org_password'];?>" onblur="$('#org_password').val(this.value);" required />
					<input type="hidden" name="org_password" id="org_password" value="" />
				</div>
				<div class="field">
					<label for="confirmpwd">Confirm password: (optional)</label>
					<input type="<?php if($_SESSION['type']== 'Superadmin'){ ?>text<?php }else{ ?>password<?php }?>" name="confirmpwd" id="confirmpwd" value="<?php echo $clients['org_password'];?>" required />
				</div>
				<?php
				if(strtolower(trim($clients['type'])) == 'admin')
				{
					?>
					<div class="field">
						<label for="can_create_events">Can Create Events?</label>
						<input type="checkbox" name="can_create_events" id="can_create_events" value="1" <?php if($clients['can_create_events'] == 1){ echo "checked"; }?> />
						<label for="can_create_events" style="display:inline-block;">Yes</lable>
					</div>
					<?php
				}?>
				<div class="action">
					<input type="submit" name="update" value="Update" /> 
				</div>
			</fieldset>
        </form>
       
	</div>
	<?php include_once 'footer.php';  ?>

</body>
</html>