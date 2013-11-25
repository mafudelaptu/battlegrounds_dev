<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class FakeUser{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function fakeSubmittsSimulieren($matchID, $teamWonID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$steamID = $_SESSION['user']["steamID"];
		$ret['debug'] .= "Start fakeSubmittsSimulieren \n<br>";
		if($steamID > 0){
			if($matchID > 0){
				$TestUser = new TestUser();
				$Match = new Match();

				$sql="SELECT *
						FROM `MatchDetails`
						WHERE MatchID = ".(int) $matchID." AND Submitted != 1
									
								LIMIT 9";
				$ret['debug'] .= $sql."<br>";
				$data = $DB->multiSelect($sql);
				if(is_array($data) && count($data) > 0){
					$i=1;
					foreach($data as $k => $v){
						if($teamWonID == $v['TeamID']){
							$result = 1;
						}
						else{
							$result = -1;
						}

						$sql = "UPDATE `MatchDetails`
								SET Submitted = 1, SubmissionFor = ".$result.", SubmissionTimestamp = ".time()."
										WHERE MatchID = ".(int) $matchID." AND SteamID = ".secureNumber($v['SteamID'])."";
						$DB->update($sql);
						$ret['debug'] .= $sql."<br>";
							
						$i++;
					}
				}

				$ret['status'] = true;
			}
			else{
				$ret['status'] = "matchID 0!";
			}
		}
		else{
			$ret['status'] = "steamID 0!";
		}
		$ret['debug'] .= "End fakeSubmittsSimulieren \n<br>";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function resetSubmissions($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$steamID = $_SESSION['user']["steamID"];
		$ret['debug'] .= "Start resetSubmissions \n<br>";
		if($steamID > 0){
			if($matchID > 0){
				$TestUser = new TestUser();
				$Match = new Match();

				$sql="SELECT *
						FROM `MatchDetails`
						WHERE MatchID = ".(int) $matchID." AND Submitted = 1 AND SteamID != ".secureNumber($steamID)."
									
								LIMIT 9";
				$ret['debug'] .= $sql."<br>";
				$data = $DB->multiSelect($sql);
				if(is_array($data) && count($data) > 0){
					$i=1;
					foreach($data as $k => $v){
						$sql = "UPDATE `MatchDetails`
								SET Submitted = 0, SubmissionFor = 0, SubmissionTimestamp = 0
								WHERE MatchID = ".(int) $matchID." AND SteamID = ".secureNumber($v['SteamID'])."";
						$DB->update($sql);
						$ret['debug'] .= $sql."<br>";
						$i++;
					}
				}

				$ret['status'] = true;
			}
			else{
				$ret['status'] = "matchID 0!";
			}
		}
		else{
			$ret['status'] = "steamID 0!";
		}
		$ret['debug'] .= "End resetSubmissions \n<br>";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertUser($steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$ret['debug'] .= "Start insertUser \n<br>";
		if($steamID > 0){
			$User = new User();
			$Login = new Login();
			if(!$User->userAlreadyInDB($steamID)){
				// neuen User eintragen und hash cookie setzen
				$User->insertNewUser($steamID);

				$Login->insertFirstStatsOfUser($steamID);
				$ret['status'] = "User eingetragen!";
			}
			else{
				$ret['status'] = "User schon vorhanden";
			}
		}
		else{
			$ret['status'] = "steamID 0!";
		}
		$ret['debug'] .= "End insertUser \n<br>";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertRandomUserInQueue($matchTypeID = 1){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$ret['debug'] .= "Start insertRandomUserinQueue \n<br>";

		$SingleQueue = new SingleQueue();
		$OneVsOneQueue = new OneVsOneQueue();
		$ThreeVsThreeQueue = new ThreeVsThreeQueue();
		$Queue = new Queue();
		$matchModes = array("1","2","3","4","5","9");
		$regions = array("1");

		$sql = "SELECT SteamID
				FROM User";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= "MatchTypeID=".$matchTypeID;
		if(is_array($data) && count($data) > 0){
			foreach($data as $k => $v){
				$dataNew[] = $v['SteamID'];
			}
			//p($dataNew);
			$steamID = $dataNew[array_rand($dataNew, 1)];
			//p($steamID);
			if(!$Queue->inQueue($steamID)){
				$UserLeague = new UserLeague();
				$retUL = $UserLeague->insertFirstLeagueForUser($steamID);
				$User = new User();
				//$retUE = $User->setBasePointsForPlayer($steamID);
				//p($retUE);
				switch ($matchTypeID){
					case "1":
						$ret['debug'] .= "MatchTypeID=1";
						$ret = $SingleQueue->joinQueue2($matchModes, $regions, $steamID, true);
						break;
					case "8":
						$matchModes = array("5");
						$ret['debug'] .= "MatchTypeID=8";
						$ret = $OneVsOneQueue->joinQueue($matchModes, $regions, $steamID);
						
// 						$sql = "UPDATE `Queue`
// 								SET Elo = 1200
// 								";
// 						$DB->update($sql);
						break;
					case "9":
						$UserElo = new UserElo();
// 						$haveStats = $UserElo->checkIfUserHaveEloForMatchType(9, $steamID);
// 						$ret['debug'] .= p($haveStats,1);
// 						$haveStats = $haveStats['status'];
// 						if(!$haveStats){
// 							$retIns = $UserElo->insertFirstEloForMatchType($_POST['ID'], $steamID);
// 						}
						
						$ret['debug'] .= "MatchTypeID=9";
						$ret = $ThreeVsThreeQueue->joinQueue($matchModes, $regions, $steamID);
						
// 						$sql = "UPDATE `Queue`
// 							SET Elo = 1200
// 							WHERE Elo = 0
// 							";
// 						$DB->update($sql);
						break;
				}
				
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['debug'] .= "SQL wrong!";
		}

		$ret['debug'] .= "End insertRandomUserinQueue \n<br>";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function submitAllMatchAccept($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$ret['debug'] .= "Start submitAllMatchAccept \n<br>";

		if($matchID > 0){
			$sql = "UPDATE `MatchTeams`
					SET Ready = 1
					WHERE MatchID = ".(int) $matchID."
							";
			$DB->update($sql);
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID 0!";
		}

		$ret['debug'] .= "End submitAllMatchAccept \n<br>";
		return $ret;
	}

}

?>