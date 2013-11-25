<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserVotes{

	const maxNegativeVotesInMatch = 4;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertVote($steamID, $voteTypeID, $matchID, $voteType){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertVote <br>\n";
		$steamIDVoter = $_SESSION['user']["steamID"];
		$log = new KLogger ( "log.txt" , KLogger::INFO );

		$ret['debug'] .= "VOTEID:".$voteTypeID." <br>\n";
		if($matchID > 0 && $steamID > 0 && $steamIDVoter > 0 && $voteTypeID > 0){

			// Vorher Checken ob er das darf
			$UserVoteCounts = new UserVoteCounts();
			$checkRet = $UserVoteCounts->userAllowToVote();
			$check = $checkRet['data'];
			switch($voteType){
				case "1":
					$checkValue = $check['upvotesAllowed'];
					break;
				case "-1":
					$checkValue = $check['downvotesAllowed'];
					break;
			}

			if($checkValue){
				$insertArray = array();
				$insertArray['SteamID'] = secureNumber($steamID);
				$insertArray['VoteOfUser'] = secureNumber($steamIDVoter);
				$insertArray['VoteTypeID'] = (int) $voteTypeID;
				$insertArray['MatchID'] = (int) $matchID;
				$insertArray['Timestamp'] = time();

				$retIns = $DB->insert("UserVotes", $insertArray);
				$ret['debug'] .= p($retIns,1);
				$ret['debug'] .= p("TESTSTEST",1);
				// Update upcote/downvote counts
				if($retIns){

					$retUp = $UserVoteCounts->updateVotesForUser($voteType, $steamIDVoter);
					$ret['debug'] .= p($retUp,1);
					
					// Credits erhöhen bei poistivem Vote
					if($voteType == "1"){
						$tmpArray['SteamID'] = $steamIDVoter;
						$tmpArray['VoteOfUser'] = $steamID;
						$tmpArray['Type'] = $voteType;
						$tmpArray['Gewicht'] = 1;
						
						$array[$matchID] = $tmpArray;
						
						$UserCredits = new UserCredits();
						$ret['debug'] .= p($array,1);
						$retCredits = $UserCredits->insertCredits($matchID, $array);
						$ret['debug'] .= p($retCredits,1);
					}
				}
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}

		}
		else{
			$log->LogInfo(__FUNCTION__." MatchID:".$matchID."-SteamID:".$steamID."-SteamIDVoter:".$steamIDVoter."initiated by:".$_SESSION['user']["steamID"]);

			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End insertVote <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getUserVotesForMatch($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertNewUser <br>\n";

		if($matchID > 0){
			$sql = "SELECT uv.*, vt.Name as VoteTypeLabel, vt.Type as VoteType
					FROM `UserVotes` uv JOIN VoteType vt ON vt.VoteTypeID = uv.VoteTypeID
					WHERE MatchID = ".(int)$matchID."
							";
			$data = $DB->multiSelect($sql);

			// Array in gute struktur überführen
			if(is_array($data) && count($data) > 0){
				foreach($data as $k =>$v){
					$tmpArray = array();
					$tmpArray['VoteOfUser'] = $v['VoteOfUser'];
					$tmpArray['VoteTypeID'] = $v['VoteTypeID'];
					$tmpArray['Timestamp'] = $v['Timestamp'];
					$tmpArray['VoteTypeLabel'] = $v['VoteTypeLabel'];
					$tmpArray['VoteType'] = $v['VoteType'];

					$array[$v['SteamID']][] = $tmpArray;

				}
			}

			// einige Counts noch berechen
			if(is_array($array) && count($array) > 0){
				foreach($array as $k =>$v){
					$array[$k]['posCounts'] = 0;
					$array[$k]['negCounts'] = 0;
					if(is_array($v) && count($v) > 0){
						foreach($v as $kk =>$vv){
							$voteType = $vv['VoteType'];

							switch($voteType){
								case "1":
									$array[$k]['posCounts']++;
									break;
								case "-1":
									$array[$k]['negCounts']++;
									break;
							}
						}
					}
				}
			}

			$ret['data'] = $array;
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
	function getUserVotesForMatchOfUser($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$steamID = $_SESSION['user']["steamID"];
		$ret['debug'] .= "Start getUsersVotesForMatch <br>\n";
		if($matchID > 0){

			$sql = "SELECT *
					FROM `UserVotes`
					WHERE VoteOfUser = ".secureNumber($steamID)." AND MatchID = ".(int) $matchID."
							";
			$data = $DB->multiSelect($sql);

			// Array aufbereiten
			if(is_array($data) && count($data) > 0){
				foreach($data as $k =>$v){
					$array[] = $v['SteamID'];
				}
			}
			$ret['data'] = $array;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getUsersVotesForMatch <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getRelevantVotes($matchID, $data=array()){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getRelevantVotes <br>\n";

		if($matchID > 0){

			if(is_array($data) && count($data) == 0){
				$sql = "SELECT uv.*, vt.Gewicht, vt.Type
						FROM `UserVotes` uv JOIN VoteType vt ON uv.VoteTypeID = vt.VoteTypeID
						WHERE MatchID = ".(int)$matchID."
								ORDER BY vt.Gewicht DESC
								";
				$data = $DB->multiSelect($sql);
			}
			// für jeden gefundenen Eintrag kontrollieren ob positiv oder negativ
			// negativ nur schlimmsten 4 nehmen
			if(is_array($data) && count($data) > 0){
				$countBad = 0;
				$retArray = array();

				foreach($data as $k =>$v){
					$voteType = $v['Type'];
					switch($voteType){
						case "1":
							$retArray[] = $v;
							break;
						case "-1":
							// sind die Schlimmsten weil sortiert nach gewicht
							if($countBad < UserVotes::maxNegativeVotesInMatch){
								$retArray[] = $v;
								$countBad++;
							}
							break;
					}
				}
			}
			$ret['data'] = $retArray;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getRelevantVotes <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function setUserVotesOfMatchToEditedByCronjob($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start setUserVotesOfMatchToEditedByCronjob <br>\n";
		if($matchID > 0){
			$sql = "UPDATE `UserVotes`
					SET EditedByCronjob = 1
					WHERE MatchID = ".(int)$matchID."
					";
			$ret['debug'] .= p($sql,1);
			$retUpdate = $DB->update($sql);
			$ret['status'] = $retUpdate;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End setUserVotesOfMatchToEditedByCronjob <br>\n";

		return $ret;
	}

}

?>