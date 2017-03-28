<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';



include_once 'function.php';
loginCheck();
$HighLightedTab = 1;
?>
<!DOCTYPE html>
<html>
<head>
	<title>StudioBooth - Clients</title>
	<?php include_once 'head.php';  ?>
</head>
<body>
<?php include_once 'header.php'; ?>

<div class="dashboard">
	<div class="title-row">			
		<a href="<?php echo BASEURL; ?>/add_client.php" class="button fancy title-btn primary">Add New Client</a>			
		<h1 class="title">Clients</h1>
	</div>
	<div class="dashboard">
		<form action="" method="GET" name="userdata">
			<fieldset>
				<legend>Search</legend>
				<div class="field">				
					<label for="username">Keyword</label>
					<input type="text" class="textbox1" size="40" name="keyword" value="<?php echo $_GET['keyword']; ?>" />
					<br />
					<input align="left" type="submit" value="View" name="search" class="button>">
				</div>				
			</fieldset>
		</form>
	</div>
	<div class="data-table">
		<?php
		$records_per_page = 50;
		$pagination = new Zebra_Pagination();

		$sql="SELECT SQL_CALC_FOUND_ROWS *  FROM users WHERE 1 ";
		if(!empty($_GET['keyword'])){
			$sql.=" AND (full_name like '%".trim($_GET['keyword'])."%') ";
		}

		$results=$mysqli->query($sql." ORDER BY type DESC, full_name ASC LIMIT ".(($pagination->get_page() - 1) * $records_per_page) . ", ".$records_per_page);
		
        // fetch the total number of records in the table
        $TotalRecordsQuery = $mysqli->query('SELECT FOUND_ROWS() AS rows');
		$TotalRecords = $TotalRecordsQuery->fetch_assoc()['rows'];

        // pass the total number of records to the pagination class
        $pagination->records($TotalRecords);

        // records per page
        $pagination->records_per_page($records_per_page);
		?>
		<?php $pagination->render();?>
		<table class="dtable">
			<thead>
				<tr>
					<!-- <th width="20">S.No</th> -->
					<th>first Name</th>
					<th>last Name</th>
					<th>Email Address</th>
					<th>Create Date</th>
					<th>Role</th>
					<th>Options</th>
			</thead>
			<tbody>
				<?php
				$i=0;
				while ($user = $results->fetch_assoc()){
				$i++;
				?>
				<tr class="<?php echo TblRowBgColor();?>">
					<!-- <td><?php echo SerialNo($i,$records_per_page,$pagination->get_page()); ?></td> -->
					
					<td><?php echo $user['fname']; ?></td>
					<td><?php echo $user['lname']; ?></td>
					<td><?php echo $user['email']; ?></td>
					<td><?php echo $user['create_date']; ?></td>
					<td>
						<?php
						if(strtolower(trim($user['type'])) == 'admin')
						{
							if($user['can_create_events'] == 1)
							{
								?>
								<div style="color:darkorange">Client Admin</div>
								<?php
							}
							else
							{
								echo "Client";
							}
						}
						else
						{
							?>
							<div style="color:green">Super Admin</div>
							<?php
						}?>
					</td>
					<td style="width:125px">
						<a href="<?php echo BASEURL; ?>/edit_client.php?client_id=<?php echo $user['id']; ?>" title="Edit" class="edit">Edit</a>
						<?php
						if($user['status'] < 1)
						{
							?>
							<a class="delete" href="javascript:confirmDelete('delete_client.php?client_id=<?php echo $user['id']; ?>')" title="Delete">Delete</a>
							<?php
						}?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php $pagination->render();?>
	</div>
</div>
<?php include_once 'footer.php';  ?>
</body>
</html>