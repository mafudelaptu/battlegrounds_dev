<?php
class Notification {
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-09-04
	*/
	function getAllNotificationsForUser($steamID=0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getAllNotifications <br>\n";
		
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		
		if ($steamID > 0) {
			
			$sql="SELECT *
			FROM UserNotifications
			WHERE SteamID = ".secureNumber($steamID)." AND Checked = 0
			ORDER BY TimestampCreated ASC
			LIMIT 1
			";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			// 		p($sql);
			// 		p($count);
			// 		p($data);
			if(is_array($data) && count($data) > 0){
				$ret['type'] = "user";
				$ret['status'] = true;
				$ret['data'] = $data;
				
				$ret['status'] = true;
			}
			else{
				$ret ['status'] = false;
			}
			
		} else {
			$ret ['status'] = "steamID == 0";
		}
		$ret ['debug'] .= "End getAllNotifications <br>\n";
		return $ret;
	}
}

?>