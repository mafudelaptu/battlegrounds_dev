<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class EventSubmissions{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPlayersSubmissionOfEvent($eventID, $matchModeID=0, $matchTypeID=0, $justReady=true){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPlayersSubmissionOfEvent <br>\n";
		if($eventID > 0){
			$whereReady = "";
			if($justReady){
				$whereReady = " AND Ready = 1";
			}
			if($matchModeID > 0 && $matchTypeID > 0){
				$sqlPoints = "
						SELECT IF(SUM(PointsChange)+u.BasePoints > 0, SUM(PointsChange)+u.BasePoints, u.BasePoints) as Points
						FROM `UserPoints`
						WHERE SteamID = es.SteamID
						";
				$sql = "SELECT es.*, u.Name as Name, u.Avatar as Avatar, (".$sqlPoints.") as Elo, @curRow := @curRow + 1 AS rowNumber
						FROM `EventSubmissions` es JOIN User u ON u.SteamID = es.SteamID

						JOIN (SELECT @curRow := 0) r
						WHERE EventID = ".(int)$eventID." ".$whereReady."
								ORDER BY Timestamp ASC
								";
			}
			else{
				$sql = "SELECT *
						FROM `EventSubmissions`
						WHERE EventID = ".(int)$eventID." ".$whereReady."
								";
			}

			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			$ret['data']  = $data;
			$ret['count'] = (int) count($data);
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End getPlayersSubmissionOfEvent <br>\n";

		return $ret;

	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function joinEvent($eventID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start joinEvent <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}


		if($eventID > 0){

			// vorher checken ob ers darf
			$Event = new Event();
			$retE = $Event->checkRequirementsOfPlayer($steamID, $eventID);
			$allowed = $retE['status'];
				
			if($allowed){
				$insertArray = array();

				$insertArray['SteamID'] = secureNumber($steamID);
				$insertArray['EventID'] = (int)$eventID;
				$insertArray['CreatedEventID'] = 0;
				$insertArray['Timestamp'] = time();

				$retINs = $DB->insert("EventSubmissions", $insertArray);
				$ret['status'] = $retINs;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End joinEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkIfPlayerAlreadySignedIn($eventID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkIfPlayerAlreadySignedIn <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($eventID > 0){

			$sql = "SELECT *
					FROM `EventSubmissions`
					WHERE SteamID = ".secureNumber($steamID)." AND EventID = ".(int) $eventID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$count = count($data);
			$ret['debug'] .= p($count,1);
			if($count > 1){
				$ret['data'] = $data;
				$ret['status'] = true;
			}
			else{
				$ret['data'] = $data;
				$ret['status'] = false;
			}

		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End checkIfPlayerAlreadySignedIn <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function singOutPlayerOfEvent($eventID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start singOutPlayerOfEvent <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($eventID > 0){

			$sql = "DELETE FROM `EventSubmissions`
					WHERE SteamID = ".secureNumber($steamID)." AND EventID = ".(int) $eventID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->delete($sql);
			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End singOutPlayerOfEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getSubmissionCountOfEvent($eventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getSubmissionCountOfEvent <br>\n";
		if($eventID > 0){

			$sql = "SELECT SteamID
					FROM `EventSubmissions`
					WHERE EventID = ".(int) $eventID." AND Ready = 1
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->countRows($sql);

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End getSubmissionCountOfEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function chunkSingedInPlayersInEvent($eventID, $minSubmissions, $matchModeID, $matchTypeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start chunkSingedInPlayersInEvent <br>\n";
		if($eventID > 0){

			$retPlayers = $this->getPlayersSubmissionOfEvent($eventID, $matchModeID, $matchTypeID);
			$ret['debug'] .= p($retPlayers,1);
			$data = $retPlayers['data'];

			if(is_array($data) && count($data) > 0){
				$chuckedArray = array_chunk($data, $minSubmissions);
				$ret['data']  = $chuckedArray;
				$ret['status'] = true;
			}
			else{
				$ret['data']  = $data;
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End chunkSingedInPlayersInEvent <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function updateCreatedEventValueOfPlayer($steamID, $eventID, $createdEventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start updateCreatedEventValueOfPlayer <br>\n";
		if($steamID > 0 && $eventID > 0){
			$sql = "UPDATE `EventSubmissions`
					SET CreatedEventID = ".(int) $createdEventID."
							WHERE SteamID = ".secureNumber($steamID)." AND EventID = ".(int)$eventID."
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->update($sql);
			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "steamID = 0 OR eventID > 0";
		}

		$ret['debug'] .= "End updateCreatedEventValueOfPlayer <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getCreatedMatchOfPlayer($eventID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getCreatedMatchOfPlayer <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']["steamID"];
		}

		if($eventID > 0){

			$sql = "SELECT *
					FROM `EventSubmissions`
					WHERE SteamID = ".secureNumber($steamID)." AND EventID = ".(int) $eventID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			$ret['data']  =$data['CreatedEventID'];
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End getCreatedMatchOfPlayer <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGlobalEventSubmissionCount(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getGlobalEventSubmissionCount <br>\n";

		$sql = "SELECT *
				FROM `EventSubmissions` es JOIN `Events` e ON e.EventID = es.EventID
				WHERE EndTimestamp != 0 AND Canceled = 0
				";
		$data = $DB->countRows($sql);
		$ret['debug'] .= p($sql,1);

		$ret['data'] = $data;
		$ret['status'] = true;


		$ret['debug'] .= "End getGlobalEventSubmissionCount <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGlobalEventPlayersPlayedCount(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getGlobalEventSubmissionCount <br>\n";

		$sql = "SELECT es.*
				FROM `EventSubmissions` es JOIN `Events` e ON e.EventID = es.EventID
				WHERE EndTimestamp != 0 AND Canceled = 0 AND es.CreatedEventID > -1
				";
		$data = $DB->countRows($sql);
		$ret['debug'] .= p($sql,1);

		$ret['data'] = $data;
		$ret['status'] = true;


		$ret['debug'] .= "End getGlobalEventSubmissionCount <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function isPlayerReadyForEvent($eventID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start isPlayerReady <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']["steamID"];
		}
		
		if($eventID > 0 && $steamID > 0){
			
			$sql = "SELECT *
							FROM `EventSubmissions` 
							WHERE SteamID = ".secureNumber($steamID)." AND EventID = ".(int) $eventID."
			";
			$count = $DB->countRows($sql);
			$data = $DB->select($sql); 
			$ret['debug'] .= p($sql,1);
			$ret['debug'] .= p($count,1);
			if($count > 0){
				$ret['status'] = (int) $data['Ready'];
			}
			else{
				$ret['status'] = (int) -1;
			}
			
		}
		else{
			$ret['status'] = "eventID == 0 || steamID == 0";
		}
		$ret['debug'] .= "End isPlayerReady <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function setReadyStatus($eventID, $status, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start isPlayerReady <br>\n";
		
		if($steamID == 0){
			$steamID = $_SESSION['user']["steamID"];
		}
		
		if($eventID > 0 && $steamID > 0){
				
			$sql = "UPDATE `EventSubmissions`
					SET Ready = ".(int) $status."
					WHERE SteamID = ".secureNumber($steamID)." AND EventID = ".(int) $eventID."
			";
			$data = $DB->update($sql);
			$ret['debug'] .= p($sql,1);
				
			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "eventID == 0 || steamID == 0";
		}
		$ret['debug'] .= "End isPlayerReady <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function throwOutPlayersOutOfEventWhoDontRdy($eventID){
		$ret = array();
				$DB = new DB();
				$con = $DB->conDB();
				$ret['debug'] .= "Start throwOutPlayersOutOfEventWhoDontRdy <br>\n";
		
				if($eventID > 0){
					$sql = "SELECT *
									FROM `EventSubmissions` 
									WHERE EventID = ".(int)$eventID." AND (Ready = 0 OR Ready = -1)
					";
					$data = $DB->multiSelect($sql); 
					$ret['debug'] .= p($sql,1);
					
					if(is_array($data) && count($data) > 0){
						foreach($data as $k =>$v){
							$steamID = $v['SteamID'];
							$retUpdate = $this->updateCreatedEventValueOfPlayer($steamID, $eventID, -2);
						}
					}
					
					
					$ret['status'] = true;
				}
				else{
					$ret['status'] = "eventID == 0";
				}
				$ret['debug'] .= "End throwOutPlayersOutOfEventWhoDontRdy <br>\n";
				return $ret;
	}
	
}

?>