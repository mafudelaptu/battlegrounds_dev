<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class EventWinner{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLastEventWinners(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLastEventWinners <br>\n";

		// last event auslesen
		$Event = new Event();
		$lastWinnersRet = $Event->getLastEvents("1",true,false,false);
		$ret['debug'] .= p($lastWinnersRet,1);

		$ret['data'] = $lastWinnersRet['data'][0]['winner'];
		$ret['status'] = true;

		$ret['debug'] .= "End getLastEventWinners <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getWinnerOfEvent($eventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getWinnerOfEvent <br>\n";
		if($eventID > 0){
			$sqlPointsEarned = "
									SELECT IF(SUM(PointsChange) > 0, SUM(PointsChange), 0)
									FROM `UserPoints`
									WHERE SteamID = uwe.SteamID AND MatchTypeID = 1
									".$whereMatchMode."
											".$whereMatchType."
													";
			$sql = "SELECT uwe.*, u.*, SUM(uc.Vote) as Credits, ((".$sqlPointsEarned.")+u.BasePoints) as Rank
					FROM `UserWonEvents` uwe
					JOIN User u ON u.SteamID = uwe.SteamID
					LEFT JOIN UserCredits uc ON uwe.SteamID = uc.SteamID
					WHERE uwe.EventID = ".(int)$eventID."
							Group BY uwe.SteamID
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);

			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID 0";
		}
		$ret['debug'] .= "End getWinnerOfEvent <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getUserWithMostEventWins(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getUserWithMostEventWins <br>\n";

		$sql = "SELECT uwe.*, Count(uwe.SteamID) as Wins
						FROM `UserWonEvents` uwe
						GROUP BY uwe.SteamID
				ORDER BY Wins DESC
				LIMIT 5
		";
		$data = $DB->multiSelect($sql); 
		$ret['debug'] .= p($sql,1);
		
		// für jeden spieler die daten auslesen
		if(is_array($data) && count($data) > 0){
			$User = new User();
			$UserCredits = new UserCredits();
			
		   foreach ($data as $k => $v) {
		   		$steamID = $v['SteamID'];
				$userData = $User->getUserData($steamID);
				$ret['debug'] .= p($userData,1);
				$data[$k] = array_merge($data[$k], $userData);
				
				$userCreditsData = $UserCredits->getCreditCountOfPlayer($steamID);
				$data[$k]['Credits'] = $userCreditsData['data'];
		  }
		}
		
		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getUserWithMostEventWins <br>\n";
		return $ret;
	}
}

?>