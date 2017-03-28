<?php
/* COMMON FUNCTIONS */

function resize_image($source,$dest,$resize_height,$resize_width)
	{
		list($width, $height) = getimagesize($source);		

		$image_type=exif_imagetype($source);
		
		if(($width>$resize_width)||($height>$resize_height))
		{
			if($width>$height)
			{
				$new_width=$resize_width;

				$percetage=($new_width*100)/$width;	
				$percetage=sprintf("%0.0f",$percetage);

				$new_height=($height*$percetage)/100;
				$new_height=sprintf("%0.0f",$new_height);
				
			}	
			else
			{
			
				$new_height=$resize_height;

				$percetage=($new_height*100)/$height;	
				$percetage=sprintf("%0.0f",$percetage);

				$new_width=($width*$percetage)/100;
				$new_width=sprintf("%0.0f",$new_width);
			}
			
			$image_p = imagecreatetruecolor($new_width, $new_height);

			if($image_type==1)
			{			
				$image = imagecreatefromgif($source);
			}
			elseif($image_type==3)
			{			
				$image = imagecreatefrompng($source);
			}
			elseif($image_type==6)
			{			
				$image = imagecreatefromwbmp($source);
			}
			else
			{
				$image = imagecreatefromjpeg($source);
			}	
						
			if(!$image) 
			{
				copy($source,$dest);
			}
			else
			{
				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagejpeg($image_p,$dest, 90);
			}
		}
		else
		{
			copy($source,$dest);
		}								
	}


function upload_imagex($variable,$thumb,$upload_dir,$new_image_name,$upload_valid,$display,$max_height,$max_width,$allowed,$max_size,$resize=0)
	{

	$error_msg="";

	$filename=$_FILES[$variable]['name'];


	$filesize=$_FILES[$variable]['size'];
	$file=$thumb;
	$filetype=$_FILES[$variable]['type'];	
	
	$temp_name=explode(".",$filename);

	//CODE FOR CHECKING AND CREATING THE FOLDER, WHERE MEMBER IMAGE WOULD BE STORE...

	$dir_name=$upload_dir;

	if(!is_dir($dir_name))
	{
		$oldmask = umask(0);
		@mkdir($dir_name,0777,true);
		umask($oldmask);
	}

	$error_msg="";
	$upload_dir=$dir_name;
	$max_size=$max_size*1024;

	if(!empty($filename))
	{
		$valid=0;

		if($upload_valid=="image")
		{
			if(eregi("image",$filetype)||($temp_name[1]=="tif")||($temp_name[1]=="tiff"))
		     	{
				$valid=1;
			}
		}


		if($upload_valid=="flash")
		{
			if($temp_name[1]=="swf")
		     	{
				$valid=1;
			}
		}
		
		if($upload_valid=="document")
		{
			if($filetype=="application/pdf" || $filetype=="application/msword" || $filetype=="application/zip" || $filetype="application/octet-stream")
		    {
				$valid=1;
			}
		}
	
		list($image_width, $image_height) = getimagesize($thumb);

		if($allowed!=0)
		{
			if($allowed==1)
			{
				if(($image_width!=$max_width)||($image_height!=$max_height))
				{
					$valid=0;
					$upload_valid="";
					$error_msg.="<B>$display Dimension must be  equal to ($max_width X $max_height). </B><BR>";

				}
			}
			elseif($allowed==2)
			{
				if(($image_width>$max_width)||($image_height>$max_height))
				{
					$valid=0;
					$upload_valid="";
					$error_msg.="<B>$display Dimension must be Less then or equal to ($max_width X $max_height). </B><BR>";
				}
			}
			elseif($allowed==3)
			{
				if(($image_width<$max_width)||($image_height<$max_height))
				{
					$valid=0;
					$upload_valid="";
					$error_msg.="<B>$display Dimension must be Greater then or equal to ($max_width X $max_height). </B><BR>";
				}
			}
		}


		if($valid==1)
	     	{
	     		if($filesize<=$max_size)
		     	{
				//print "$upload_dir/$new_image_name";				
				copy($file,"$upload_dir/$new_image_name");
				if($resize==1)
				{
					$source_image=$upload_dir."/".$new_image_name;
					$dest_image=$upload_dir."/".$new_image_name;
					$height=200;	
					$width=200;
					resize_image($source_image,$dest_image,$height,$width);
				}
			}
			else
			{
				$display_size=$max_size/1024;
				$error_msg.="<B>$display cannot be uploaded. The maximum file size is $display_size KB. Please either reduce its file size, or choose another image.</B><BR>";
			}
		}
		else
		{
			if($upload_valid=="image")
			{
				$error_msg.="<B>$display you are trying to upload has the wrong format. Please go back and choose another file.</B><BR>";
			}
			elseif($upload_valid=="flash")
			{
				$error_msg.="<B>$display you are trying to upload has the wrong format. Please go back and choose another file.</B><BR>";
			}
			elseif($upload_valid=="document")
			{
				$error_msg.="<B>$display you are trying to upload has the wrong format. Please go back and choose another file.</B><BR>";
			}

		}
	}
	else
	{
		$error_msg="<B>Please specify $display to be uploaded.</B><BR>";
	}
	return 
	;
}


