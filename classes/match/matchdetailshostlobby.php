<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class MatchDetailsHostLobby{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function setLobbyHostForMatch($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start setLobbyHostForMatch <br>\n";
		$log = new KLogger ( "log.txt" , KLogger::INFO );
				
		if($matchID > 0){
			
			// get all PLayers
			$MatchDetails = new MatchDetails();
			$dataRet = $MatchDetails->getAllPlayersInMatch($matchID);
			$data = $dataRet['data'];
			
			// ordentliches Array umschreiben
			if(is_array($data) && count($data) > 0){
				$userArray = array();
				foreach($data as $k =>$v){
					$userArray[] = $v['SteamID'];
				}
				
				// random bob auswählen
				if(is_array($userArray) && count($userArray) > 0){
					$keyUser = array_rand($userArray);
					$hostSteamID = $userArray[$keyUser];
					
					if($hostSteamID > 0){
						$insertArray = array();
						
						$insertArray['MatchID'] = (int)$matchID;
						$insertArray['SteamID'] = secureNumber($hostSteamID);
						$ret['debug'] .= p($insertArray,1);
						$retDB = $DB->insert("MatchDetailsHostLobby", $insertArray);
						$ret['debug'] .= p($retDB,1);
						$ret['status'] = true;
					}
					else{
						$log->LogInfo(__FUNCTION__." - HostSteamID ist 0! initiated by:".$_SESSION['user']["steamID"]);
						$ret['status'] = false;
					}
					
				}
				else{
					$log->LogInfo(__FUNCTION__." - Random Bob konnte net bestimmt werden (Array leer)! initiated by:".$_SESSION['user']["steamID"]);
					$ret['status'] = false;
				}
					
				
			}
			else{
				$log->LogInfo(__FUNCTION__." - MatchDetails zu Match: ".$matchID." nicht vorhanden! initiated by:".$_SESSION['user']["steamID"]);
				$ret['status'] = false;
			}
			
		}
		else{
			$ret['status'] = "MatchID = 0";
		}
		
		$ret['debug'] .= "End setLobbyHostForMatch <br>\n";
		
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function cleanHostForMatch($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertNewUser <br>\n";
		
		$log = new KLogger ( "log.txt" , KLogger::INFO );

				
		
		if($matchID > 0){
			$sql = "DELETE FROM `MatchDetailsHostLobby`
					WHERE MatchID = ".(int) $matchID."
					";
			$log->LogInfo(__FUNCTION__.": clean  Host for Match: ".$matchID." - initiated by:".$_SESSION['user']["steamID"]);
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}
		
		$ret['debug'] .= "End insertNewUser <br>\n";
		
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getHostForMatch($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertNewUser <br>\n";
		if($matchID > 0){
			$sql = "SELECT *
					FROM `MatchDetailsHostLobby` mdhl LEFT JOIN User u ON u.SteamID = mdhl.SteamID
					WHERE MatchID = ".(int) $matchID."
					";
			$data = $DB->select($sql);
			
			$ret['data'] = $data;
			
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}
		
		$ret['debug'] .= "End insertNewUser <br>\n";
		
		return $ret;
	}
	
}

?>