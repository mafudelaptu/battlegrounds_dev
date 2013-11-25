<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserCredits{
	const banBorder = -20;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertCredits($matchID, $data) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertCredits <br>\n";
		$ret['debug'] .= p($data,1);
		if($matchID > 0){
			if(is_array($data) && count($data) > 0){
				$insertArray = array();
				foreach($data as $k =>$v){
					$tmpArray = array();
					$ret['debug'] .= p($v,1);
					$tmpArray['SteamID'] = secureNumber($v['SteamID']);
					$tmpArray['VotedOfPlayer'] = secureNumber($v['VoteOfUser']);
					$tmpArray['MatchID'] = (int) $matchID;
					$voteType = $v['Type'];
					switch($voteType){
						case "1":
							$tmpArray['Vote'] = $v['Gewicht'];
							break;
						case "-1":
							$tmpArray['Vote'] = (int) $v['Gewicht']*(-1);
							break;
					}

					$tmpArray['Timestamp'] = time();
					$insertArray[] = $tmpArray;
				}
				$ret['debug'] .= $DB->multiInsert("UserCredits", $insertArray,1);
				$retIns = $DB->multiInsert("UserCredits", $insertArray);

				$ret['status'] = $retIns;
			}
			else{
				$ret['debug'] .= "Data -> leer";
				$ret['status'] = false;
			}
		}
		else{
			$ret['debug'] .= "MatchID = 0";
			$ret['status'] = false;
		}

		$ret['debug'] .= "End insertCredits <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function resetUserCredits($steamID) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start resetUserCredits <br>\n";
		if($steamID > 0){
			$sql = "DELETE FROM `UserCredits`
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->delete($sql);
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID = 0";
		}

		$ret['debug'] .= "End resetUserCredits <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getCreditCountOfPlayer($steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getCreditCountOfPlayer <br>\n";
		if($steamID > 0){
			$sql = "SELECT SUM(Vote) as Summe
					FROM `UserCredits`
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);


			if($data['Summe'] != ""){
				$ret['data'] = $data['Summe'];

			}
			else{
				$ret['data'] = (int) 0;
			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID = 0";
		}

		$ret['debug'] .= "End getCreditCountOfPlayer <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function initCreditsOfPlayer($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		$ret['debug'] .= "Start initCreditsOfPlayer <br>\n";
		if($steamID > 0){

			$sql = "SELECT *
					FROM `UserCredits`
					WHERE SteamID = ".secureNumber($steamID)."
							LIMIT 1
							";
			$ret['debug'] .= p($sql,1);
			$count = $DB->countRows($sql);

			// wenn leer dann ersten Datensatz eintragen
			if($count == "0"){
				$insertArray = array();
				$insertArray['SteamID'] = secureNumber($steamID);
				$insertArray['VotedOfPlayer'] = secureNumber($steamID);
				$insertArray['MatchID'] = (int)0;
				$insertArray['Vote'] = 0; // NUllVOte

				$retIns = $DB->insert("UserCredits", $insertArray);
				$ret['status'] = $retIns;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "SteamID = 0";
		}

		$ret['debug'] .= "End initCreditsOfPlayer <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getHighestUserCredits($count=5){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getHighestUserCredits <br>\n";
		if($count > 0){
			$sqlPoints = "SELECT IF(SUM(PointsChange)+u.BasePoints > 0, SUM(PointsChange)+u.BasePoints, 0)
				FROM `UserPoints`
				WHERE SteamID = uc.SteamID
								";
			$sql = "SELECT SUM(Vote) as Credits, uc.SteamID, u.Avatar, u.Name, 
					(".$sqlPoints.") as Points
					FROM `UserCredits` uc
					JOIN User u ON u.SteamID = uc.SteamID
					GROUP BY uc.SteamID
					HAVING Credits > 0
					ORDER BY Credits DESC
					LIMIT ".(int)$count."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);
			
			$ret['data'] = $data;
		}

		$ret['status'] = true;

		$ret['debug'] .= "End getHighestUserCredits <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertReplayUploadBonus($matchID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertReplayUploadBonus <br>\n";
	
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
	
		if($matchID > 0){
			$insertArray = array();
	
			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['VotedOfPlayer'] = secureNumber($steamID);
			$insertArray['MatchID'] = (int) $matchID;
			$insertArray['Vote'] = 1;
			$insertArray['Timestamp'] = time();
	
			$retINs = $DB->insert("UserCredits", $insertArray);
			$ret['status'] = $retINs;
		}
		else{
			$ret['status'] .= "matchID == 0";
		}
		$ret['debug'] .= "End insertReplayUploadBonus <br>\n";
	
		return $ret;
	}
	
}

?>