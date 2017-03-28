<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';



include_once('../email/settings.php');
include_once("../twilio/Services/Twilio.php");
include_once("../twilio/Services/twilio-settings.php");
include_once('function.php');

if(@$_GET['mode'] == 'openstats')
{
	$eventShortCode = trim($_GET['event_id']);
	$_GET['event_id'] = shorty($eventShortCode, true)-10000;
}
else
{
	loginCheck();
	$eventShortCode = shorty($_GET['event_id']+10000);
}
$oldmask = umask(0);
@mkdir("../graphs-imgs/",0777,true);
umask($oldmask);
?>
<!DOCTYPE html>
<html>
<head>
	<title>StudioBooth - Dashboard</title>
	<?php include_once 'head.php';  ?>
	<script type="text/javascript" src="<?php echo BASEURL; ?>/js/jqBarGraph.1.1.js?t=1"></script>
	<script type="text/javascript" src="<?php echo BASEURL; ?>/js/d3.min.js"></script>
	<script type="text/javascript" src="<?php echo BASEURL; ?>/js/d3pie.js"></script>
	<script type="text/javascript" src="<?php echo BASEURL; ?>/js/saveSvgAsPng.js"></script>
	<script>
		function savePieChart(svgContainer,imgName)
		{
			return true;
		    //var canvas 	= $("#"+svgContainer).find('svg')[0];

		    var canvas 	= $("#"+svgContainer).find('svg').html();
		    var mychart = $('<div/>')
		    mychart.append(canvas);
		    mychart.find("g.p1_tooltips, g.p0_tooltips").remove();
		    $('body').append(mychart);

		    try
		    {
				var Data = "&Mode=SaveImage&imgData="+encodeURIComponent(mychart.html())+"&imgName="+encodeURIComponent(imgName);
				$.ajax({
					type: "POST",
					url: "dbbyajax.php", 
					cache: false,
					data: Data,
					success: function(response){
						$("#DownloadLink").show();
	    				console.debug(imgName);
					}
				});
				/*svgAsPngUri(canvas, null, function(uri) {
					var Data = "&Mode=SaveImage&imgData="+encodeURIComponent(uri)+"&imgName="+encodeURIComponent(imgName);
					$.ajax({
						type: "POST",
						url: "dbbyajax.php", 
						cache: false,
						data: Data,
						success: function(response){
							$("#DownloadLink").show();
		    				console.debug(imgName);
						}
					});
				});*/
		    }
		    catch(err)
		    {
		    }
		}
	</script>
