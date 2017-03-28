<?php
function validate1($var,$varname,$empty,$numeric,$alphanum,$alpha,$alphawhite,$alphaext,$dtext)
	{
 //If $var variable not found
 //if(!$var) echo "Variable Error.<br>";

 //If $empty is 1 than $var should not be blank
 	$error="";
 	if($empty=="1") {
 		if(($var=="") || (!$var)){
 			$error.="<b>$varname</b> cannot be left blank. <br>";
 			return $error;
 		}
 	}

 //If $numeric is 1 that $var should be in digits
 	if($numeric=="1") {
 		$theresults = preg_match("[^0-9.]", $var, $trashed);
 		if($theresults) {
 			$error.="<b>$varname</b> should be in valid. Only Digits 0 to 9 are allowed.<br>";
 			return $error;
 		}
 	}

 //If $alphanum is 1 that $var should be a word
 	if($alphanum=="1") {
 		$theresults = preg_match("[^0-9-]", $var, $trashed);
 		if($theresults) {
 			//print "<b><font color=red>Error</font></b> : <b>$varname</b> should be a word. <br>";
 			$error.="<b>$varname</b> should be valid. Only digits 0 to 9 and symbol - are allowed.<br>";
 			return $error;
 		}
 	}



 //If $alpha is 1 that $var should be alpha character
 	if($alpha=="1") {
 		$theresults = preg_match("[^A-Za-z ]", $var, $trashed);
 		if($theresults) {
 			//print "<b><font color=red>Error</font></b> : <b>$varname</b> should be in Alpha Word Only. <br>";
			$error.="<b>$varname</b> should be valid. Only Alphabates are allowed<br>";
 			return $error;
 		}
 	}
 //If $alphawhite is 1 that $var should be alphawhite with white
 	if($alphawhite=="1") {
 		$theresults = preg_match("[^A-Za-z0-9$#%,*@! ]", $var, $trashed);
 		if($theresults) {
 			//print "<b><font color=red>Error</font></b> : <b>$varname</b> should be in Alphabet with Space Only. <br>";
			$error.="<b>$varname</b> should be valid. Only Alphabates(a to z),Digits(0 to 9) and special character($#%,*@!) are allowed.<br>";
 			return $error;
 		}
 	}
 //If $alphaext is 1 that $var should be alpha, white or -
 	if($alphaext=="1") {
 		$theresults = preg_match("[^A-Za-z]\-[^A-Za-z _]", $var, $trashed);
 		if($theresults) {
 			//print "<b><font color=red>Error</font></b> : <b>$varname</b> is invalid, choose another. <br>";
 			return "error";
 		}
 	}

 //If $text is 1 that $var should be description withour html and email address
 	if($dtext=="1") {
 		$theresults = preg_match("[^0-9A-Za-z \t\n\r\.\?\(\)\&\"\:\;\,_]", $var, $trashed);
 		if($theresults) {
 			//print "<b><font color=red>Error</font></b> : <b>$varname</b> contains invalid characters (Only digits,alphabets,whitespace and : . \" ; , - are allowed). <br>";
 			return "error";
 		}
 	}
	
 }
 

	 
	function failure_message($message,$heading="",$width="90%")
	{
		echo"
		
			<div id=\"msg-flash1\" style=\"width:$width\">
		";
		if(!empty($heading))
		{
			echo"<h2>$heading</h2>";
		}
		echo "$message";	
			
	
		echo "</div>";	
	}

	function ymd_to_dmy($date)
	{		
		$date=explode(" ",$date);
		
		$month_text_array=array("","Jan","Feb","March","April","May","Jun","July","Aug","Sep","Oct","Nov","Dec");	

		$date=explode("-",$date[0]);

 		$month=abs($date[1]);
	 	$day=$date[2];
 		$year=$date[0];

		$date=$day." ".$month_text_array[$month]." ".$year;
	
 		return $date;
	}

	function ymd_to_dmy_short($date)
	{
		
		$date=explode(" ",$date);
		

		$month_text_array=array("","Jan","Feb","March","April","May","Jun","July","Aug","Sep","Oct","Nov","Dec");

		$date=explode("-",$date[0]);

 		$month=abs($date[1]);
	 	$day=$date[2];
 		$year=$date[0];

		$date=$day."".$month_text_array[$month]."".$year;
	
 		return $date;	
	}
	
 function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
function generateSlug($Str)
{
	$Str = strtolower(trim($Str));
	$Str = str_replace(array("~","`","!","@","#","$","%","^","*",'"',"'",":",";",".",">","<",",","?","|","\\","‘","’",'“','”'),"",$Str);
	$Str = str_replace(array("{","[","("),"",$Str);
	$Str = str_replace(array("}","]",")"),"",$Str);
	$Str = str_replace(array("&"),"-",$Str);
	$Str = str_replace(array("/"),"-",$Str);
	$Str = str_replace(array(" "),"-",$Str);
	$Str = str_replace(array("--"),"-",$Str);
	$Str = str_replace(array("-"),"-",$Str);
	return $Str;
}
function diskTotalSpaceBytes()
{
	$bytes = disk_total_space(".");
	return $bytes;
}
function getDiskTotalSpace()
{
	$bytes = disk_total_space("."); 
	$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
	$base = 1024;
	$class = min((int)log($bytes , $base) , count($si_prefix) - 1);

	return sprintf('%1.2f' , $bytes / pow($base,$class)).' '.$si_prefix[$class];
}
function diskFreeSpaceBytes()
{
	$bytes = disk_free_space(".");
	return $bytes;
}
function getDiskFreeSpace()
{
	$bytes = disk_free_space("."); 
	$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
	$base = 1024;
	$class = min((int)log($bytes , $base) , count($si_prefix) - 1);

	return sprintf('%1.2f' , $bytes / pow($base,$class)).' '.$si_prefix[$class];
}
function getDomainDetails($domain)
{
	/*echo shell_exec(" whois ".$domain." | egrep -i 'Expiration|Expires on'");*/
	include_once(__DIR__.'/../phpwhois-4.2.2/whois.main.php');

	$whois = new Whois();
	$data = $whois->Lookup($domain);
	$return['registrar'] = $data['regyinfo']['registrar'];
	$return['expires'] = $data['regrinfo']['domain']['expires'];
	return $return;
}
function getSSLDetails($url)
{
	$url = str_ireplace(array("http://","https://"), "", strtolower($url));
	$url = "http://".$url;
    $orignal_parse = parse_url($url, PHP_URL_HOST);
    $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
    $read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
    $cert = stream_context_get_params($read);
    $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);

    return $certinfo;
}
?>