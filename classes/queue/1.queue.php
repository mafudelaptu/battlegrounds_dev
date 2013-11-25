<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Queue{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function leaveQueue($individualUser=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		if($individualUser){
			$steamID = $individualUser;
		}
		else{
			$steamID = $_SESSION['user']["steamID"];
		}

		if($steamID > 0){
			$sql = "SELECT SteamID
					FROM SingleQueue
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$counts["SingleQueue"] = $DB->countRows($sql);
			//p($count);
			if(is_array($counts) AND count($counts)>0){
				foreach($counts as $k => $v){
					if($v > 0){
						$sql = "DELETE FROM ".$k."
								WHERE SteamID = ".secureNumber($steamID)."
										";
						$DB->delete($sql,0);
					}
				}
			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "not logged in!";
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function inQueue($steamID = 0){
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID == 0){
			$steamID = $_SESSION['user']["steamID"];
		}

		if($steamID > 0){
			$sql = "SELECT SteamID
					FROM Queue
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$count = $DB->countRows($sql);

			$sql = "SELECT *
					FROM `QueueGroupMembers`
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$ret['debug'] .= p($sql,1);
			$count2 = $DB->countRows($sql);

			//p($count);
			if($count > 0 || $count2 > 0){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function leaveQueue2($individualUser=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		// Sessions löschen wenn von matchmaking kommt
		unset($_SESSION['queue']);
		unset($_SESSION['erweiterteSuche']);
		unset($_SESSION['range']);

		if($individualUser){
			$steamID = $individualUser;
		}
		else{
			$steamID = $_SESSION['user']["steamID"];
		}

		if($steamID > 0){
			//p($count);
			$sql = "DELETE FROM Queue
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$DB->delete($sql,0);
			// queue timestamp zur�cksetzen
			$_SESSION['user']['joinTimestamp'] = 0;

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "not logged in!";
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function kickAllPlayersOutOfQueue($matchID, $reason){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		if($matchID > 0){
			$Match = new Match();
			$retM = $Match->isPlayerInMatch($matchID);
			if($retM){
				// Sessions löschen wenn von matchmaking kommt
				unset($_SESSION['queue']);
				unset($_SESSION['erweiterteSuche']);
				unset($_SESSION['range']);

				$log = new KLogger ( "log.txt" , KLogger::INFO );
				$log->LogInfo("kick all Player out of Queue! initiated by: ".$_SESSION['user']['steamID']." for match:".$matchID." reason:".$reason);	//Prints to the log file

					

					
				// Spieler auslesen die gefunden wurden

				$sql = "SELECT DISTINCT SteamID
						FROM MatchTeams
						WHERE MatchID = ".(int) $matchID."
								";
				$data = $DB->multiSelect($sql);
				$ret['debug'] .= p($data,1);
				// wenn nciht schon vorher gel�scht
				if(is_array($data) && count($data) > 0){
					$SingleQueueGroup = new SingleQueueGroup();

					foreach($data as $k =>$v){
						$steamID = $v['SteamID'];

						$sql = "DELETE FROM Queue
								WHERE SteamID = ".secureNumber($steamID)."
										";
						$DB->delete($sql,0);
						$ret['debug'] .= p($sql,1);

						// alle Gruppen l�schen die dabei waren
						$retCleanGroup = $SingleQueueGroup->cleanQueueGroupByPlayer($steamID);
						$ret['debug'] .= p($retCleanGroup,1);
					}
				}
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}

		}
		else{
			$ret['status'] = "not logged in!";
		}
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function updateForceSearch($forceSearch, $steamID = "", $matchTypeID=1){
		$ret = array();
		$ret['debug'] .= "Start updateForceSearch \n";

		$ret['debug'] .= "Force:".$forceSearch." \n";

		if($steamID == ""){
			$steamID = $_SESSION['user']['steamID'];
		}

		$ret['debug'] .= "Force:".$forceSearch." \n";

		if($steamID > 0){
			$DB = new DB();
			$con = $DB->conDB();

			if($forceSearch == "true"){

				$retQMT = $this->getQueueMatchType($steamID);
				$matchTypeID = $retQMT['data'];
				
				$UserSkillBracket = new UserSkillBracket();
				$retUSB = $UserSkillBracket->getSkillBracketOfUser($steamID, $matchTypeID);
				if($retUSB['data']['SkillBracketTypeID'] != "1"){
					$forceSearch = 1;
				}
				else{
					$forceSearch = 0;
				}
				
			}
			else{
				$forceSearch = 0;
			}

			$sql = " UPDATE Queue
					SET ForceSearch = ".(int) $forceSearch."
							WHERE SteamID = ".secureNumber($steamID)."
									";
			$DB->update($sql);

			$sql = "SELECT DISTINCT GroupID
					FROM `QueueGroupMembers`
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);
			$groupID = $data['GroupID'];

			if($groupID > 0){
				$sql = " UPDATE QueueGroup
						SET ForceSearch = ".(int) $forceSearch."
								WHERE GroupID = ".(int)$groupID."
										";
				$DB->update($sql);
			}

			$ret['sql'] = $sql;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = false;
		}

		$ret['debug'] .= "END updateForceSearch \n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getInQueueCount(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$sql="SELECT SteamID
				FROM `Queue`
				GROUP BY SteamID
				";
		$count = $DB->countRows($sql);

		$ret['count'] = $count;
		$ret['status'] = true;

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function kickPlayersOutOfQueueIf10PlayersFound($data) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "START kickPlayersOutOfQueueIf10PlayersFound \n";
		$ret['debug'] .= p($data,1);
		$log = new KLogger ( "log.txt" , KLogger::INFO );

		if(is_array($data) && count($data) > 0){
			foreach ($data as $k => $v) {
				$matchMode = $k;
				$log->LogInfo("löschen für matchModeID:".$matchMode);	//Prints to the log file

				$data2 = $v;
				if(is_array($data2) && count($data2) > 0){
					foreach($data2 as $p_k =>$p_v){
						$steamID = $p_v['SteamID'];
						$log->LogInfo("loesche Spieler: ".$steamID." as Queue");	//Prints to the log file
						$sql = "DELETE FROM Queue
								WHERE SteamID = ".secureNumber($steamID)."
										";
						$DB->delete($sql);
						// Gruppen auch l�schen
						$groupID = $p_v['GroupID'];
						$log->LogInfo("GroupID: ".$groupID." ");	//Prints to the log file
						if($groupID > 0){
							// Gruppe l�schen
							$sql = "DELETE FROM `QueueGroup`
									WHERE GroupID = ".(int)$groupID."
											";
							$ret['debug'] .= p($sql,1);
							$retDel = $DB->delete($sql);
							$sql = "DELETE FROM `QueueGroupMembers`
									WHERE GroupID = ".(int)$groupID."
											";
							$ret['debug'] .= p($sql,1);
							$retDel = $DB->delete($sql);

						}
					}
				}

			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID 0";
		}

		$ret['debug'] .= "END kickPlayersOutOfQueueIf10PlayersFound \n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function kickPlayersOutOfQueueIf10PlayersFound2($data) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "START kickPlayersOutOfQueueIf10PlayersFound \n";
		$ret['debug'] .= p($data,1);

		if(is_array($data) && count($data) > 0){
			foreach($data as $p_k =>$p_v){
				$steamID = $p_v['SteamID'];
				$sql = "DELETE FROM Queue
						WHERE SteamID = ".secureNumber($steamID)."
								";
				$ret['debug'] .= p($sql,1);
				$DB->delete($sql);
				// Gruppen auch l�schen
				$groupID = $p_v['GroupID'];
				if($groupID > 0){
					// Gruppe l�schen
					$sql = "DELETE FROM `QueueGroup`
							WHERE GroupID = ".(int)$groupID."
									";
					$ret['debug'] .= p($sql,1);
					$retDel = $DB->delete($sql);
					$sql = "DELETE FROM `QueueGroupMembers`
							WHERE GroupID = ".(int)$groupID."
									";
					$ret['debug'] .= p($sql,1);
					$retDel = $DB->delete($sql);
				}
			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID 0";
		}

		$ret['debug'] .= "END kickPlayersOutOfQueueIf10PlayersFound \n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function userNotAlreadyInQueue($steamID){
		$DB = new DB();
		$con = $DB->conDB();
		if($steamID > 0){
			$sql = "SELECT SteamID
					FROM `Queue`
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$count = $DB->countRows($sql);
			//p($count);
			if($count > 0){
				return false;
			}
			else{
				return true;
			}
		}
		else{
			return false;
		}
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-09-16
	*/
	function checkJoinQueue($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkJoinQueue <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){

			// leave Queue
			//$retQ = $this->leaveQueue2();

			// check Queue Lock
			$QueueLock = new QueueLock();
			$retQL = $QueueLock->checkLock($steamID);

			if($retQL['status'] == true){

				// Check if banned?
				$Banlist = new Banlist();
				$retPB = $Banlist->isUserPermaBanned($steamID);
				if($retPB['status'] == false){
					$retB = $Banlist->checkForBansOfPlayer($steamID);
					$ret['debug'] .= p($retB,1);
					if($retB['banned'] == false && $retB['display'] != 1){
							
						// checkIfAlreadyInQueueWithGroup
						$SingleQueueGroup = new SingleQueueGroup ();
						$retSQG = $SingleQueueGroup->checkIfAlreadyInQueueWithGroup ();
							
						if($retSQG['inQueue'] == false){

							$Match = new Match();
							$playerInMatch = $Match->isPlayerInMatch();
							if(!$playerInMatch){
								$ret['status'] = true;
							}
							else{
								$ret['status'] = "inMatch";
							}
						}
						else{
							$ret['GroupID'] = $retSQG['GroupID'];
							$ret['status'] = "inDuoQueue";
						}
							
					}
					else{
						// get Bann data to display
						$retBL = $Banlist->getCurrentBanDataOfPlayer($steamID);
						$ret['banned'] = $retBL['banned'];
						$ret['bannData'] = $retBL['data'];
						$ret['display'] = $retB['display'];
						$ret['banCounts'] = $retB['banCounts'];
						$ret['data'] = $retB['data'];
						$ret['status'] = "banned";
					}
				}
				else{
					$ret['status'] = "permaBanned";
				}

			}
			else{
				$ret['time'] = $retQL['time'];
				$ret['status'] = "queueLock";
			}
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End checkJoinQueue <br>\n";
		return $ret;
	}

	function getQueueMatchType($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getQueueMatchType <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
				
			$sql = "SELECT MatchTypeID
					FROM `Queue` q
					WHERE q.SteamID = ".secureNumber($steamID)."
							GROUP BY q.SteamID
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);
			
			if($data['MatchTypeID'] == ""){
				$sql = "SELECT MatchTypeID
					FROM `QueueGroupMembers` qgm
					WHERE qgm.SteamID = ".secureNumber($steamID)."
							GROUP BY qgm.SteamID
							";
				$data = $DB->select($sql);
				$ret['debug'] .= p($sql,1);
				
				$ret['data'] = (int) $data['MatchTypeID'];
				$ret['status'] = true;
				
			}else{
				$ret['data'] = (int) $data['MatchTypeID'];
				$ret['status'] = true;
			}
			
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getQueueMatchType <br>\n";
		return $ret;
	}

}

?>