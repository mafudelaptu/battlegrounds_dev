<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class RaceType{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getRaceData($raceTypeID){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getRaceData <br>\n";
		if($raceTypeID > 0){
			$sql = "SELECT *
					FROM `RaceType`
					WHERE RaceTypeID = ".(int) $raceTypeID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "raceTypeID = 0";
		}

		$ret['debug'] .= "End getRaceData <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getRaceStats($raceData){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getRaceStats <br>\n";
		if(is_array($raceData) && count($raceData) > 0){
				
			$raceTypeID = $raceData['RaceTypeID'];
			$start = $raceData['StartTimestamp'];
			$end = $raceData['EndTimestamp'];
			$winnerCount = $raceData['WinnerCount'];
			$winnerCountType = $raceData['WinnerCountType'];
			// active Players
			$UserPoints = new UserPoints();
			$retUP = $UserPoints->getActivePlayersOverTime($start, $end);
			$ret['debug'] .= p($retUP,1);
			$activePlayersData = $retUP['data'];
				
			// Players in Winning-List
			$countActive = count($activePlayersData);
			if($winnerCountType == "Percent"){
				$countWinners = ceil($winnerCount/100 * $countActive);
			}
				
			// Matches played
			$Match = new Match();
			$retM = $Match->getMatchesPlayedOverTime($start, $end);
			$ret['debug'] .= p($retM,1);
			$MatchesPlayedCount = count($retM['data']);
				
			$data['ActivePlayersCount'] = (int)$countActive;
			$data['WinnersCount'] = (int)$countWinners;
			$data['MatchesPlayedCount'] = (int)$MatchesPlayedCount;
				
			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "raceData = 0";
		}



		$ret['debug'] .= "End getRaceStats <br>\n";

		return $ret;


	}
	

}

?>