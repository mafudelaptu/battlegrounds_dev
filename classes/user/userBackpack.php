<?php 

class UserBackpack{
	function getAllBackpackItemsOfUser($steamID = 0) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllBackpackItemsOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT *
					FROM `UserBackpack` ub JOIN BackpackItems bi ON bi.BackpackItemID = ub.BackpackItemID
					WHERE ub.SteamID = ".secureNumber($steamID)." AND bi.Active=1
					ORDER BY GetTimestamp DESC
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getAllBackpackItemsOfUser <br>\n";
		return $ret;
	}
}

?>