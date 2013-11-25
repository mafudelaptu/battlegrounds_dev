<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class EventPool{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertWonTeamIntoPool($eventID, $createdEventID, $round, $teamData){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertWonTeamIntoPool <br>\n";
		if($eventID > 0 && $createdEventID > 0 && $round > 0){
			if(is_array($teamData) && count($teamData)>0){
				$insertArray = array();
				foreach($teamData as $k => $v){
					$tmpArray = array();
					$tmpArray['EventID'] = (int)$eventID;
					$tmpArray['CreatedEventID'] = (int)$createdEventID;
					$tmpArray['Round'] = (int)$round;
					$tmpArray['SteamID'] = secureNumber($v['SteamID']);
					$tmpArray['Elo'] = (int)$v['Elo'];
						
					$insertArray[] = $tmpArray;
				}

				$retIns = $DB->multiINsert("EventPool", $insertArray);
				$ret['status'] = $retIns;
			}
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | round = 0";
		}

		$ret['debug'] .= "End insertWonTeamIntoPool <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkPoolEnoughPlayerForRound2($eventID, $createdEventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkPoolEnoughPlayerForRound2 <br>\n";

		if($eventID > 0 && $createdEventID > 0){
			$sql = "SELECT *
					FROM `EventPool`
					WHERE Round = 2 AND EventID = ".(int) $eventID." AND CreatedEventID = ".(int) $createdEventID."
					
					";
			$ret['debug'] = p($sql,1);
			$data = $DB->multiSelect($sql);
			$count = count($data);
			$ret['data'] = $data;
			$ret['count'] = (int)$count;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | teamID = 0";
		}


		$ret['debug'] .= "End checkPoolEnoughPlayerForRound2 <br>\n";

		return $ret;
	}

}

?>