function sec_session_start() {
    $session_name = 'sec_session_id';
    $secure = SECURE;
    $httponly = true;
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
		flash( 'msg', 'Dafe session could not be started', 'error' );
        header('Location: '. BASEURL .'/index.php');
       exit();
    }
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    session_name($session_name);
    session_start();
    session_regenerate_id();
}
function login($email, $password, $mysqli) {

    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt, type, full_name,can_create_events FROM users WHERE TRIM(email) COLLATE latin1_bin = ? LIMIT 1")) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $username, $db_password, $salt, $type,$full_name,$can_create_events);
        $stmt->fetch();
        $password = hash('sha512', $password . $salt);
	
        if ($stmt->num_rows == 1) {
            if (checkbrute($user_id, $mysqli) == true) {
                return false;
            } else {
                if ($db_password == $password) {
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);
                    $_SESSION['username'] = $username;
					$_SESSION['type']=$type;
					$_SESSION['full_name']=$full_name;
					$_SESSION['can_create_events']=$can_create_events;
					
                    $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                    return true;
                } else {
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time) VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            return false;
        }
    }
}
function checkbrute($user_id, $mysqli) {
    $now = time();
    $valid_attempts = $now - (2 * 60 * 60); 
    if ($stmt = $mysqli->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id); 
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}
function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], 
                        $_SESSION['username'], 
						
                        $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
		
		
       
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ? LIMIT 1")) {
			$stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }
    } else {
        // Not logged in 
        return false;
    }
}

function check_exists($field, $val, $table) {
	$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
    $query = "SELECT COUNT(*) AS num_rows FROM $table WHERE $field = $val";
    if ($stmt = $mysqli->query($query)) {
        return $stmt->num_rows;
    }
    return 0;
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    } 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url; 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }

    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        return '';
    } else {
        return $url;
    }
}
function flash( $name = '', $message = '', $class = 'success fadeout-message', $url = '' ){
    //We can only do something if the name isn't empty
    if( !empty( $name ) )
    {
        //No message, create it
        if( !empty( $message ) && empty( $_SESSION[$name] ) )
        {
            if( !empty( $_SESSION[$name] ) )
            {
                unset( $_SESSION[$name] );
            }
            if( !empty( $_SESSION[$name.'_class'] ) )
            {
                unset( $_SESSION[$name.'_class'] );
            }
 
            $_SESSION[$name] = $message;
            $_SESSION[$name.'_class'] = $class;
        }
        //Message exists, display it
        elseif( !empty( $_SESSION[$name] ) && empty( $message ) )
        {
            $class = !empty( $_SESSION[$name.'_class'] ) ? $_SESSION[$name.'_class'] : 'success';
            echo '<div class="'.$class.'" id="msg-flash">'.$_SESSION[$name].'</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name.'_class']);
        }
    }
	if( !empty( $url ) || $url != '' )
    {
		header('Location: '.$url);
		exit();
	}
}

function upload_image($img, $folder = ''){
	//$date = date('YmdHis');
	//$pre = $date."/";
	if($folder != ''){
		$udir = BASEPATH.'/'.$folder;
	}else{
		$udir = BASEPATH.'/images';
	}
	if (!file_exists($udir)) {
		$oldmask = umask(0);
		@mkdir($udir, 0777, true);
		@mkdir($udir."/thumb", 0777, true);
		umask($oldmask);
	}
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$videoExt = array("mp4", "mpeg");
	$extension = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
	$temp = explode(".", $img["name"]);
	
	
	if (in_array($extension, $allowedExts) ){
		if ($img["error"] > 0){
			$errMsg = uploadFileErrorType($img["error"]);
			return "error-::-Media ".$errMsg.". Please try another one.";
		}else{
			list($width, $height) = getimagesize($img["tmp_name"]);
			// check if the file is really an image
			if ($width == null || $height == null || $width < 1 || $height < 1) {
				return "error-::-Corrupted media. Please try another one.";
			}
						
			move_uploaded_file($img["tmp_name"], $udir .'/'. $img["name"]);

			/*$image = new uploader($temp);
			
			$image->destDir = $udir;
			//$image->pre = $pre;
			$image->upload($img);
			
			$image->resizeDir = $udir."/thumb/";
			echo $result = $image->resize('',75,75, true);*/
			$result = $img["name"];
			$oldmask = umask(0);
			@mkdir($udir."/thumb/",0777,true);
			umask($oldmask);
			resize_image($udir."/".$result,$udir."/thumb/".$result,75,75);
			return $result;
		}
	}else if(in_array($extension, $videoExt)){
		if ($img["error"] > 0){
			$errMsg = uploadFileErrorType($img["error"]);
			return "error-::-Media ".$errMsg.". Please try another one.";			
		}else{			
			move_uploaded_file($img["tmp_name"], $udir .'/'. $img["name"]);
			return $img["name"];
			
		}
	}else{
		return "error-::-Invalid media file with '".$extension."' extension";
	}
}

