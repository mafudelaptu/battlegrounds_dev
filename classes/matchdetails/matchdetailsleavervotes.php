<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class MatchDetailsLeaverVotes{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLeaverData($matchID, $withUserData=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($matchID > 0){

			$sql = "SELECT mdlv.SteamID, mdlv.VoteForPlayer, mdlv.Timestamp
					FROM MatchDetailsLeaverVotes mdlv
					WHERE mdlv.MatchID = ".(int) $matchID."
							";
			$data = $DB->multiSelect($sql);

			if(is_array($data) && count($data) > 0){
				$dataLeaver = array();
				$User = new User();
				// array in richtige Formn bringen
				foreach($data as $k => $v){
					$dataCountable[] = $v['VoteForPlayer'];


					$tmp = array();
					if ($withUserData) {
						$tmp = $User->getUserData($v['SteamID'], "Name, Avatar");
						$leaverUserData = $User->getUserData($v['VoteForPlayer'], "Name, Avatar");
						$dataLeaver[$v['VoteForPlayer']]['userData'] = $leaverUserData;
					}
					$tmp['VotedBy'] = $v['SteamID'];
					$tmp['Timestamp'] = $v['Timestamp'];

					$dataLeaver[$v['VoteForPlayer']]['VotedByPlayers'][] = $tmp;
				}
				$countArray = array_count_values($dataCountable);
				$ret['countArray'] = $countArray;
				$ret['data'] = $dataLeaver;
				$ret['status'] = true;
			}
			else{
				$ret['status'] = "keine Leaver";
			}
		}
		else{
			$ret['status'] = "matchID = 0";
		}

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLeaverVoteCount($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($matchID > 0){
			$sql = "SELECT VoteForPlayer
					FROM `MatchDetailsLeaverVotes`
					WHERE MatchID = ".(int)$matchID." AND Vote != 0
							";
			$count = $DB->countRows($sql);

			$ret['count'] = $count;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID = 0";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertSelectedLeaver($matchID, $leaver, $steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] = print_r($leaver,1);
		if($matchID > 0){
			if(is_array($leaver) && count($leaver)>0){
				$ret['debug'] .= "#### LEAVER angegeben ####";
				foreach($leaver as $k => $v){
					$ret['debug'] .= "LEAVER: ".$v;

					$insertArray = array();

					$insertArray['steamID'] = secureNumber($steamID);
					$insertArray['matchID'] = (int) $matchID;
					$insertArray['VoteForPlayer'] = secureNumber($v);
					$insertArray['Timestamp'] = time();

					$DB->insert("MatchDetailsLeaverVotes", $insertArray);
				}
				$ret['status'] = true;
			}
			else $ret['status'] = "Leaver ist null";
		}
		else{
			$ret['status'] = "matchID = 0";
		}
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertLeaverVote($matchID, $leaver, $steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] = print_r($leaver,1);
		if($matchID > 0 && $leaver > 0 && $steamID > 0){
			$ret['debug'] .= "LEAVER: ".$leaver;

			$insertArray = array();

			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['MatchID'] = (int) $matchID;
			$insertArray['VoteForPlayer'] = secureNumber($leaver);
			$insertArray['Timestamp'] = time();

			$DB->insert("MatchDetailsLeaverVotes", $insertArray);

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID = 0 || leaver = 0 || steamID = 0";
		}
		return $ret;
	}

	function getCancelLeaverData($matchID, $withUserData=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($matchID > 0){

			$sql = "SELECT mdlv.SteamID, mdlv.VoteForPlayer, mdlv.Timestamp
					FROM `MatchDetailsCancelMatchVotes` mdlv
					WHERE mdlv.MatchID = ".(int) $matchID."
							";
			$data = $DB->multiSelect($sql);

			if(is_array($data) && count($data) > 0){
				$dataLeaver = array();
				$User = new User();
				// array in richtige Formn bringen
				foreach($data as $k => $v){
					$dataCountable[] = $v['VoteForPlayer'];


					$tmp = array();
					if ($withUserData) {
						$tmp = $User->getUserData($v['SteamID'], "Name, Avatar");
						$leaverUserData = $User->getUserData($v['SteamID'], "Name, Avatar");
						$dataLeaver[$v['VoteForPlayer']]['userData'] = $leaverUserData;
					}
					$tmp['VotedBy'] = $v['SteamID'];
					$tmp['Timestamp'] = $v['Timestamp'];

					$dataLeaver[$v['VoteForPlayer']]['VotedByPlayers'][] = $tmp;
				}
				$countArray = array_count_values($dataCountable);
				$ret['countArray'] = $countArray;
				$ret['data'] = $dataLeaver;
				$ret['status'] = true;
			}
			else{
				$ret['status'] = "keine Leaver";
			}
		}
		else{
			$ret['status'] = "matchID = 0";
		}

		return $ret;
	}

	function deleteAllVotesOfLeaver($matchID, $leaver){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start deleteAllVotesOfLeaver <br>\n";

		if($matchID > 0 && $leaver > 0){
			
			$sql = "DELETE	FROM `MatchDetailsLeaverVotes` 
							WHERE VoteForPlayer = ".secureNumber($leaver)." AND MatchID = ".(int) $matchID."
			";
			$data = $DB->delete($sql); 
			
			$sql = "DELETE	FROM `MatchDetailsCancelMatchVotes`
							WHERE VoteForPlayer = ".secureNumber($leaver)." AND MatchID = ".(int) $matchID."
			";
			$data = $DB->delete($sql);
			
			$ret['debug'] .= p($sql,1);

			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "matchID == 0 || leaver = 0";
		}
		$ret['debug'] .= "End deleteAllVotesOfLeaver <br>\n";
		return $ret;
	}
}

?>