</head>
<body style="<?php if(@$_GET['mode'] == 'openstats'){ echo "background:url('');";}?>">
	<?php
	if(@$_GET['mode'] != 'openstats')
	{
		include_once 'header.php';
	}
	else
	{
		?>
		<div style="padding:10px;">
		<?php
	}
	?>
	<div class="dashboardWrapper">
		<?php
		$sql="SELECT * FROM events where id = '".trim($_GET['event_id'])."'";
		if($_GET['mode'] != 'openstats')
		{
			if(!empty($_GET['client']))
			{
				$sql.=" AND client_id='{$_GET['client']}'";
			}
			else
			{
				if($_SESSION['type'] != 'Superadmin' )
				{
					$sql.=" AND client_id='{$_SESSION['user_id']}'";
				}
			}
		}
		$sql_result 	= $mysqli->query($sql." ORDER BY id DESC") or print mysql_error();
		$totalEvents	= mysqli_num_rows($sql_result) or print mysql_error();
		while($event=mysqli_fetch_array($sql_result))
		{
			$eventId 	= $event['id'];
			$clientId 	= $event['client_id'];
			$eventName 	= $event['event_name'];
			$preset_id 	= $event['preset_id'];
			$slug 		= $event['slug'];

			if($preset_id != 0)
			{
				$setting = $mysqli->query("SELECT photo,video,gif FROM event_preset WHERE id = '".$preset_id."' LIMIT 1");
			}
			else
			{
				$setting =  $mysqli->query("SELECT photo,video,gif FROM event_settings WHERE event_id = '".$eventId."' LIMIT 1");
			}
			if(	$setting->num_rows != 0 ){
				 $setting_data = $setting->fetch_assoc();
			}

			$openedEmails 		= 0;
			$totalEmailsSent 	= 0;
			$deliveredSms 		= 0;

			$startEndQuery = $mysqli->query("SELECT * FROM social_media WHERE event_id='".$eventId."' ORDER BY id ASC LIMIT 1");
			$eventStartDate = 0;
			while($Row = mysqli_fetch_array($startEndQuery))
			{
				$eventStartDate = $Row['createdon'];
			}
			$startEndQuery = $mysqli->query("SELECT * FROM social_media WHERE event_id='".$eventId."' ORDER BY id DESC LIMIT 1");
			$eventEndDate = 0;
			while($Row = mysqli_fetch_array($startEndQuery))
			{
				$eventEndDate = $Row['createdon'];
			}
			?>
			<div style="<?php if(@$_GET['mode'] != 'openstats'){ echo "height:50px;";} else{ echo "height:88px;"; } ?> margin-top:8px;">
				<div class="right dashboard-links">
					<?php
					if(@$_GET['mode'] != 'openstats')
					{
						?>
						<a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/stats/<?php echo $eventShortCode; ?>" target="Stats_<?php echo $eventShortCode; ?>">Share Analytics Link</a>
						<?php
					}
					?>
					<a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/admin/dashboard_pdf.php?event_id=<?php echo $eventShortCode; ?>" id="DownloadLink">Download as PDF</a>
				</div>
				<div class="eventHeader left">
					<?php
					if($_GET['mode'] == 'openstats')
					{
						?>
						<img src="<?php echo BASEURL; ?>/images/studiobooth-logo.png" style="width:242px; margin-bottom:10px;" alt="StudioBooth" />
						<?php
					}?>
					<div>
						<div><b>Client:</b> <?php echo $eventName; ?></div>
						<div style="margin-top: 5px;"><b>Event Dates:</b> <?php echo date("m/d/Y",$eventStartDate)." - ".date("m/d/Y",$eventEndDate); ?></div>
						<div style="margin-top: 5px;"><b>Last Updated:</b> <?php echo date("m/d/Y @ g:iA",$eventEndDate); ?></div>
					</div>
				</div>
				<div style="clear:right;"></div>
			</div>
			<div class="dashboard-subheading"><span>APP ANALYTICS</span></div>
			<?php
			$mediaQuery = $mysqli->query("SELECT COUNT(*) as c, mediatype FROM event_images WHERE event_id='".$eventId."' GROUP BY mediatype ORDER BY FIELD(mediatype,'Photo,Gif,Boomerang,Video')");
			$mediaHtml = "";
			$dataArr = array();
			$totalMediaCapture = 0;
			$mediaCountArr = array();
			$colorArr = array("photo" => "#375888","gif" => "#6a9452","video" => "#dea351");
			$mediaCountArr = array("photo" => "0","gif" => "0","video" => "0");
			if(@$setting_data['photo'] == 0)
			{
				$mediaCountArr['photo'] = "Disabled";
			}
			if(@$setting_data['gif'] == 0)
			{
				$mediaCountArr['gif'] = "Disabled";
			}
			if(@$setting_data['video'] == 0)
			{
				$mediaCountArr['video'] = "Disabled";
			}
			while($mediaRow = mysqli_fetch_array($mediaQuery))
			{
				$label = $mediaRow['mediatype'];
				if(strtolower(trim($label)) == 'boomerang')
				{
					$label = "Gif";
				}
				$totalMedia = $mediaRow['c'];
				$totalMediaCapture += $totalMedia;
				$mediaCountArr[strtolower($label)] += $totalMedia;
				$dataArr[$label]['count'] += $totalMedia;
				$dataArr[$label]['label'] = $label;
				$dataArr[$label]['color'] = $colorArr[strtolower($label)];
			}
			?>
			<div class="rowWrapper" style="height:248px;">
				<div>
					<div style="margin-bottom:5px;"><b>Total Images Captured:</b> <?php echo $totalMediaCapture; ?></div>
					<div style="margin-bottom:5px;"><b>Photos:</b> <?php echo $mediaCountArr['photo']; ?></div>
					<div style="margin-bottom:5px;"><b>GIFs:</b> <?php echo $mediaCountArr['gif']; ?></div>
					<div style="margin-bottom:5px;"><b>Videos:</b> <?php echo $mediaCountArr['video']; ?></div>
				</div>
				<div class="graphColumn" style="margin:-24px 0 0 -285px;">
					<div id="mediaPieChart"></div>
					<?php
					if($mediaCountArr['photo'] > 0 || $mediaCountArr['gif'] > 0 || $mediaCountArr['video'] > 0)
					{
						?>
					    <script>
							var pie = new d3pie("mediaPieChart", {
								"header": {
									"title": {
										"text": "",
										"fontSize": 24,
										"font": "open sans"
									},
									"subtitle": {
										"text": "",
										"color": "#999999",
										"fontSize": 12,
										"font": "open sans"
									},
									"titleSubtitlePadding": 9
								},
								"footer": {
									"text": "",
									"color": "#999999",
									"fontSize": 10,
									"font": "open sans",
									"location": "bottom-left"
								},
								"size": {
									"canvasHeight": 248,
									"canvasWidth": 380,
									"pieOuterRadius": "94%"
								},
								"data": {
									/*"sortOrder": "value-desc",*/
									"content": [
										<?php
										foreach ($dataArr as $key => $dataItemArr)
										{
											$count = $dataItemArr['count'];
											$label = $dataItemArr['label'];
											$color = $dataItemArr['color'];
											if(strtolower(trim($label)) == "gif")
											{
												$label = "GIF";
											}
											?>
											{ value: <?php echo $count; ?>, label: "<?php echo $label; ?>", color: "<?php echo $color; ?>" },
											<?php
										}
										?>
									]
								},
								"labels": {
									"outer": {
										"pieDistance": 10
									},
									"inner": {
										"format": "none",
										"hideWhenLessThanPercentage": 20
									},
									"mainLabel": {
										"color": "#000000",
										"font": "helvetica",
										"fontSize": 13
									},
									"percentage": {
										"color": "#ffffff",
										"decimalPlaces": 0
									},
									"value": {
										"color": "#adadad",
										"fontSize": 13
									},
									"lines": {
										"enabled": false
									},
									"truncation": {
										"enabled": false
									}
								},
								"tooltips": {
									"enabled": true,
									"type": "placeholder",
									"string": "{label}: {value}, {percentage}%"
								},
								"effects": {
									"load": {
										"effect": "none"
									},
									"pullOutSegmentOnClick": {
										"effect": "none",
										"speed": 400,
										"size": 8
									}
								},
								"misc": {
									"colors": {
										/*"background": "#FFF"*/
										"segmentStroke": ""
									},
									"gradient": {
										"enabled": false,
										"percentage": 100
									}
								}/*,
								"callbacks": { 
									"onload": function(){
										savePieChart("mediaPieChart","graphs-imgs/media_<?php echo $eventId; ?>.svg");
									}
								}*/
							});
					    </script>
					    <?php
					}?>
				</div>
			</div>
			<?php
			$mediaQuery = $mysqli->query("SELECT COUNT(*) as total_emails FROM social_media WHERE share_method='Email' AND event_id='".$eventId."'");
			while($mediaRow = mysqli_fetch_array($mediaQuery))
			{
				$totalEmailsSent = $mediaRow['total_emails'];
			}
			$mediaQuery = $mysqli->query("SELECT COUNT(*) as total_sms FROM social_media WHERE share_method='SMS' AND event_id='".$eventId."'");
			while($mediaRow = mysqli_fetch_array($mediaQuery))
			{
				$totalSmsSent = $mediaRow['total_sms'];
			}

			$Cond = "tag=".$eventId;
			$statDetail = json_decode(emailOutBoundOverview($Cond));
			$openedEmails = $statDetail->UniqueOpens;

			$mediaQuery = $mysqli->query("SELECT COUNT(*) as delivered_sms FROM social_media WHERE share_method='SMS' AND event_id='".$eventId."' AND twilio_status='delivered'");
			while($mediaRow = mysqli_fetch_array($mediaQuery))
			{
				$deliveredSms = $mediaRow['delivered_sms'];
			}
			?>
			<div style="height:30px;"></div>
			<div class="rowWrapper" style="height:300px;">
				<div>
					<div style="margin-bottom:40px;">
						<div style="margin-bottom:5px;"><b>Total Images Shared:</b> <?php echo ($totalEmailsSent + $totalSmsSent); ?></div>
						<div style="margin-bottom:5px;"><b>Email:</b> <?php echo $totalEmailsSent; ?></div>
						<div style="margin-bottom:5px;"><b>SMS:</b> <?php echo $totalSmsSent; ?></div>
					</div>
					<div style="margin-bottom:40px;">
						<div style="margin-bottom:5px;"><b>Total Images Opened:</b> <?php echo ($openedEmails + $deliveredSms); ?></div>
						<div style="margin-bottom:5px;"><b>Email:</b> <?php echo $openedEmails; ?></div>
						<div style="margin-bottom:5px;"><b>SMS:</b> <?php echo $deliveredSms; ?></div>
					</div>
				</div>
				<div class="graphColumn" style="margin:-13px 0 0 -225px;">
					<div class="left">
						<div id="barChart_<?php echo $eventId; ?>"></div>
						<script type="text/javascript">
							arrayOfData2 = new Array(
								[<?php echo $totalSmsSent; ?>,'SMS','#333333'],
								[<?php echo $totalEmailsSent; ?>,'Email','#666666']
							);
							$('#barChart_<?php echo $eventId; ?>').jqBarGraph({ animate: false, data: arrayOfData2, width: 100, height: 268 , footer: '<div style="margin:15px 0; display:block; font-weight:bold;">Shared</div>'
							});
						</script>
					</div>
					<div class="left" style="margin-left:30px;">
						<div id="barChart2_<?php echo $eventId; ?>"></div>
						<script type="text/javascript">
							arrayOfData2 = new Array(
								[<?php echo $deliveredSms; ?>,'SMS','#333333'],
								[<?php echo $openedEmails; ?>,'Email','#666666']
							);	
							$('#barChart2_<?php echo $eventId; ?>').jqBarGraph({ animate: false, data: arrayOfData2, width: 100, height: 268 , footer: '<div style="margin:15px 0; display:block; font-weight:bold;">Opened</div>'
							});
						</script>
					</div>
					<br style="clear:both;" />
				</div>
			</div>
			<div style="height:80px;"></div>
			<div class="dashboard-subheading"><span>GALLERY ANALYTICS</span></div>
			<?php
			$totalTraffic = 0;
			$uniqueTraffic = 0;
			$trafficQuery = $mysqli->query("SELECT COUNT(*) as total FROM visitor_log WHERE event_id='".$eventId."'");
			while($mediaRow = mysqli_fetch_array($trafficQuery))
			{
				$totalTraffic = $mediaRow['total'];
			}
			$trafficQuery = $mysqli->query("SELECT COUNT(*) as total FROM (SELECT * FROM visitor_log WHERE event_id='".$eventId."' GROUP BY ip) temp");
			while($mediaRow = mysqli_fetch_array($trafficQuery))
			{
				$uniqueTraffic = $mediaRow['total'];
			}
			$barGraphWidth = 268;
			?>
			<div class="rowWrapper" style="height:90px;">
				<div>
					<div style="margin-top: 30px;"><b>Total Traffic: </b><?php echo $totalTraffic; ?></div>
					<div style="margin-top: 10px;"><b>Total Unique Traffic: </b><?php echo $uniqueTraffic; ?></div>
				</div>
				<div class="graphColumn" style="margin:-5px 0 0 -285px;">
					<div class="horizontalBarWrapper">
						<div class="barWrapper bar1Wrapper">
							<div class="barLabel">Total Traffic</div>
							<div class="barGraph" style="width:<?php echo $barGraphWidth; ?>px; background-color: rgb(102, 102, 102);"></div>
							<div class="barValue"><?php echo $totalTraffic; ?></div>
						</div>
						<div class="barWrapper bar2Wrapper">
							<div class="barLabel">Unique Traffic</div>
							<div class="barGraph" style="width:<?php echo ($barGraphWidth / ($totalTraffic / $uniqueTraffic)); ?>px;  background-color: rgb(51, 51, 51);"></div>
							<div class="barValue"><?php echo $uniqueTraffic; ?></div>
						</div>
					</div>
				</div>
			</div>
			<?php
			$dataArr = array();

			$socialClicksQuery = $mysqli->query("SELECT COUNT(*) as c, page_name FROM clicks_log WHERE event_id='".$eventId."' GROUP BY page_name ORDER BY FIELD(LOWER(page_name),'facebook,twitter,instagram,tumblr')");

			$totalSharing = 0;
			$socialShareDataArr = array();
			$i = 0;
			$colorArr = array("facebook" => "#375888","twitter" => "#6a9452","instagram" => "#dea351","tumblr" => "#ae3936");
			$socialShareDataArr = array("facebook" => "0","twitter" => "0","instagram" => "0","tumblr" => "0");
			while($mediaRow = mysqli_fetch_array($socialClicksQuery))
			{
				$sharingNo 		= trim($mediaRow['c']);
				$socialSiteName = trim($mediaRow['page_name']);
				$totalSharing += $sharingNo;

				$dataArr[$i]['count'] = $sharingNo;
				$dataArr[$i]['label'] = $socialSiteName;
				$dataArr[$i]['color'] = $colorArr[strtolower($socialSiteName)];

				$socialShareDataArr[strtolower($socialSiteName)] = $sharingNo;

				$i++;
			}
			?>
			<div style="height:75px;"></div>
			<div class="rowWrapper" style="height:248px;">
				<div>
					<div style="margin-bottom:5px;"><b>Total Social Shares:</b> <?php echo $totalSharing; ?></div>
					<div style="margin-bottom:5px;"><b>Facebook:</b> <?php echo $socialShareDataArr['facebook']; ?></div>
					<div style="margin-bottom:5px;"><b>Twitter:</b> <?php echo $socialShareDataArr['twitter']; ?></div>
					<div style="margin-bottom:5px;"><b>Instagram:</b> <?php echo $socialShareDataArr['instagram']; ?></div>
					<div style="margin-bottom:5px;"><b>Tumblr:</b> <?php echo $socialShareDataArr['tumblr']; ?></div>
				</div>
				<div class="graphColumn" style="margin:-24px 0 0 -285px;">
					<div id="socialClicksPieChart"></div>
					<?php
					if($socialShareDataArr['facebook'] > 0 || $socialShareDataArr['twitter'] > 0 || $socialShareDataArr['instagram'] > 0 || $socialShareDataArr['tumblr'] > 0)
					{
						?>
					    <script>
							var pie = new d3pie("socialClicksPieChart", {
								"header": {
									"title": {
										"text": "",
										"fontSize": 24,
										"font": "open sans"
									},
									"subtitle": {
										"text": "",
										"color": "#999999",
										"fontSize": 12,
										"font": "open sans"
									},
									"titleSubtitlePadding": 9
								},
								"footer": {
									"text": "",
									"color": "#999999",
									"fontSize": 10,
									"font": "open sans",
									"location": "bottom-left"
								},
								"size": {
									"canvasHeight": 248,
									"canvasWidth": 380,
									"pieOuterRadius": "94%"
								},
								"data": {
									/*"sortOrder": "value-desc",*/
									"content": [
										<?php
										foreach ($dataArr as $key => $dataItemArr)
										{
											$count = $dataItemArr['count'];
											$label = $dataItemArr['label'];
											$color = $dataItemArr['color'];
											?>
											{ value: <?php echo $count; ?>, label: "<?php echo $label; ?>", color: "<?php echo $color; ?>" },
											<?php
										}
										?>
									]
								},
								"labels": {
									"outer": {
										"pieDistance": 10
									},
									"inner": {
										"format": "none",
										"hideWhenLessThanPercentage": 20
									},
									"mainLabel": {
										"color": "#000000",
										"font": "helvetica",
										"fontSize": 13
									},
									"percentage": {
										"color": "#ffffff",
										"decimalPlaces": 0
									},
									"value": {
										"color": "#adadad",
										"fontSize": 13
									},
									"lines": {
										"enabled": false
									},
									"truncation": {
										"enabled": false
									}
								},
								"tooltips": {
									"enabled": true,
									"type": "placeholder",
									"string": "{label}: {value}, {percentage}%"
								},
								"effects": {
									"load": {
										"effect": "none"
									},
									"pullOutSegmentOnClick": {
										"effect": "none",
										"speed": 400,
										"size": 8
									}
								},
								"misc": {
									"colors": {
										/*"background": "#FFF"*/
										"segmentStroke": ""
									},
									"gradient": {
										"enabled": false,
										"percentage": 100
									}
								}/*,
								"callbacks": { 
									"onload": function(){
										savePieChart("socialClicksPieChart","graphs-imgs/social_media_<?php echo $eventId; ?>.svg");
									}
								}*/
							});
					    </script>
					    <?php
					}?>
				</div>
			</div>

			<div style="margin-top:30px; margin-bottom: 10px;"><b>Top 5 shared images</b></div>
			<?php
			$topMediaQuery = $mysqli->query("SELECT COUNT(*) as c, media_id FROM clicks_log WHERE event_id='".$eventId."' GROUP BY media_id ORDER BY c DESC LIMIT 5");
			while($topMediaRow = mysqli_fetch_array($topMediaQuery))
			{
				$mediaId 	= $topMediaRow['media_id'];

				$mediaQuery = $mysqli->query("SELECT * FROM event_images WHERE event_id='".$eventId."' AND id='".$mediaId."'");
				while($mediaRow = mysqli_fetch_array($mediaQuery))
				{
					$shortCode 	= shorty($mediaId+10000);
					$wrapperUrl = "http://".$_SERVER['SERVER_NAME']."/".$slug."/".$shortCode;

					$type = $mediaRow['media_type'];
					$onS3 = $mediaRow['on_s3'];
					$media=	BASEURL.'/../media/'.$mediaRow['event_id'].'/'.$mediaRow['images'];

					if($onS3)
					{
						$media 	= S3BUCKET.'media/'.$mediaRow['event_id'].'/'.$mediaRow['images'];
					}
					if(trim($mediaRow['media_type']) == "video/mp4")
					{
						$videoThumb = BASEURL.'/../media/'.$mediaRow['event_id'].'/video-thumbs/'.str_ireplace(".mp4", ".jpg", $mediaRow['images']);
						if($onS3)
						{
							$videoThumb = S3BUCKET.'media/'.$mediaRow['event_id'].'/video-thumbs/'.str_ireplace(".mp4", ".jpg", $mediaRow['images']);
						}
						?>
						<!-- <video width="280" height="200" controls>
						  <source src="<?=$media;?>" type="video/mp4">
							  Your browser does not support the video tag.
						</video> -->
						<a class="video" href="<?php echo $wrapperUrl; ?>" target="<?php echo $shortCode; ?>" style="margin:5px 5px 5px 0;"><img class='media' height='240' border="0" src='<?php echo $videoThumb;?>' /></a>
						<?php
					}
					else
					{
						?>
						<a class="image" href="<?php echo $wrapperUrl; ?>" target="<?php echo $shortCode; ?>" style="margin:5px 5px 5px 0;"><img class='media' height='240' border="0" src='<?=$media;?>' /></a>
						<?php
					}
				}
			}
		}
		?>
	</div>
	<script>
		<?php
		if($mediaCountArr['photo'] < 1 && $mediaCountArr['gif'] < 1 && $mediaCountArr['video'] < 1 && $socialShareDataArr['facebook'] < 1 && $socialShareDataArr['twitter'] < 1 && $socialShareDataArr['instagram'] < 1 && $socialShareDataArr['tumblr'] < 1)
		{
			?>
			$(document).ready(function(){
				$("#DownloadLink").show();
			});
			<?php
		}
		?>
	</script>
	<?php 
	if(@$_GET['mode'] != 'openstats')
	{
		include_once 'footer.php';
	}
	else
	{
		?>
		</div>
		<?php
	}?>
</body>
</html>