function upload_media($img, $folder = ''){
	//$date = date('YmdHis');
	//$pre = $date."/";
	if($folder != ''){
		$udir = BASEPATH.'/'.$folder;
	}else{
		$udir = BASEPATH.'/images';
	}
	if (!file_exists($udir)) {
		$oldmask = umask(0);
		@mkdir($udir, 0777, true);
		@mkdir($udir."/thumb", 0777, true);
		umask($oldmask);
	}
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$videoExt = array("mp4");
	$extension = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
	$temp = explode(".", $img["name"]);
	
	$fileUniqueName =  time().".".$extension;

	if (in_array($extension, $allowedExts) ){
		if ($img["error"] > 0){
			$errMsg = uploadFileErrorType($img["error"]);
			return "error-::-Media ".$errMsg.". Please try another one.";
		}else{
			list($width, $height) = getimagesize($img["tmp_name"]);
			// check if the file is really an image
			if ($width == null || $height == null || $width < 1 || $height < 1) {
				return "error-::-Corrupted media. Please try another one.";
			}

			/*move_uploaded_file($img["tmp_name"], $udir .'/'. $img["name"]);*/
			move_uploaded_file($img["tmp_name"], $udir .'/'.$fileUniqueName);

			if($extension == "png")
			{
				/*$jpgImg = str_ireplace(".png", ".jpg", strtolower(trim($img["name"])));*/
				$jpgImg = str_ireplace(".png", ".jpg", strtolower(trim($fileUniqueName)));
				/*convertPngToJpg($udir.'/'.$img["name"], $width, $height, $udir.'/'.$jpgImg);*/
				convertPngToJpg($udir.'/'.$fileUniqueName, $width, $height, $udir.'/'.$jpgImg);
				/*@unlink($udir.'/'.$img["name"]);*/
				@unlink($udir.'/'.$fileUniqueName);
				/*$img["name"] = $jpgImg;*/
				$fileUniqueName = $jpgImg;
			}

			$oldmask = umask(0);
			@mkdir($udir."/org/",0777,true);
			umask($oldmask);
			/*@copy($udir .'/'. $img["name"], $udir .'/org/'. $img["name"]);
			$result = $img["name"];*/
			@copy($udir .'/'. $fileUniqueName, $udir .'/org/'. $fileUniqueName);
			$result = $fileUniqueName;
			return $result;
		}
	}else if(in_array($extension, $videoExt)){
		if ($img["error"] > 0){
			$errMsg = uploadFileErrorType($img["error"]);
			return "error-::-Media ".$errMsg.". Please try another one.";
			
		}else{			
			/*move_uploaded_file($img["tmp_name"], $udir .'/'. $img["name"]);*/
			$targetPath = $udir .'/'. $fileUniqueName;

			move_uploaded_file($img["tmp_name"], $targetPath);

			$oldmask = umask(0);
			@mkdir($udir."/org/",0777,true);
			umask($oldmask);
			/*@copy($udir .'/'. $img["name"], $udir .'/org/'. $img["name"]);
			return $img["name"];*/
			@copy($targetPath, $udir .'/org/'. $fileUniqueName);

			$ffmpeg 		= FFMPEGPATH;
			$videoFileNew	= str_ireplace(".mp4","_test.mp4",$targetPath);

			exec("$ffmpeg -i '$targetPath' -strict -2 '$videoFileNew'");
			@unlink($targetPath);
			@rename($videoFileNew, $targetPath);
			return $fileUniqueName;			
		}
	}else{
		return "error-::-Invalid media file with '".$extension."' extension";
	}
}
function delete_image($img , $folder = ''){
	if($img !=""){
		if($folder != ''){
			$udir = BASEPATH.'/images/'.$folder;
		}else{
			$udir = BASEPATH.'/images';
		}
		if (file_exists($udir."/".$img)){
			unlink($udir."/".$img);
		}
	}
}

$default_status_order=array("Select","Active","InActive");
$default_status_order_value=array("","0","1");
$curPage = esc_url($_SERVER['PHP_SELF']);
function loginCheck()
{
	global $mysqli;
	if(login_check($mysqli) != true)
	{
		//flash('msg', 'You are not authorized to access this page, please login', 'success', BASEURL .'/index.php');-
		header('Location: '.BASEURL .'/index.php');
		die;
	}
	/*$allowed = array('wizard_create_video_swf_file.php','dashboard_pdf.php','dashboard.php','sms_status.php','wizard_create_video_thumbs.php','wizard_update_client_id.php','wizard_create_bitly_links.php','index.php','login.php','gallery.php','exit.php','register.php', 'API','dbbyajax.php','forgot-password.php','first-login.php');
	$curPage = esc_url($_SERVER['PHP_SELF']);
	$IsAllowed = 'false';
	foreach($allowed as $elm){
		$pos = strpos($curPage, $elm, 1);
		if($pos){
			$IsAllowed = 'true';
		}
	}
	if(login_check($mysqli) != true){
		$logged = false;
		if($IsAllowed == 'false'){
			//flash('msg', 'You are not authorized to access this page, please login', 'success', BASEURL .'/index.php');
			header('Location: '.BASEURL .'/index.php');
			die;
		}
	}else{
		$logged = true;
	}*/
}
function GetCleanNameForFile($Str)
{
	$Str = frenchChars(ucwords(strtolower(trim($Str))));
	$Str = str_replace(array("~","`","!","@","#","$","%","^","*",'"',"'",":",";",".",">","<",",","?","|","\\","‘","’",'“','”',","),"",$Str);
	$Str = str_replace(array("{","[","("),"",$Str);
	$Str = str_replace(array("}","]",")"),"",$Str);
	$Str = str_replace(array("&"),"-and-",$Str);
	$Str = str_replace(array("/"),"-or-",$Str);
	$Str = str_replace(array(" "),"-",$Str);
	$Str = str_replace(array("--"),"-",$Str);
	$Str = str_replace(array("-"),"_",$Str);
	return $Str;
}
function SerialNo($RowNo,$records_per_page,$CurrPage = 1)
{
	if($CurrPage < 1)
	{
		$CurrPage = 1;
	}
	return (($records_per_page* ($CurrPage-1))+$RowNo);
}
function TblRowBgColor()
{
	global $TblRowClass;
	if($TblRowClass == "" || $TblRowClass == "tblrow2")
	{
		$TblRowClass = "tblrow1";
	}
	else
	{
		$TblRowClass = "tblrow2";
	}
	return $TblRowClass;
}
/*if(get_magic_quotes_gpc())
{*/
  function undo_magic_quotes_array($array)
  {
    return is_array($array) ? array_map('undo_magic_quotes_array', $array) : str_replace("\\'", "'", str_replace("\\\"", "\"", str_replace("\\\\", "\\", str_replace("\\\x00", "\x00", $array))));
  }

  $_GET = undo_magic_quotes_array($_GET);
  $_POST = undo_magic_quotes_array($_POST);
  $_COOKIE = undo_magic_quotes_array($_COOKIE);
  $_FILES = undo_magic_quotes_array($_FILES);
  $_REQUEST = undo_magic_quotes_array($_REQUEST);
