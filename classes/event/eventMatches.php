<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class EventMatches{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertEventMatchToEvent($eventID, $createdEventID, $matchID, $round, $team1, $team2){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertAllEventMatchesOfEvent <br>\n";
		if($eventID > 0 && $createdEventID > 0 && $matchID>0 && $round > 0 && $team1 > 0 && $team2 > 0){

			$tmpArray = array();
			$tmpArray['EventID'] = (int) $eventID;
			$tmpArray['CreatedEventID'] = (int) $createdEventID;
			$tmpArray['Round'] = (int) $round;
			$tmpArray['MatchID'] = (int) $matchID;
			$tmpArray['Team1'] =  (int) $team1;
			$tmpArray['Team2'] = (int) $team2;

			$retIns = $DB->insert("EventMatches", $tmpArray);
			$ret['status'] = $retIns;
		}
		else{
			$ret['status'] = "eventID = 0 |  createdEventID = 0 etc";
		}

		$ret['debug'] .= "End insertAllEventMatchesOfEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchData($eventID, $createdEventID, $teamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchData <br>\n";


		if($eventID > 0 && $createdEventID > 0 && $teamID > 0){
			$sql = "SELECT *
					FROM `EventMatches`
					WHERE EventID = ".(int) $eventID." AND CreatedEventID = ".(int) $createdEventID."
							AND (Team1 = ".(int) $teamID." OR Team2 = ".(int) $teamID.")
									LIMIT 1
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0 |  createdEventID = 0 etc";
		}

		$ret['debug'] .= "End getMatchData <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchDataByMatchID($eventID, $createdEventID, $matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchDataByMatchID <br>\n";


		if($eventID > 0 && $createdEventID > 0 && $matchID > 0){
			$sql = "SELECT *
					FROM `EventMatches`
					WHERE EventID = ".(int) $eventID." AND CreatedEventID = ".(int) $createdEventID."
							AND MatchID = ".(int)$matchID."
									LIMIT 1
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0 |  createdEventID = 0 etc";
		}

		$ret['debug'] .= "End getMatchDataByMatchID <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function updateEventMatchesTeamWonID($eventID, $createdEventID, $matchID, $teamWonID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start updateEventMatchesTeamWonID <br>\n";


		if($eventID > 0 && $createdEventID > 0 && $matchID > 0){
			$sql = "UPDATE `EventMatches`
					SET TeamWonID = ".(int) $teamWonID."
							WHERE EventID = ".(int) $eventID." AND CreatedEventID = ".(int) $createdEventID."
									AND MatchID = ".(int) $matchID."
											";
			$ret['debug'] .= p($sql,1);
			$data = $DB->update($sql);

			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "eventID = 0 |  createdEventID = 0 etc";
		}

		$ret['debug'] .= "End updateEventMatchesTeamWonID <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getEventMatchesStatus($eventID, $createdEventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getEventMatchesStatus <br>\n";

		if($eventID > 0 && $createdEventID > 0){
			$sql = "SELECT *
					FROM `EventMatches`
					WHERE EventID = ".(int) $eventID." AND CreatedEventID = ".(int) $createdEventID."
							";
			$data = $DB->multiSelect($sql);

			// Array aufbereiten
			if(is_array($data) && count($data)>0){
				$retArray = array();
				foreach($data as $k => $v){
					$tmpArray = array();
					$tmpArray['Team1'] = $v['Team1'];
					$tmpArray['Team2'] = $v['Team2'];
					$tmpArray['TeamWonID'] = $v['TeamWonID'];
					$retArray[$v['Round']][] = $tmpArray;
				}
			}
			$ret['data'] = $retArray;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0";
		}


		$ret['debug'] .= "End getEventMatchesStatus <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkIfRound2AlreadyCreated($eventID, $createdEventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkIfRound2AlreadyCreated <br>\n";

		if($eventID > 0 && $createdEventID > 0){
			$sql = "SELECT *
					FROM `EventMatches`
					WHERE EventID = ".(int) $eventID." AND CreatedEventID = ".(int) $createdEventID."
							AND Round = 2 AND Team1 = 5 AND Team2 = 6
							";
			$ret['debug'] = p($sql,1);
			$count = $DB->countRows($sql);
			if($count > 0){
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
				
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | teamID = 0";
		}


		$ret['debug'] .= "End checkIfRound2AlreadyCreated <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getEventMatchDataByMatchID($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getEventMatchDataByMatchID <br>\n";
		if($matchID > 0){
			$sql = "SELECT *
					FROM `EventMatches`
					WHERE MatchID = ".(int)$matchID."
									LIMIT 1
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			
			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getEventMatchDataByMatchID <br>\n";

		return $ret;
	}
}

?>