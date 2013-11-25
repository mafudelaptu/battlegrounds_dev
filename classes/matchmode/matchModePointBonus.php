<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class MatchModePointBonus{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPointBonusForMatchModeByMatchID($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPointBonusForMatchMode <br>\n";
		if($matchID > 0){
			
			$sql = "SELECT mmpb.*
								FROM `MatchModePointBonus` mmpb JOIN `Match` m ON m.MatchModeID = mmpb.MatchModeID
								WHERE m.MatchID = ".(int) $matchID." AND mmpb.Active = 1
								LIMIT 1
								";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			
			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID = 0";
		}

		$ret['debug'] .= "End getPointBonusForMatchMode <br>\n";

		return $ret;
	}

}

?>