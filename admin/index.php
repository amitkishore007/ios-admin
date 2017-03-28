<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Forever marathon</title>
	<?php include_once 'head.php';  ?>
	<script type="text/JavaScript" src="<?php echo BASEURL; ?>/js/sha512.js"></script> 
	<script type="text/JavaScript" src="<?php echo BASEURL; ?>/js/forms.js"></script>
	<style type="text/css">
		#msg-flash
		{
			margin-left:0;
		}
	</style>
</head>
<body class="login-page" onload="$('#email').focus();">
	<?php flash('msg' ); ?>	
	<div class="login">
		<?php
        if (isset($_GET['error'])) {
            echo '<p class="error">Error Logging In!</p>';
        }
        ?> 
        <form action="login.php" method="post" name="login_form">
			<fieldset>
				<div class="logo">
					<a href="<?php echo BASEURL;?>dashboard.php"><img src="<?php echo BASEURL;?>/images/studiobooth.png" /></a>
				</div>
				<div class="field">
					<label for="email">Email:</label>
					<input type="email" id="email" name="email" />
				</div>
				<div class="field">
					<label for="password">Password:</label>
					<input type="password" name="password" id="password"/>
				</div>
				<div class="action">
					<input type="submit" value="Login" onclick="formhash(this.form, this.form.password); return false;" />
					<!-- <a href="javascript:void(0);" class="forgot-password-link">Forgot Password?</a> -->
					<div style="clear:both;"></div>
				</div>
			</fieldset>
			<!-- <pre>
				<?php 
				// $password = '123456';
				// $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
				// echo $random_salt;
				// echo '<br/>';
				// $password = hash('sha512', $password . $random_salt);
				// echo $password;

				?>
			</pre> -->
		</form>
	</div>
	<div class="forgot-password">
        <form action="" method="post" onsubmit="return ValidateForgotPassword();" name="forgot_pass_form">
			<fieldset>
				<div class="logo">
					<a href="<?php echo BASEURL;?>dashboard.php"><img src="<?php echo BASEURL;?>/images/studiobooth.png" /></a>
				</div>
				<b>Note:</b> Reset password link will be send on your registered email id.
				<br />
				<br />
				<div id="forgot-pass-result"></div>
				<div class="field">
					<label for="email">Registered Email:</label>
					<input type="email" id="registered_email" name="registered_email" required />
				</div>
				<div class="action">
					<input type="submit" value="Send mail" />
					<a href="javascript:void(0);" class="forgot-password-login">Login?</a>
					<div style="clear:both;"></div>
				</div>
			</fieldset>
		</form>
	</div>
</body>
</html>