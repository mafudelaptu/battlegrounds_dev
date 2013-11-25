<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class MatchMode{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllMatchModes($select="*", $hideInactive=true){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();
		if($hideInactive){
			$where = "WHERE Active = 1";
		}
		else{
			$where = "";
		}
			
		$sql = "SELECT ".$select."
				FROM MatchMode
				".$where."
						";
		$ret = $DB->multiSelect($sql);
			
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertNewMatchMode($matchMode){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertNewMatchMode <br>\n";
		if($matchMode != ""){
				
				
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchMode = ''";
		}

		$ret['debug'] .= "End insertNewMatchMode <br>\n";

		return $ret;
	}

	function getMostMatchmodesPlayedOfUser($steamID=0) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMostMatchmodesPlayedOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
			$Match = new Match();
			$retM = $Match->getMatchesPlayedCountOfUser($steamID);
			$matchCount = $retM['data'];
			if($matchCount > 0){
				$sql = "SELECT m.MatchModeID, CAST((Count(m.MatchModeID)/".(int) $matchCount.")*100 AS DECIMAL(12,2)) as Value, mm.Name as MatchModeName, mm.Shortcut as MMShortcut
							FROM `Match` m JOIN MatchDetails md ON m.MatchID = md.MatchID
								JOIN MatchMode mm ON mm.MatchModeID = m.MatchModeID
							WHERE md.SteamID = ".secureNumber($steamID)." AND mm.Active = 1 AND Canceled = 0 AND TeamWonID != -1
					GROUP BY m.MatchModeID
									ORDER BY Value DESC
									LIMIT 6
			";
				$data = $DB->multiSelect($sql);
				$ret['debug'] .= p($sql,1);
					
				$ret['data'] = $data;
			}
			else{
				$ret['data'] = false;
			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getMostMatchmodesPlayedOfUser <br>\n";
		return $ret;
	}
}

?>
