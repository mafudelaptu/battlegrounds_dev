<?php 

class UserCoins {
	function getCoinsOfUser($steamID=0) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getCoinsOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT SUM(uc.CoinsChange) as Coins
					FROM `UserCoins` uc
					WHERE uc.SteamID = ".secureNumber($steamID)."
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);
				
			$ret['data'] = (int)$data['Coins'];
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getCoinsOfUser <br>\n";
		return $ret;
	}

	function insertCoinsChangeForUser($coinsTypeID, $coinsChange, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertCoinsChangeForUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
			
			$data = array();
			$data['SteamID'] = secureNumber($steamID);
			$data['CoinsTypeID'] = (int) $coinsTypeID;
			$data['CoinsChange'] = mysql_real_escape_string($coinsChange);
			$data['Timestamp'] = time();
			
			$retIns = $DB->insert("UserCoins", $data);
			$ret['status'] = $retIns;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End insertCoinsChangeForUser <br>\n";
		return $ret;
	}
}

?>