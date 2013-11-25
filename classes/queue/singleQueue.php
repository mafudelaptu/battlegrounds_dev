<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class SingleQueue extends Queue{

	const matchType = 1; // SingleQueueID in MatchType-DB
	/*
	* Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function joinQueue(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$UserStats = new UserStats();
		$steamID = $_SESSION['user']["steamID"];
		$rank = $UserStats->getRank($steamID);

		if($this->userNotAlreadyInQueue($steamID)){

			$insertArray["SteamID"] = $steamID;
			$insertArray["Rank"] = (int)$rank;

			$DB->insert("SingleQueue", $insertArray, null, 0);
			$ret['status'] = "true";
		}
		else{
			$sql = "UPDATE SingleQueue
					Set Rank = ".(int)$rank.", Timestamp = now()
							WHERE SteamID = ".$steamID."";
			$DB->update($sql,0);
			$ret['status'] = "update";
		}
		return $ret;
	}


	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	/**
	 * function joinQueue2
	 * Liefert Array mit prozentwerten für graf darstellung in Überischt eigene Prozessschritte
	 *
	 * @author Artur Leinweber <mafudelaptu@web.de>
	 *
	 * @param 	Array $matchmodes
	 *
	 * return Array
	 */
	function joinQueue2($matchmodes, $regions, $steamID=0, $bot=false){
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
						
					$ret['debug'] .= p("TS:".$_SESSION['user']['joinTimestamp'],1);
					$ret['debug'] .= p($_SESSION,1);
						
					if($_SESSION['user']['joinTimestamp'] == 0 OR $_SESSION['user']['joinTimestamp'] == ""){
						$_SESSION['user']['joinTimestamp'] = time();
					}
					$ret['debug'] .= p("TS2:".$_SESSION['user']['joinTimestamp'],1);
						
					if(is_array($matchmodes) AND count($matchmodes)>0){
						$insertStatement = "";
						$insertArray = array();
						//$_SESSION['elo'] = array();
						$UserPoints = new UserPoints();
						$retUP = $UserPoints->getGlobalPointsOfUser($steamID, SingleQueue::matchType);
						$ret['debug'] .= p($retUP,1);

						$points = $retUP['data'];
						foreach($matchmodes as $k => $v){
							$matchMode = (int)$v;

							if(is_array($regions) AND count($regions)>0){
									
								foreach($regions as $kr => $vr){
									$regionID = $vr;
										
									$tmpArray['SteamID'] = secureNumber($steamID);
									$tmpArray['MatchTypeID'] = SingleQueue::matchType;
									$tmpArray['MatchModeID'] = (int) $matchMode;
									$tmpArray['Elo'] = (int)$points;
									if($bot){
										$timestamp = time();
									}
									else{
										$timestamp = (int)$_SESSION['user']['joinTimestamp'];
									}
										
									$tmpArray['Timestamp'] = (int)$timestamp;
									$tmpArray['Region'] = (int) $regionID;
									$tmpArray['ForceSearch'] = 0;

									$insertArray[] = $tmpArray;
									// Session für matchMaiking setzen, damit die Elo's nciht erneut ausgelesen werden müssen
									$_SESSION['regions'][$matchMode] = $regionID;
								}
							}

							// Session für matchMaiking setzen, damit die Elo's nciht erneut ausgelesen werden müssen
							//$_SESSION['elo'][$matchMode] = (int)$data['Elo'];
							$_SESSION['points'] = (int) $points;
						}
							
						$retINs = $DB->multiInsert("Queue",$insertArray);

						$ret['status'] = $retINs;
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
				WHERE MatchTypeID = ".(int) SingleQueue::matchType."
						";
		$count = $DB->countRows($sql);

		$sql = "SELECT DISTINCT SteamID
				FROM QueueGroupMembers
				WHERE MatchTypeID = ".(int) SingleQueue::matchType."
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

		$sql = "SELECT Count(DISTINCT m.MatchID)*10 as Count
				FROM `Match` m JOIN MatchDetails md ON md.MatchID = m.MatchID
				WHERE MatchTypeID = ".(int) SingleQueue::matchType."
						AND TeamWonID = -1
						AND TimestampClosed = 0
						AND ManuallyCheck != 1
						AND Canceled != 1
						";
		$ret['debug'] .= $sql;
		$data = $DB->select($sql);
		$ret['count'] = $data['Count'];
		// 		$count = $DB->countRows($sql);

		// 		$ret['count'] = $count*10;

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
				WHERE MatchTypeID = ".SingleQueue::matchType."
						GROUP BY MatchModeID, SteamID
						";
		$data = $DB->multiSelect($sql);

		if(!is_array($data) OR count($data) == 0){
			$data = array();
		}

		$sql = "SELECT DISTINCT qgm.MatchModeID, SteamID, mm.Name as MatchModeName, mm.Shortcut as MatchModeSc
				FROM QueueGroupMembers qgm JOIN MatchMode mm ON mm.MatchModeID = qgm.MatchModeID
				WHERE MatchTypeID = ".SingleQueue::matchType."
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
				WHERE MatchTypeID = ".SingleQueue::matchType."
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