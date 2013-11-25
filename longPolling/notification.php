<?php 
session_start();
define("HOME_PATH_REL", "/battlegrounds/");

require_once(dirname(__FILE__)."/../inc/inc_base.php");
$steamID = $_SESSION['user']['steamID'];
session_write_close();


$ret = array();
$ret['debug'] = "";
$sql = "";
$from = "";
$where = "";
$orderBy = "ORDER BY TimestampCreated ASC";

$DB = new DB();
$con = $DB->conDB();

$i=0;
$globalCount = 0;
while($i <= 15){
	for($j=0; $j<1; $j++){
		$select = "*";

		//if($j==0){
			$from = "UserNotifications";
			$type = "user";
			$where = "WHERE SteamID = ".secureNumber($steamID)." AND Checked = 0";
		//}
// 		else{
// 			$from = "GlobalNotifications";
// 			$type = "global";
// 			$where = "WHERE Checked = 0";
// 		}
		$sql="SELECT ".$select."
			FROM ".$from."
			".$where."
			".$orderBy."
			LIMIT 1
			";
		//p($sql);
		$ret['debug'] .= p($sql,1);
		$data = $DB->select($sql);
		
// 		p($sql);
// 		p($count);
// 		p($data);
		if(is_array($data) && count($data) > 0){
			$ret['type'] = $type;
			$ret['status'] = true;
			$ret['data'] = $data;
			echo json_encode($ret);
			exit();
			break;
			
		}
	}
	flush();
	//ob_flush();
	sleep(1);
	$i++;
}
mysql_close($con);

if($i > 16){
	echo json_encode($ret);
}
ob_flush();
flush();

exit();
?>