/*}*/

function WelcomeMail($UserID)
{
	global $mysqli;
	include_once("../email/settings.php");
	include_once("../email/postmark/Mail.php");

	$result = $mysqli->query("SELECT * FROM users WHERE id='".$UserID."'");
	while($row = $result->fetch_assoc())
	{
		$email			= trim($row['email']);
		$password 		= trim($row['password']);
		$orgPassword 	= trim($row['org_password']);
		$FullName 		= trim($row['full_name']);
		$UserName 		= trim($row['username']);

		ob_start();
		?>
		<div style="width:580px; background-color:#d2d2d2; padding:10px;">
			<div style="padding:20px 15px; background-color:#fff; word-wrap:break-word;">
				<?php
				$ServerPath = trim(BASEURL);
				?>
				<img src="<?php echo $ServerPath; ?>/images/studiobooth.png" alt="StudioBooth" title="StudioBooth" />
				<div style="height:1px; border-bottom:1px solid #d2d2d2; margin-top:5px;">&nbsp;</div>
				<br /><br />
				HI <?php echo strtoupper($FullName); ?>,<br /><br />
				YOUR ACCOUNT HAS BEEN CREATED FOR THE STUDIOBOOTH ADMIN PANEL. PLEASE LOGIN BELOW.
				<br /><br />
				Your password is: <?php echo $orgPassword; ?>
				<br /><br />
				<div style="text-align:center;">
					<!-- <a href="<?php echo $ServerPath; ?>/first-login.php?t=<?php echo $password; ?>&it=<?php echo base64_encode($UserID."-::-".time());?>" style="color: #fff; background-color:#E64A45; text-decoration: none; border-radius: 3px; padding: 5px 19px 7px 19px; font-size: 16px; white-space: nowrap; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; letter-spacing:1px;">LOGIN</a> -->
					<a href="<?php echo $ServerPath; ?>/" style="color: #fff; background-color:#E64A45; text-decoration: none; border-radius: 3px; padding: 5px 19px 7px 19px; font-size: 16px; white-space: nowrap; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; letter-spacing:1px;">LOGIN</a>
				</div>

				<br /><br />
				Thanks! <br />
				The StudioBooth Team
			</div>
		</div>
		<?php
		$Message = ob_get_contents();
		ob_get_clean();
		ob_get_flush();

		$MailSent = Mail::compose(POSTMARKAPP_API_KEY)
				->from('social@thestudiobooth.com', "StudioBooth")
				->addTo($email)
				->subject('WELCOME TO THE STUDIOBOOTH ADMIN PANEL')
				->messageHtml($Message)
				/*->addAttachment($file)
				->tag($email->{'from'})*/
				->send();
	}
}
function shorty($num, $decode = false) {
	
	//borred from Dropmark
	//based on code from Flickr (http://www.flickr.com/groups/api/discuss/72157616713786392/)
	$alphabet = "bQH1yF0Sw2VdKJsLNvzjBWTm5f3Cr4ctpqDxG6Yk98Rn7ghXPMZ"; //A-Za-z0-9 (vowels removed, l removed), randomized to make it harder to guess

	if ($decode == false) {
		$base_count = strlen($alphabet);
		$encoded = '';
		while ($num >= $base_count) {
			$div = $num/$base_count;
			$mod = ($num-($base_count*intval($div)));
			$encoded = $alphabet[$mod] . $encoded;
			$num = intval($div);
		}
		if ($num) $encoded = $alphabet[$num] . $encoded;
		return $encoded;
	} else {
		$decoded = 0;
		$multi = 1;
		while (strlen($num) > 0) {
			$digit = $num[strlen($num)-1];
			$decoded += $multi * strpos($alphabet, $digit);
			$multi = $multi * strlen($alphabet);
			$num = substr($num, 0, -1);
		}	
		return $decoded;
	}
}
function htmlize($str) {
  return htmlentities($str,ENT_QUOTES,'UTF-8');
}
function gif_to_video($gifImage,$videoFile,$frame_rate,$overlayImg,$camera)
{
	$ffmpeg 		= FFMPEGPATH;
	$time 			= time();
	$gifFileName 	= basename($gifImage);
	$gifFilePath 	= str_replace($gifFileName,"",$gifImage);
	$tempDir 		= $gifFilePath."temp/".$time."/";

	$oldmask = umask(0);
	@mkdir($tempDir,0777,true);
	umask($oldmask);

	//echo "Converting GIF to mp4.<br />";
	//echo "Fetch GIF frames and convert into 'jpg' images.<br />";
	exec("$ffmpeg -y -i '$gifImage' '$tempDir"."image%d.jpg'");

	$decimalPosition = strpos($frame_rate, ".");

	if(trim($frame_rate) == "" || trim($frame_rate) == null)
	{
		$frame_rate = "0.3";
	}

	//echo "Creating video from 'jpg' images.<br />";
	/*exec("$ffmpeg -y -loop 1 -framerate 1/".$frame_rate." -i '$tempDir"."image%d.jpg' -c:v libx264 -vf 'fps=10,format=yuv420p' -t 6 '$videoFile'");*/

	exec("$ffmpeg -y -framerate 1/".$frame_rate." -i '$tempDir"."image%d.jpg' -c:v libx264 -vf 'format=yuv420p' '$videoFile'");

	/***** Create GIF Image ******/
	if(!is_dir($overlayImg))
	{
		require_once(GifCreatorPATH);
		$files = glob($tempDir.'*.[Jj][Pp][Gg]');
		foreach ($files as $key => $extractedImage)
		{
			if(strtolower(trim($camera)) == 'front')
			{
				/* this function should come before image overlay. */
				mirror_media($extractedImage,'image');
			}
			merge_overlay($extractedImage,$overlayImg,$extractedImage,'image');
		}

		// Create an array containing file paths, resource var (initialized with imagecreatefromXXX), 
		// image URLs or even binary code from image files.
		// All sorted in order to appear.
		$frames = $files;

		// Create an array containing the duration (in millisecond) of each frames (in order too)
		$frame_rate2 = ($frame_rate * 100);
		$durations = array($frame_rate2, $frame_rate2, $frame_rate2, $frame_rate2);

		// Initialize and create the GIF !
		$gc = new GifCreator();
		$gc->create($frames, $durations, 0);

		$gifBinary = $gc->getGif();

		/*** Print on browser ***/
			/*header('Content-type: image/gif');
			header('Content-Disposition: filename="butterfly.gif"');
			echo $gifBinary;
			exit;*/
		/*** Print on browser ***/
		$gifImage = str_ireplace("/org/", "/", $gifImage);
		file_put_contents($gifImage, $gifBinary);
	}
	/***** Create GIF Image ******/

	delete_directory($tempDir);
	return true;
}
function gif_to_video_for_instagram($gifImage,$videoFile,$videoWidth,$videoHeight)
{
	$ffmpeg 		= FFMPEGPATH;
	$time 			= time();
	$gifFileName 	= basename($gifImage);
	$gifFilePath 	= str_replace($gifFileName,"",$gifImage);
	$tempDir 		= $gifFilePath."temp/".$time."/";
	$videoFileNew	= str_ireplace(".mp4","_test.mp4",$videoFile);

	$oldmask = umask(0);
	@mkdir($tempDir,0777,true);
	umask($oldmask);

	//echo "Converting GIF to mp4.<br />";
	//echo "Fetch GIF frames and convert into 'jpg' images.<br />";
	exec("$ffmpeg -y -i '$gifImage' '$tempDir"."image%d.jpg'");

	//echo "Creating video from 'jpg' images.<br />";
	exec("$ffmpeg -y -loop 1 -framerate 1/0.3 -i '$tempDir"."image%d.jpg' -c:v libx264 -vf 'format=yuv420p' -t 6 '$videoFile'");

	//echo "Scaling video to exact size. <br />";
	/*exec("$ffmpeg -y -i '$videoFile' -strict -2 -filter:v 'scale=iw*min($videoWidth/iw\,$videoHeight/ih):ih*min($videoWidth/iw\,$videoHeight/ih), pad=$videoWidth:$videoHeight:($videoWidth-iw*min($videoWidth/iw\,$videoHeight/ih))/2:($videoHeight-ih*min($videoWidth/iw\,$videoHeight/ih))/2:ffffff' '$videoFileNew'");*/

	@copy($videoFile,$videoFileNew);
	
	@unlink($videoFile);
	@rename($videoFileNew, $videoFile);

	delete_directory($tempDir);
	return true;
}
function prepare_video_for_instagram($srcVideo,$targetFile,$videoWidth,$videoHeight)
{
	@copy($srcVideo,$targetFile);
	return true;
	$ffmpeg = FFMPEGPATH;

	//echo "Scaling video to exact size. <br />";
	exec("$ffmpeg -y -i '$srcVideo' -strict -2 -filter:v 'scale=iw*min($videoWidth/iw\,$videoHeight/ih):ih*min($videoWidth/iw\,$videoHeight/ih), pad=$videoWidth:$videoHeight:($videoWidth-iw*min($videoWidth/iw\,$videoHeight/ih))/2:($videoHeight-ih*min($videoWidth/iw\,$videoHeight/ih))/2:ffffff' '$targetFile'".' 2>&1', $result);
	
	return true;
}
function delete_directory($dirname)
{
	if (@is_dir($dirname))
	{
		$dir_handle = @opendir($dirname);
	}
	if (!$dir_handle)
	{
		return false;
	}
	while($file = readdir($dir_handle))
	{
		if ($file != "." && $file != "..")
		{
			if (!is_dir($dirname."/".$file))
			{
				@unlink($dirname."/".$file);
			}
			else
			{
				delete_directory($dirname.'/'.$file);
			}
		}
	}
	@closedir($dir_handle);
	@rmdir($dirname);
	return true;
}
function prepare_image_for_instagram($mediaPath,$instagramMediaPath,$imageWidth,$imageHeight)
{
	@copy($mediaPath,$instagramMediaPath);
	return true; /* Stop image resizing. As now instagram is supporing any sized image. */
	$image = new Imagick();
	$image->readImage($mediaPath);

	$image->setbackgroundcolor('rgb(64, 64, 64)');
	$image->thumbnailImage($imageWidth,$imageHeight, true, true);

	$image->resizeImage($imageWidth,$imageHeight, imagick::FILTER_LANCZOS, 0.9, true);

	$image->writeImage($instagramMediaPath);
	
	return true;
}
function isNull($text)
{
	if(trim($text) == "" || trim($text) == null)
	{
		$text = "--";
	}
	return $text;
}
function mirror_media($srcImg,$type)
{
	if(trim($type) == 'image')
	{
		$Ext = strtolower(substr(strrchr($srcImg, '.'), 1));
		$srcImgNew	= str_ireplace(".".$Ext,"_test.".$Ext,$srcImg);

		$image	= new Imagick($srcImg);
		$image->flopImage();
		$image->writeImage($srcImgNew);

		@unlink($srcImg);
		@rename($srcImgNew, $srcImg);
	}
	else if(trim($type) == 'video')
	{
		$ffmpeg 		= FFMPEGPATH;
		$videoFileNew	= str_ireplace(".mp4","_test.mp4",$srcImg);

		exec("$ffmpeg -y -i '$srcImg' -vf 'hflip,format=yuv420p' -metadata:s:v rotate=0 -codec:v libx264 -codec:a copy '$videoFileNew'");

		@unlink($srcImg);
		@rename($videoFileNew, $srcImg);
	}
}
function merge_overlay($srcImg,$overlayImg,$desImg,$type)
{
	if(trim($type) == 'image')
	{
		$image 		= new Imagick($srcImg);
		$overlay 	= new Imagick($overlayImg);
		$image->compositeImage($overlay,Imagick::COMPOSITE_DEFAULT,0,0);
		$image->writeImage($desImg);
	}
	else if(trim($type) == 'video')
	{
		$ffmpeg 		= FFMPEGPATH;
		$videoFileNew	= str_ireplace(".mp4","_test.mp4",$desImg);

		exec("$ffmpeg -y -i '$srcImg' -i '$overlayImg' -filter_complex 'overlay=0:0' -codec:a copy '$videoFileNew'");
		/*exec("$ffmpeg -y -i '$srcImg' -i '$overlayImg' -filter_complex 'overlay=0:main_h-overlay_h-1 [out]' -codec:a copy '$videoFileNew'");*/
		
		@unlink($desImg);
		@rename($videoFileNew, $desImg);
	}
}
function uploadFileErrorType($errorCode)
{
	if($errorCode == 0)
	{
		return false;
	}
	switch ($errorCode)
	{
		case 1:
				/*"The uploaded file exceeds the upload_max_filesize directive in php.ini",*/
				return "exceeds the file size";
		case 2:
				/*"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"*/
				return "exceeds the file size (Form)";
		case 3:
				/*"The uploaded file was only partially uploaded"*/
				return "was only partially uploaded";
		case 4:
				/*"No file was uploaded"*/
				return "was uploaded";
		case 6:
				/*"Missing a temporary folder"*/
				return "missing a temporary folder";
		case 7:
				/*"Failed to write file to disk"*/
				return "failed to write on disk";
		case 8:
				/*"A PHP extension stopped the file upload."*/
				return "stopped the file upload";
	}
}
function resize_overlay_image($source,$dest,$resize_height,$resize_width)
{
	$image = new Imagick();
	$image->readImage($source);
	$image->resizeImage($resize_width,$resize_height, imagick::FILTER_LANCZOS, 0.9, true);

	$transImg = new Imagick();
	$transImg->newImage($resize_width,$resize_height, new ImagickPixel('transparent')); // use this predefined transparent color string
	$transImg->setImageFormat('png32'); 

	$transImg->compositeImage($image, Imagick::COMPOSITE_COPY, 0, 0);
	$transImg->writeImage($dest);

	return true;
}
function convertPngToJpg($pngImg,$width, $height,$jpgImg)
{	
	$image=new Imagick($pngImg);

	$white=new Imagick();
	$white->newImage($width, $height, "white");
	$white->compositeimage($image, Imagick::COMPOSITE_OVER, 0, 0);
	$white->setImageFormat('jpg');
	$white->writeImage($jpgImg);

	return true;
}
function videoThumbnail($video,$thumbImage)
{
	$ffmpeg = FFMPEGPATH;

	exec("$ffmpeg -y -i '$video' -r 1 -ss 00:00:00 -vframes 1 '$thumbImage'".' 2>&1', $result);
}
function getAllEventsDetail()
{
	global $mysqli;
	$AllEventsDetailArr = array();

	$result = $mysqli->query("SELECT * FROM events ORDER BY id ASC");
	while($row = $result->fetch_assoc())
	{
		$ID	= trim($row['id']);
		$eventName	= stripslashes(trim($row['event_name']));
		$AllEventsDetailArr[$ID]['name'] = $eventName;
	}
	return $AllEventsDetailArr;
}
function getAllClientsDetail()
{
	global $mysqli;
	$AllClientsDetailArr = array();

	$result = $mysqli->query("SELECT * FROM users ORDER BY id ASC");
	while($row = $result->fetch_assoc())
	{
		$ID	= trim($row['id']);
		$fullName	= stripslashes(trim($row['full_name']));
		$AllClientsDetailArr[$ID]['name'] = $fullName;
	}
	return $AllClientsDetailArr;
}
function downloadFile($file_name)
{
	if(is_file($file_name))
	{
		// required for IE
		if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}

		// get the file mime type using the file extension
		switch(strtolower(substr(strrchr($file_name, '.'), 1)))
		{
			case 'pdf': $mime = 'application/pdf'; break;
			case 'zip': $mime = 'application/zip'; break;
			case 'jpeg':
			case 'jpg': $mime = 'image/jpg'; break;
			default: $mime = 'application/force-download';
		}
		header('Pragma: public'); 	// required
		header('Expires: 0');		// no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
		header('Cache-Control: private',false);
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($file_name));	// provide file size
		header('Connection: close');
		readfile($file_name);	// push it out
		exit();
	}
}
function emailOutBoundOverview($Cond)
{
    /*$ch = curl_init('https://api.postmarkapp.com/stats/outbound/opens?fromdate=2014-01-01&todate=2014-02-01');*/
    $ch = curl_init('https://api.postmarkapp.com/stats/outbound?'.$Cond);

	$headers = array(
		'Accept: application/json',
		'Content-Type: application/json',
		'X-Postmark-Server-Token: ' . POSTMARKAPP_API_KEY
	);
    /*curl_setopt($ch, CURLOPT_GET, 1);*/
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    if ($http_code == 200) {
        $response = $result;
    } else {
        $response = $http_code."-::-".$result;
    }
    return $response;
    /***
	"Sent": 615,
	"Bounced": 64,
	"SMTPApiErrors": 25,
	"BounceRate": 10.406,
	"SpamComplaints": 10,
	"SpamComplaintsRate": 1.626,
	"Opens": 166,
	"UniqueOpens": 26,
	"Tracked": 111,
	"WithClientRecorded": 14,
	"WithPlatformRecorded": 10,
	"WithReadTimeRecorded": 10
    **/
}
function validate_ip($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false)
    {
        return false;
    }
    return true;
}
function get_ip_address()
{
	$ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
	foreach ($ip_keys as $key)
	{
	    if (array_key_exists($key, $_SERVER) === true)
	    {
	        foreach (explode(',', $_SERVER[$key]) as $ip)
	        {
	            // trim for safety measures
	            $ip = trim($ip);
	            // attempt to validate IP
	            if (validate_ip($ip))
	            {
	                return $ip;
	            }
	        }
	    }
	}
	return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
}
function updateVisitorLog($page_name,$event_id,$client_id)
{
	global $mysqli;
	$ip = get_ip_address();
	$browser_data = $_SERVER['HTTP_USER_AGENT'];
	$mysqli->query("INSERT INTO visitor_log SET ip='".addslashes($ip)."',browser_data='".addslashes($browser_data)."',page_name='".$page_name."',event_id='".$event_id."',client_id='".$client_id."',createdon='".time()."'");
}
function updateClickLog($dataArr)
{
	global $mysqli;
	$page_name 	= $dataArr['page_name'];
	$event_id 	= $dataArr['event_id'];
	$client_id 	= $dataArr['client_id'];
	$media_id 	= $dataArr['media_id'];

	$ip = get_ip_address();
	$browser_data = $_SERVER['HTTP_USER_AGENT'];
	$mysqli->query("INSERT INTO clicks_log SET ip='".addslashes($ip)."',browser_data='".addslashes($browser_data)."',page_name='".$page_name."',event_id='".$event_id."',client_id='".$client_id."',media_id='".$media_id."',createdon='".time()."'");
}
function hex2rgb($hex)
{
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}
function videoToSWF($video,$swfFile)
{
	$ffmpeg = FFMPEGPATH;

	exec("$ffmpeg -y -i '$video' -b:v 1200k '$swfFile'".' 2>&1', $result);
}
function loopVideo($video,$targetVideo,$loop)
{
	$ffmpeg 		= FFMPEGPATH;
	/*$loopedVideo 	= str_ireplace(".mp4", "_loop.mp4", $video);*/
	$loopedVideo 	= $targetVideo;
	$time 			= time();
	$textFile 		= str_ireplace("/ffmpeg","/media",$ffmpeg)."/".$time.".txt";

	$content = "";
	for($i=1; $i<=$loop; $i++)
	{
		$content .= "file '".$video."'\n";
	}
	// Write the contents to the file, 
	// using the FILE_APPEND flag to append the content to the end of the file
	// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
	file_put_contents($textFile, $content, FILE_APPEND | LOCK_EX);

	exec("$ffmpeg -f concat -i '".$textFile."' -c copy '$loopedVideo'".' 2>&1', $result);
	@unlink($textFile);
	/*@unlink($video);
	@rename($loopedVideo, $video);*/
}
function getEventSlug($event_id)
{
	global $mysqli;
	$slug = "";

	$sql="SELECT * FROM events WHERE id = '".trim($event_id)."'";
	$sql_result 	= $mysqli->query($sql." ORDER BY id DESC") or print mysql_error();
	$totalEvents	= mysqli_num_rows($sql_result) or print mysql_error();
	while($event=mysqli_fetch_array($sql_result))
	{
		$slug 		= $event['slug'];
	}
	return $slug;
}
function frenchChars($string)
{
  $normalizeChars = array(
    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
    'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
    'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
    'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
  );

  $string = strtr($string, $normalizeChars); /* Translating the letters */

  $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");

  $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");

  $string = str_replace($search, $replace, $string); /* Replacing the letters */
  
  return $string;
}
function move_all_processed_media_on_s3($media_id)
{
	global $S3_Buckets, $mysqli;

	$mediaResult = $mysqli->query("SELECT * FROM event_images WHERE id='".$media_id."'");
	if($mediaResult->num_rows > 0)
	{
		while($mediaRow = $mediaResult->fetch_assoc())
		{
			$eventMediaArray = array();
			$media_id 	= trim($mediaRow['id']);
			$event_id 	= trim($mediaRow['event_id']);
			$media_type = strtolower(trim($mediaRow['media_type']));
			$mediaFile 	= trim($mediaRow['images']);
			$wasGif 	= trim($mediaRow['was_gif']);
			$instagram_image    = trim($mediaRow['instagram_image']);

			$instagramMediaPath = ROOT_DIR."/".'media/'.$event_id.'/instagram/'.$instagram_image;
			//@unlink($instagramMediaPath);
			/*if(!file_exists($instagramMediaPath) && trim($instagram_image) != "")
			{*/
			    /************************* Prepare Media For Instagram ********************************/
			    
				    $imageWidth     = "640";
				    $imageHeight    = "640";
				    $mediaPath      = ROOT_DIR."/".'media/'.$event_id.'/'.$mediaFile;
				    if(strtolower(trim($media_type)) == 'video/mp4')
				    {
				        if($wasGif == 1)
				        {
				            $mediaPath = ROOT_DIR."/".'media/'.$event_id.'/looped/'.$mediaFile;
				            if(!file_exists($mediaPath) && trim($mediaPath) != "")
				            {
				                $mediaPath = ROOT_DIR."/".'media/'.$event_id.'/'.$mediaFile;
				            }
				        }

				        $mediaNewFile       = frenchChars($mediaFile);
				        $instagramMediaDir  = ROOT_DIR."/".'media/'.$event_id.'/instagram/';
				        $instagramMediaPath = $instagramMediaDir.$mediaNewFile;

				        $oldmask = umask(0);
				        @mkdir($instagramMediaDir,0777,true);
				        umask($oldmask);

				        $result = prepare_video_for_instagram($mediaPath,$instagramMediaPath,$imageWidth,$imageHeight);
				    }
				    else if(strtolower(trim($media_type)) == 'image/gif')
				    {
				        $mediaNewFile       = frenchChars(str_ireplace(".gif",".mp4",$mediaFile));
				        $instagramMediaDir  = ROOT_DIR."/".'media/'.$event_id.'/instagram/';
				        $instagramMediaPath = $instagramMediaDir.$mediaNewFile;
				        
				        $oldmask = umask(0);
				        @mkdir($instagramMediaDir,0777,true);
				        umask($oldmask);
				        
				        $result = gif_to_video_for_instagram($mediaPath,$instagramMediaPath,$videoWidth,$videoHeight);
				    }
				    else
				    {
				        $mediaNewFile       = frenchChars($mediaFile);
				        $instagramMediaDir  = ROOT_DIR."/".'media/'.$event_id.'/instagram/';
				        $instagramMediaPath = $instagramMediaDir.$mediaNewFile;

				        $oldmask = umask(0);
				        @mkdir($instagramMediaDir,0777,true);
				        umask($oldmask);

				        $result = prepare_image_for_instagram($mediaPath,$instagramMediaPath,$imageWidth,$imageHeight);
				    }
				    if(file_exists($instagramMediaDir.$mediaNewFile) && trim($mediaNewFile) != "")
				    {
						$mysqli->query("UPDATE event_images SET instagram_image='".$mediaNewFile."' WHERE id = '".$media_id."'");
					}
		    	/************************* Prepare Media For Instagram ********************************/
			/*}*/

			$media 			= 'media/'.$event_id.'/'.$mediaFile;
			$gifMedia 		= 'media/'.$event_id.'/'.str_ireplace(array(".mp4",".gif"), ".gif", $mediaFile);
			$orgGifMedia 	= 'media/'.$event_id.'/org/'.str_ireplace(array(".mp4",".gif"), ".gif", $mediaFile);
			$orgMedia 		= 'media/'.$event_id.'/org/'.$mediaFile;
			$thumbMedia		= 'media/'.$event_id."/thumb/".$mediaFile;
			$loopedMedia 	= 'media/'.$event_id."/looped/".str_ireplace(array(".mp4",".gif"), ".mp4", $mediaFile);
			$instagramMedia = 'media/'.$event_id."/instagram/".str_ireplace(array(".mp4",".gif"), ".mp4", $mediaFile);
			$videoThumb		= 'media/'.$event_id."/video-thumbs/".str_ireplace(array(".mp4",".gif"), ".jpg", $mediaFile);

			$eventMediaArray['media'] 			= $media;
			$eventMediaArray['gifMedia'] 		= $gifMedia;
			$eventMediaArray['orgGifMedia'] 	= $orgGifMedia;
			$eventMediaArray['orgMedia'] 		= $orgMedia;
			$eventMediaArray['thumbMedia'] 		= $thumbMedia;
			$eventMediaArray['loopedMedia'] 	= $loopedMedia;
			$eventMediaArray['videoThumb'] 		= $videoThumb;
			$eventMediaArray['instagramMedia'] 	= $instagramMedia;

			foreach ($eventMediaArray as $key => $value)
			{
				if(!file_exists(ROOT_DIR."/".$value) && trim($mediaFile) != "")
				{
					unset($eventMediaArray[$key]);
				}
			}

			$uploaded_media = 0;

			foreach ($eventMediaArray as $key => $value)
			{
				$bucket 		= $S3_Buckets['wrapper'];
				$srcFilePath 	= ROOT_DIR."/".$value;
				$tarFilePath 	= $value;

				if(empty($bucket))
				{
					$bucket = 'sb-wrapper';
				}
				if(!file_exists($srcFilePath) && trim($mediaFile) != "")
				{
					unset($eventMediaArray[$key]);
				}
				else
				{
					$response = upload_file($srcFilePath, $tarFilePath, $bucket);
					$response = json_decode($response);
					if($response->success)
					{
						$uploaded_media++;
						//@unlink(ROOT_DIR."/".$value);
					}
				}
			}
			if($uploaded_media == count($eventMediaArray) && $uploaded_media > 0)
			{
				foreach ($eventMediaArray as $key => $value)
				{
					@unlink(ROOT_DIR."/".$value);
				}
				$mysqli->query("UPDATE event_images SET on_s3='1' WHERE id = '".$media_id."'");
			}
		}
	}
}
function move_raw_media_on_s3($media)
{
	global $S3_Buckets;
	$bucket 		= $S3_Buckets['raw-media'];
	$srcFilePath 	= ROOT_DIR."/".$media;
	$tarFilePath 	= $media;
	
	if(empty($bucket))
	{
		$bucket = 'sb-raw-media';
	}
	$response = upload_file($srcFilePath, $tarFilePath, $bucket);
	$response = json_decode($response);
	if($response->success)
	{
		return true;
	}
	else
	{
		return $response;
	}
}
?>