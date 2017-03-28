<header class="cf" id="header">
	<div class="logo">		
		<a href="#" target="SB"><!-- <img src="<?php //echo BASEURL;?>/images/" /> -->Forever Marathon</a>
	</div>
	<?php /*if($logged){*/
		$LoggedInUserFullName = ucwords(trim(@$_SESSION['full_name']));
		if(trim($_SESSION['full_name']) == "" || trim(@$_SESSION['full_name']) == null)
		{
			$LoggedInUserFullName = trim($_SESSION['type']);
		}
	?>
	<?php if($_SESSION['type']== 'Superadmin'){ ?>
	<nav id="nav">
		<ul class="menu">
			<li><span>Welcome <?=$LoggedInUserFullName?></span></li>
			<!-- <li <?php //if(@$HighLightedTab == 2){ echo 'class="Selected"';}?>><a href="<?php //echo BASEURL; ?>/view_events.php">Events</a></li> -->
			<li <?php if(@$HighLightedTab == 1){ echo 'class="Selected"';}?>><a href="<?php echo BASEURL; ?>/client_list.php">Users</a></li>
			<li <?php if(@$HighLightedTab == 3){ echo 'class="Selected"';}?>><a href="<?php echo BASEURL; ?>/donations.php">Donations</a></li>
			<!--<li <?php// if(@$HighLightedTab == 4){ echo 'class="Selected"';}?>><a href="<?php //echo BASEURL; ?>/media_list.php">Media</a></li>
			<li <?php //if(@$HighLightedTab == 5){ echo 'class="Selected"';}?>><a href="<?php// echo BASEURL; ?>/view_shareddata.php">Sharing Data</a></li>
			<li <?php //if(@$HighLightedTab == 6){ echo 'class="Selected"';}?>><a href="<?php //echo BASEURL; ?>/server_info.php">Server Info</a></li>-->
			<li><a href="<?php echo BASEURL; ?>/logout.php">Logout</a></li>


			<!-- <li <?php //if(@$HighLightedTab == 7){ echo 'class="Selected"';}?>><a href="<?php //echo BASEURL; ?>/view_userdata.php">User Data</a></li> -->
			<!-- <li><a href="<?php //echo BASEURL; ?>/occpucation_list.php">Occupations</a></li> -->
			<!-- <li <?php //if(@$HighLightedTab == 6){ echo 'class="Selected"';}?>><a href="<?php //echo BASEURL; ?>/change_password.php">Change Password</a></li> -->
			
		</ul></nav>
	<?php }else{?>
		
	<nav id="nav">
		<ul class="menu">
			<li><span>Welcome <?=$LoggedInUserFullName?></span></li>
			<li <?php if(@$HighLightedTab == 2){ echo 'class="Selected"';}?>><a href="<?php echo BASEURL; ?>/view_events.php">Events</a></li>
			<li <?php if(@$HighLightedTab == 4){ echo 'class="Selected"';}?>><a href="<?php echo BASEURL; ?>/media_list.php">Media</a></li>
			<li <?php if(@$HighLightedTab == 5){ echo 'class="Selected"';}?>><a href="<?php echo BASEURL; ?>/view_shareddata.php">Sharing Data</a></li>
			<li><a href="<?php echo BASEURL; ?>/logout.php">Logout</a></li>

			
			<!-- <li <?php //if(@$HighLightedTab == 7){ echo 'class="Selected"';}?>><a href="<?php //echo BASEURL; ?>/view_userdata.php">User Data</a></li> -->
			<!-- <li><a href="<?php //echo BASEURL; ?>/occpucation_list.php">Occupations</a></li> -->
			<!-- <li <?php //if(@$HighLightedTab == 6){ echo 'class="Selected"';}?>><a href="<?php //echo BASEURL; ?>/change_password.php">Change Password</a></li> -->
			
		</ul></nav>
	
	<?php } /*}*/ ?>
</header>
<?php flash('msg' ); ?>
<section class="wrapper">