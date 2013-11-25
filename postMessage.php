<?php
session_start();
require_once(dirname(__FILE__)."/inc/inc_general_php_functions.php");

$steamID = $_SESSION['user']['steamID'];

session_write_close();
?>
<?php 
$ret = array();
$DB = new DB();
$con = $DB->conDB();

$section = $_POST['section'];
$special = $_POST['special'];
$inputMessage = $_POST['msg'];


$message = htmlentities(strip_tags($inputMessage));
$ret['debug'] .= p($message,1);
if($message != "\n" && $message != ""){
	$insertArray = array();
	$insertArray['Section'] = mysql_real_escape_string($section);
	$insertArray['Special'] = mysql_real_escape_string($special);
	$insertArray['SteamID'] = secureNumber($steamID);
	$insertArray['Timestamp'] = time();
	$insertArray['Msg'] = mysql_real_escape_string($message);
		
	$retInsDebug =  $DB->insert("Chat", $insertArray, null, 1);
	$ret['debug'] .= p($retInsDebug,1);
	$retIns = $DB->insert("Chat", $insertArray);
	$ret['lastTimestamp'] = time();
	$ret['status'] = true;
}
else{
	$ret['status'] = false;
}
mysql_close($con);
echo json_encode($ret);

?>