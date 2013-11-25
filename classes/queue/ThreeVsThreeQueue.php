<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class ThreeVsThreeQueue extends Queue{

	const matchType = 9; // 3vs3QueueID in MatchType-DB
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function joinQueue($matchmodes, $regions, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$UserElo = new UserElo();

		if($steamID == 0){
			$steamID = $_SESSION['user']["steamID"];
		}


		//p($matchmodes);
		if($steamID > 0){
			if($this->userNotAlreadyInQueue($steamID)){
				$Match = new Match();
				$playerInMatch = $Match->isPlayerInMatch();
				if(!$playerInMatch){
					if(is_array($matchmodes) AND count($matchmodes)>0){
						$insertStatement = "";
						$insertArray = array();
						//$_SESSION['elo'] = array();
						$UserPoints = new UserPoints();
						$retUP = $UserPoints->getGlobalPointsOfUser($steamID, ThreeVsThreeQueue::matchType);
						$points = $retUP['data'];
						foreach($matchmodes as $k => $v){
							$matchMode = (int)$v;

							

							if(is_array($regions) AND count($regions)>0){
									
								foreach($regions as $kr => $vr){
									$regionID = $vr;

									$tmpArray['SteamID'] = secureNumber($steamID);
									$tmpArray['MatchTypeID'] = ThreeVsThreeQueue::matchType;
									$tmpArray['MatchModeID'] = (int) $matchMode;
									$tmpArray['Elo'] = (int)$points;
									$tmpArray['Timestamp'] = time();
									$tmpArray['Region'] = (int) $regionID;
									$tmpArray['ForceSearch'] = 0;

									$insertArray[] = $tmpArray;
									// Session für matchMaiking setzen, damit die Elo's nciht erneut ausgelesen werden müssen
									$_SESSION['regions'][$matchMode] = $regionID;
								}
							}

							// Session für matchMaiking setzen, damit die Elo's nciht erneut ausgelesen werden müssen
						//	$_SESSION['elo'][$matchMode] = (int)$data['Elo'];
							$_SESSION['points'] = (int) $points;
						}
							
						$DB->multiInsert("Queue",$insertArray);
							
						$ret['sql'] = $sql;
						$ret['status'] = true;
					}
				}
				else{
					$ret['status'] = "inMatch";
				}
			}
			else{
				$ret['debug'] .= "user already in Queue!";
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
	function getPlayersInQueue(){
		$ret = array();

		$DB = new DB();
		$con = $DB->conDB();

		$sql = "SELECT DISTINCT SteamID
				FROM Queue
				WHERE MatchTypeID = ".(int) ThreeVsThreeQueue::matchType."
						";
		$count = $DB->countRows($sql);

		$sql = "SELECT DISTINCT SteamID
				FROM QueueGroupMembers
				WHERE MatchTypeID = ".(int) ThreeVsThreeQueue::matchType."
						";
		$count2 = $DB->countRows($sql);
		$count = $count + $count2;
		$ret['count'] = $count;

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPlayersInMatchCount(){
		$ret = array();

		$DB = new DB();
		$con = $DB->conDB();

		$sql = "SELECT DISTINCT MatchID
				FROM `Match`
				WHERE MatchTypeID = ".(int) ThreeVsThreeQueue::matchType."
						AND TeamWonID = -1
						AND TimestampClosed = 0
						AND ManuallyCheck != 1
						AND Canceled != 1
						";
		$count = $DB->countRows($sql);

		$ret['count'] = $count*6;

		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchModeCounts($moreInfo = false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
	
		$sql = "SELECT DISTINCT q.MatchModeID, SteamID, mm.Name as MatchModeName, mm.Shortcut as MatchModeSc
				FROM Queue q JOIN MatchMode mm ON mm.MatchModeID = q.MatchModeID
				WHERE MatchTypeID = ".ThreeVsThreeQueue::matchType."
						GROUP BY MatchModeID, SteamID
						";
		$data = $DB->multiSelect($sql);
	
		if(!is_array($data) OR count($data) == 0){
			$data = array();
		}
	
		$sql = "SELECT DISTINCT qgm.MatchModeID, SteamID, mm.Name as MatchModeName, mm.Shortcut as MatchModeSc
				FROM QueueGroupMembers qgm JOIN MatchMode mm ON mm.MatchModeID = qgm.MatchModeID
				WHERE MatchTypeID = ".ThreeVsThreeQueue::matchType."
						GROUP BY qgm.MatchModeID, SteamID
	
				";
		$data2 = $DB->multiSelect($sql);
	
		if(!is_array($data2) OR count($data2) == 0){
			$data2 = array();
		}
	
		$data = array_merge($data,$data2);
		// Array in eine bestimmte struktur bringen
		/*
			* Array{
		* 	[{MatchModeID}] => {Count}
		* 	...
		* }
		*/
		if(is_array($data) && count($data) > 0){
			foreach($data as $k => $v){
				if($moreInfo){
					$tmpMoreInfo[$v['MatchModeID']]['MatchModeName'] = $v['MatchModeName'];
					$tmpMoreInfo[$v['MatchModeID']]['Shortcut'] = $v['MatchModeSc'];
					$tmpMoreInfo[$v['MatchModeID']]['Count']++;
				}
					
				$tmpData[$v['MatchModeID']]++;
	
			}
		}
		if($moreInfo){
			$ret['statsData'] = $tmpMoreInfo;
			// max Wert bei welchem Matchmode
			if(is_array($tmpData) && count($tmpData) > 0){
			arsort($tmpData);
			$ret['debug'] .= p($tmpData,1);
			$maxKey = key($tmpData);
			$ret['debug'] .= p($maxKey,1);
			$matchModeName = $tmpMoreInfo[$maxKey]['MatchModeName'];
			$matchModeCount = $tmpMoreInfo[$maxKey]['Count'];
			$ret['maxMM'] = $matchModeName;
			$ret['maxMMCount'] = $matchModeCount;
			}
		}
	
	
		$ret['data'] = $tmpData;
	
		$ret['status'] = true;
	
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getRegionCounts($moreInfo = false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
	
		$sql = "SELECT DISTINCT q.Region, SteamID, r.Name as RegionName, r.Shortcut as RegionSc
				FROM Queue q JOIN Region r ON q.Region = r.RegionID
				WHERE MatchTypeID = ".ThreeVsThreeQueue::matchType."
						GROUP BY Region,SteamID
						";
		$data = $DB->multiSelect($sql);
	
		// Array in eine bestimmte struktur bringen
		/*
			* Array{
		* 	[{MatchModeID}] => {Count}
		* 	...
		* }
		*/
		if(is_array($data) && count($data) > 0){
			foreach($data as $k => $v){
				if($moreInfo){
					$tmpMoreInfo[$v['Region']]['RegionName'] = $v['RegionName'];
					$tmpMoreInfo[$v['Region']]['Shortcut'] = $v['RegionSc'];
					$tmpMoreInfo[$v['Region']]['Count']++;
				}
				$tmpData[$v['Region']]++;
			}
		}
		if($moreInfo){
			$ret['statsData'] = $tmpMoreInfo;
			// max Wert bei welcher Region
			if(is_array($tmpData) && count($tmpData) > 0){
				arsort($tmpData);
				$ret['debug'] .= p($tmpData,1);
				$maxKey = key($tmpData);
				$ret['debug'] .= p($maxKey,1);
				$regionName = $tmpMoreInfo[$maxKey]['RegionName'];
				$regionCount = $tmpMoreInfo[$maxKey]['Count'];
				$ret['maxR'] = $regionName;
				$ret['maxRCount'] = $regionCount;
			}
			
		}
		$ret['data'] = $tmpData;
		$ret['status'] = true;
	
		return $ret;
	}
	
}

?>