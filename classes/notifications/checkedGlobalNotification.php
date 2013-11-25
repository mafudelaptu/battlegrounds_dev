<?php 

class CheckedGlobalNotification{

	function insertCheckedNotification($id){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertCheckedNotification <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($id > 0){
			$data = array();
			$data['SteamID'] = secureNumber($steamID);
			$data['GlobalNotificationID'] = (int)($id);
			$data['Timestamp'] = (int)time();
			$retIns = $DB->insert("CheckedGlobalNotification", $data);

			$ret['status'] = $retIns;
		}
		else{
			$ret['status'] = "id == 0";
		}
		$ret['debug'] .= "End insertCheckedNotification <br>\n";
		return $ret;
	}

}

?>