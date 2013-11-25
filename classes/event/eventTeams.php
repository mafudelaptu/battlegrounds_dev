<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class EventTeams{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertPlayerIntoTeam($eventID, $createdEventID, $teamID, $steamID, $elo, $round){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertPlayerIntoTeam <br>\n";

		if($eventID > 0 && $createdEventID > 0 && $teamID > 0 && $steamID > 0 && $round > 0){
			$insertArray = array();
			$insertArray['EventID'] = (int) $eventID;
			$insertArray['CreatedEventID'] = (int) $createdEventID;
			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['EventTeamID'] = (int) $teamID;
			$insertArray['Elo'] = (int) $elo;
			$insertArray['Round'] = (int) $round;
			$retIns = $DB->insert("EventTeams", $insertArray);

			$ret['status'] = $retIns;
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | teamID = 0 | steamID = 0 round = 0";
		}

		$ret['debug'] .= "End insertPlayerIntoTeam <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getFirstRoundTeams($eventID, $createdEventID, $minSubmissions){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getFirstRoundTeams <br>\n";
		if($eventID > 0 && $createdEventID > 0 && $minSubmissions > 0){

			switch($minSubmissions){
				case "20":
					$teamData1 = $this->getTeam($eventID, $createdEventID, 1);
					$teamData2 = $this->getTeam($eventID, $createdEventID, 2);
					$retArray[0][1] = $teamData1['data'];
					$retArray[0][2] = $teamData2['data'];

					$teamData3 = $this->getTeam($eventID, $createdEventID, 3);
					$teamData4 = $this->getTeam($eventID, $createdEventID, 4);
					$retArray[1][1] = $teamData3['data'];
					$retArray[1][2] = $teamData4['data'];
					break;
			}

			$ret['data']  =$retArray;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | minSubmissions = 0";
		}

		$ret['debug'] .= "End getFirstRoundTeams <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getSecondRoundTeams($eventID, $createdEventID, $minSubmissions){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getSecondRoundTeams <br>\n";
		if($eventID > 0 && $createdEventID > 0 && $minSubmissions > 0){
	
			switch($minSubmissions){
				case "10":
					$teamData1 = $this->getTeam($eventID, $createdEventID, 5);
					$teamData2 = $this->getTeam($eventID, $createdEventID, 6);
					$retArray[1] = $teamData1['data'];
					$retArray[2] = $teamData2['data'];
					break;
			}
	
			$ret['data']  =$retArray;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | minSubmissions = 0";
		}
	
		$ret['debug'] .= "End getSecondRoundTeams <br>\n";
	
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getTeam($eventID, $createdEventID, $teamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getTeam <br>\n";
		if($eventID > 0 && $createdEventID > 0 && $teamID > 0){
			$sql = "SELECT *
					FROM `EventTeams`
					WHERE EventID = ".(int) $eventID." AND CreatedEventID = ".(int) $createdEventID."
							AND EventTeamID = ".(int) $teamID."
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			// je nachdem ob gerade oder ungerade später für matchdetails team bestimmen (Radiant oder Dire)
			if($teamID%2==0){
				$t = 2;
			}
			else{
				$t = 1;
			}
			if(is_array($data) && count($data) > 0){
				foreach($data as $k =>$v){
					$data[$k]['TeamID'] = $t;
				}
			}
			$ret['data']  = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | teamID = 0";
		}

		$ret['debug'] .= "End getTeam <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getTeamOfUser($eventID, $createdEventID, $round, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getTeamOfUser <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($eventID > 0 && $createdEventID > 0){
			$sql = "SELECT *
					FROM `EventTeams`
					WHERE EventID = ".(int) $eventID." AND CreatedEventID = ".(int) $createdEventID." 
							AND SteamID = ".secureNumber($steamID)." AND Round = ".(int) $round."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0";
		}

		$ret['status'] = true;


		$ret['debug'] .= "End getTeamOfUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getTeamMembers($eventID, $createdEventID, $teamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getTeamMembers <br>\n";
		if($eventID > 0 && $createdEventID > 0 && $teamID > 0){
			$sql = "SELECT et.*, u.Name as Name, u.Avatar
					FROM `EventTeams` et JOIN `User` u ON et.SteamID = u.SteamID 
					WHERE EventID = ".(int) $eventID." AND CreatedEventID = ".(int) $createdEventID." AND EventTeamID = ".(int) $teamID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);
			
			$ret['data'] = $data;
			$ret['status'] = true;
			
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | teamID = 0";
		}

		$ret['debug'] .= "End getTeamMembers <br>\n";

		return $ret;
	}
}

?>