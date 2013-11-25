<?php
session_start();
$pfadTiefe = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__) ); // alle nötigen PHP funktionen einfügen
$slashCount = substr_count($pfadTiefe, '/'); // Zählen wieviele Slashes drinne sind
$ebeneTiefer = "";
for($i=0; $i<$slashCount; $i++){ // für jeden slash eine ebene tiefergehen
	$ebeneTiefer .= DIRECTORY_SEPARATOR . '..';
}
$pfad = dirname(__FILE__).$ebeneTiefer;
require_once( dirname(__FILE__)."/inc/inc_base.php");
session_write_close();
?>
<?php 
$ret = array();
$DB = new DB();
$con = $DB->conDB();

$section = $_POST['section'];
$special = $_POST['special'];
$lastTimestamp = $_POST['lastTimestamp'];
$i = 0;
while($i <= 15){
	$mess1 = time();
	$sql = "SELECT *
		FROM `Chat` c LEFT JOIN User u ON u.SteamID = c.SteamID
		WHERE Section = '".mysql_real_escape_string($section)."' AND Special = '".mysql_real_escape_string($special)."'
		AND Timestamp > ".(int)$lastTimestamp."
		ORDER BY Timestamp DESC
		LIMIT 50
		";
	$data = $DB->multiSelect($sql);
	$mess2 = time();
	//$ret['debug'] .= p($sql,1);

	$ts = (int)$data[0]['Timestamp'];
	$ret['lastTimestamp'] = $ts;
	$ret['status'] = false;
	$ret['debug'] .= p("ts:".$ts." lTs:".$lastTimestamp,1);
	if($ts == $lastTimestamp){
		//$ret['debug'] .= p("dauer:".($mess2-$mess1),1);
		$i++;
		ob_flush();
		flush();
		sleep(1);
	}
	else{
		if(is_array($data) && count($data) > 0){
			$dataOrdered = orderArrayBy($data, "Timestamp", SORT_ASC);

			$ret['data'] = $dataOrdered;
			if(is_array($dataOrdered) && count($dataOrdered) > 0){
				foreach ($dataOrdered as $k => $v) {
					$smarty->assign ( "message", $v["Msg"] );
					$smarty->assign ( "userName", $v['Name'] );
					$smarty->assign ( "userAvatar", $v['Avatar'] );
					$smarty->assign ( "time", $v['Timestamp'] );
					$smarty->assign ( "userSteamID", $v['SteamID'] );
					$chatMessage = $smarty->fetch ( "prototypes/chatMessage.tpl" );
					$html [] = $chatMessage;
				}
			}
			$ret['html'] = $html;
			$ret['lastTimestamp'] = $ts;
			$ret['status'] = true;
			echo json_encode($ret);
			break;
		}
		else{
			$i++;
			ob_flush();
			flush();
			sleep(1);
		}
	}
}
mysql_close($con);
if($i > 15){
	$ret['lastTimestamp'] = $lastTimestamp;
	echo json_encode($ret);
}

ob_flush();
flush();
exit;
?>