<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class QueueLock{
	
	const lockTime = 180;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertLock($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertLock <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		
		if($steamID > 0){
			
			$insertArray = array();
			
			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['Timestamp'] = time()+QueueLock::lockTime;
			
			$ret = $DB->insert("QueueLock", $insertArray);

			$ret['status'] = $ret;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End insertLock <br>\n";
		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkLock($steamID = 0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkLock <br>\n";
		
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		
		if($steamID > 0){
				
			$sql = "SELECT *
							FROM `QueueLock` 
							WHERE SteamID = ".secureNumber($steamID)."
			";
			$data = $DB->select($sql); 
			$ret['debug'] .= p($sql,1);
			if($data['SteamID'] > 0){
				$timestamp = $data['Timestamp']-time();
				$ret['debug'] .= p($timestamp,1);
				if($timestamp > 0){
					$time = date("i:s",$timestamp+30); // 63 sec puffer
					$tmp = explode(":", $time);
					$min = $tmp[0];
					$sec = $tmp[1];
				}
				else{
					$min = "0";
					$sec = "0";
				}
				
				$ret['time'] = $min." minutes and ".$sec." seconds";
				$ret['status'] = false;
			}
			else{
				$ret['status'] = true;
			}
			
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End checkLock <br>\n";
		return $ret;
		
	}
}

?>