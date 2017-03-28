<?php
include_once("dbconfig.php");
include_once 'layouts/common.php';



include_once 'function.php';

if(trim($_POST["Mode"]) == "updateEventStatus" && trim($_POST["event_id"]) > 0)
{
	$event_id 	= trim($_POST["event_id"]);
	$status 	= trim($_POST["status"]);

	if($status == 'true')
	{
		$status = "1";
	}
	else
	{
		$status = "0";
	}
	$mysqli->query("UPDATE events SET status='".$status."' WHERE TRIM(id) = '".$event_id."'");
}
if(trim($_POST["Mode"]) == "SaveImage" && trim($_POST["imgData"]) != "" && trim($_POST["imgName"]) != "")
{
	$SVGImage = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg width="380" height="248" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://web.resource.org/cc/">'.rawurldecode(trim($_POST["imgData"])).'</svg>';

	file_put_contents(trim($_POST["imgName"]),$SVGImage);
	die;
	//@unlink(trim($_POST["imgName"]));
	$imgData    = base64_decode(str_replace(array("data:image/png;base64,","data:image/png;base64"), "", rawurldecode($_POST['imgData'])));

	$imagick = new Imagick();
	$imagick->readImageBlob($imgData);
	$imagick = $imagick->flattenImages();
	$imagick->setImageFormat('PNG');
	$imagick->writeImage(trim($_POST["imgName"]));

  /***** GD Option
  $imgHandle  = imagecreatefromstring($imgData);
  //imagefilter( $imgHandle, IMG_FILTER_GRAYSCALE );
  header('Content-type:image/jpeg'); //tell the browser what to expect
  imagejpeg($imgHandle, $croppedImagePath); //output the image
  imagedestroy($imgHandle); //clean up
  ******/
}
if(trim($_POST['mode']) == 'SendForgotPasswordLink' && trim($_POST['email']) != "")
{
	include_once("../email/settings.php");
	include_once("../email/postmark/Mail.php");
	$email = rawurldecode(trim($_POST["email"]));

	$result = $mysqli->query("SELECT * FROM users WHERE email='".$email."'");
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_assoc())
		{
			$email		= trim($row['email']);
			$password 	= trim($row['password']);
			$FullName 	= trim($row['full_name']);
			$UserName 	= trim($row['username']);
			$UserID 	= trim($row['id']);

			ob_start();
			?>
			<div style="width:580px; background-color:#d2d2d2; padding:10px;">
				<div style="padding:20px 15px; background-color:#fff; word-wrap:break-word;">
					<?php
					$ServerPath = trim($_SERVER['SERVER_NAME']);

					if(strtolower($ServerPath) == 'localhost')
					{
						$ServerPath = "http://localhost/studionew";
					}
					else
					{
						$ServerPath .= "/studiobooth";
					}
					?>
					<img src="<?php echo $ServerPath; ?>/images/studiobooth.png" alt="StudioBooth" title="StudioBooth" />
					<div style="height:1px; border-bottom:1px solid #d2d2d2; margin-top:5px;">&nbsp;</div>
					<br /><br />
					Hi <?php echo $FullName; ?>,<br /><br />
					There was recently a request to change the password for your account.<br>
					If you requested this password change, please reset your password here:<br /><br />
					<div style="text-align:center;">
						<a href="<?php echo $ServerPath; ?>/forgot-password.php?t=<?php echo $password; ?>&it=<?php echo base64_encode($UserID."-::-".time());?>" style="color: #fff; background-color:#E64A45; text-decoration: none; border-radius: 3px; padding: 5px 19px 7px 19px; font-size: 16px; white-space: nowrap; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; letter-spacing:1px;">RESET PASSWORD</a>
					</div>
					<br /><br>
					If you did not make this request, you can ignore this message and your password will remain the same.

					<br /><br />
					Regards,<br />
					StudioBooth Team
				</div>
			</div>
			<?php
			$Message = ob_get_contents();
			ob_get_clean();
			ob_get_flush();

			$MailSent = Mail::compose(POSTMARKAPP_API_KEY)
					->from('social@thestudiobooth.com', "StudioBooth")
					->addTo($email)
					->subject("Hi ".$FullName.', Reset password link')
					->messageHtml($Message)
					/*->addAttachment($file)
					->tag($email->{'from'})*/
					->send();

			if($MailSent)
			{
				echo "Success-::-Mail sent successfully.";
			}
			else
			{
				echo "Error-::-Unable to send mail. Please try again.";
			}
		}
	}
	else
	{
		echo "Error-::-Sorry, '".$email."' email-id is not registered with us.";
	}
}
?>