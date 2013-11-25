<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class SingleQueueGroup extends Queue{
	const matchType = 1; // SingleQueueID in MatchType-DB
	/*
	* Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function joinQueueAsGroup($groupID, $matchmodes, $regions){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start joinQueueAsGroup <br>\n";
		if($groupID > 0){

			if($steamID == 0){
				$steamID = $_SESSION['user']['steamID'];
			}



			$ret['debug'] .= p("groupID:".$groupID,1);
			// gruppenmitglieder auslesen
			$Group = new Group();
			$data = $Group->getGroupMembers($groupID);
				
			// test some cases
			if(is_array($data['data']) && count($data['data']) > 0){
				foreach ($data['data'] as $k => $v) {
					$tmpID = $v['SteamID'];
					$ret['debug'] .= p("sID:".$tmpID,1);
					$retCheck = $this->checkIfAlreadyInQueueWithGroup($tmpID);
					$ret['debug'] .= p($retCheck,1);
					if($retCheck['inQueue']){
						if($retCheck['GroupID'] != $groupID){
							$ret['status'] = "inQueue";
							return $ret;
						}
					}
						
					$Match = new Match();
					$inMatch = $Match->isPlayerInMatch(0, $tmpID);
					$ret['debug'] .= p("inMatch:".$inMatch,1);
					if($inMatch){
						$ret['status'] = "inMatch";
						return $ret;
					}
				}
			}
				
			$ret['debug'] .= p($data,1);
			if(is_array($matchmodes) && count($matchmodes) > 0){
				//$_SESSION['elo'] = array();
				foreach($matchmodes as $k =>$v){
					$matchModeID = $v;
					if(is_array($regions) && count($regions) > 0){
						foreach($regions as $kk =>$vv){
							$regionID = $vv;

							// in Queue eintragen
							$this->insertGroupInQueue($groupID, SingleQueueGroup::matchType, $matchModeID, $regionID);

							// Session für matchMaiking setzen, damit die Elo's nciht erneut ausgelesen werden müssen
							$_SESSION['regions'][$matchModeID] = $regionID;
						}
					}

					// QueueGroupMembers eintragen
					$retInsQGM = $this->insertQueueGroupMembers($groupID, $matchModeID, $data['data']);
					$ret['debug'] .= p($retInsQGM,1);

				}
			}
			$ret['status'] = true;
				
		}
		else{
			$ret['status'] = "groupID = 0";
		}

		$ret['debug'] .= "End joinQueueAsGroup <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertGroupInQueue($groupID, $matchTypeID, $matchModeID, $regionID, $forceSearch="0"){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertGroupInQueue <br>\n";
		if($groupID > 0){
			$data = array();
			$data['GroupID'] = (int) $groupID;
			$data['MatchTypeID'] = (int) $matchTypeID;
			$data['MatchModeID'] = (int) $matchModeID;
			$data['Region'] = (int) $regionID;
			$data['Timestamp'] = (int) time();
			$data['ForceSearch'] = (int) $forceSearch;

			$insRet = $DB->insert("QueueGroup", $data);
			if($insRet){
				$ret['status'] = true;
			}
			else{
				$ret['status'] = "QueueGroup insert net geklappt";
			}

		}
		else{
			$ret['status'] = "groupID = 0";
		}

		$ret['debug'] .= "End insertGroupInQueue <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertQueueGroupMembers($groupID, $matchModeID, $data){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertQueueGroupMembers <br>\n";
		if($groupID > 0){
			$insArray = array();
			if(is_array($data) && count($data) > 0){


				foreach($data as $k =>$v){
					// 					$ret['debug'] .= p("HIER!!",1);

					// 					$ret['debug'] .= p("Zuende!!",1);
					$steamID = $v['SteamID'];
					// Elo von User auslesen
					$UserPoints = new UserPoints();
					$retUP = $UserPoints->getGlobalPointsOfUser($steamID, SingleQueueGroup::matchType);
					$points = $retUP['data'];

					$ret['debug'] .= p($steamID." ".SingleQueueGroup::matchType." ".$matchModeID,1);

					$insertArray = array();
					$insertArray['GroupID'] = (int) $groupID;
					$insertArray['SteamID'] = secureNumber($steamID);
					$insertArray['MatchTypeID'] = (int) SingleQueueGroup::matchType;
					$insertArray['MatchModeID'] = (int) $matchModeID;
					$insertArray['Elo'] = (int) $points;

					$insArray[] = $insertArray;

					// Session für matchMaiking setzen, damit die Elo's nciht erneut ausgelesen werden müssen
					//$_SESSION['elo'][$matchModeID] = (int)$elo;
					$_SESSION['points'] = (int) $points;
				}
				$ret['debug'] .= $DB->multiInsert("QueueGroupMembers", $insArray,1);
				$retMIns = $DB->multiInsert("QueueGroupMembers", $insArray);
				$ret['status'] = $retMIns;
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End insertQueueGroupMembers <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function cleanQueueGroup($groupID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start cleanQueueGroup <br>\n";
		if($groupID > 0){

			$sql = "DELETE FROM `QueueGroup`
					WHERE GroupID = ".(int)$groupID."
							";
			$ret['debug'] .= p($sql,1);
			$retD = $DB->delete($sql);

			$sql = "DELETE FROM `QueueGroupMembers`
					WHERE GroupID = ".(int)$groupID."
							";
			$ret['debug'] .= p($sql,1);
			$retD = $DB->delete($sql);
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "groupID = 0";
		}

		$ret['debug'] .= "End cleanQueueGroup <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkIfAlreadyInQueueWithGroup($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkIfAlreadyInQueueWithGroup <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT DISTINCT GroupID
					FROM `QueueGroupMembers`
					WHERE SteamID = ".secureNumber($steamID)."
							LIMIT 1
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			if(is_array($data) && count($data) > 0){
				$ret['inQueue'] = true;
				$ret['GroupID'] = $data['GroupID'];
			}
			else{
				$ret['inQueue'] = false;
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End checkIfAlreadyInQueueWithGroup <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function cleanQueueGroupByPlayer($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertNewUser <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){

			$retGroup = $this->checkIfAlreadyInQueueWithGroup($steamID);
			$ret['debug'] .= p($retGroup,1);
			$groupID = $retGroup['GroupID'];

			$retClean = $this->cleanQueueGroup($groupID);
			$ret['debug'] .= p($retClean,1);
			$ret['data']  =$data;
			$ret['status'] = $retClean['status'];
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End insertNewUser <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkAbleToJoinDuo($groupID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkAbleToJoinDuo <br>\n";

		if($groupID > 0){
			
			// gruppenmitglieder auslesen
			$Group = new Group();
			$data = $Group->getGroupMembers($groupID);
			
			// test some cases
			if(is_array($data['data']) && count($data['data']) > 0){
				foreach ($data['data'] as $k => $v) {
					$tmpID = $v['SteamID'];
					$ret['debug'] .= p("sID:".$tmpID,1);
					$retCheck = $this->checkIfAlreadyInQueueWithGroup($tmpID);
					$ret['debug'] .= p($retCheck,1);
					if($retCheck['inQueue']){
						if($retCheck['GroupID'] != $groupID){
							$ret['status'] = "inQueue";
							return $ret;
						}
					}
			
					$Match = new Match();
					$inMatch = $Match->isPlayerInMatch(0, $tmpID);
					$ret['debug'] .= p("inMatch:".$inMatch,1);
					if($inMatch){
						$ret['status'] = "inMatch";
						return $ret;
					}
				}
			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "groupID == 0";
		}
		$ret['debug'] .= "End checkAbleToJoinDuo <br>\n";
		return $ret;
	}
}
	?>