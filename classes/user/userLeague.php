<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserLeague{
	const totalLeagueLvl = 5;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertFirstLeagueForUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertFirstLeagueForUser <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT *
					FROM `UserLeague`
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->countRows($sql);
			if($data > 0){
				$ret['status'] = "bereits eingetragen";
			}
			else{
				$insertArray = array();

				$insertArray['SteamID'] = secureNumber($steamID);
				$insertArray['LeagueTypeID'] = 1;
				$insertArray['LeagueTypeIDBefore'] = 0;
				$insertArray['ChangeTimestamp'] = 0;

				$retINs = $DB->insert("UserLeague", $insertArray);
				$ret['status'] = $retINs;
			}


		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End insertFirstLeagueForUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLeagueOfUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLeagueOfUser <br>\n";
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		if($steamID > 0){
			$sql = "SELECT *
					FROM `UserLeague` ul JOIN LeagueType lt ON lt.LeagueTypeID = ul.LeagueTypeID
					WHERE ul.SteamID = ".secureNumber($steamID)."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End getLeagueOfUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLeagueLvl($steamID, $leagueTypeID, $points){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLeagueLvl <br>\n";
		if($steamID > 0 && $leagueTypeID > 0){
			$leagueLvl = "";
			$ret['debug'] .= p($LeagueTypeID,1);
				
			if($points < 0){
				$points = 0;
			}
				
			switch($leagueTypeID){
				// Qualifying -> keine Lvl
				case "1":
					$leagueLvl = "";
					break;
					// Diamond nach oben offen
				case "2":
					$LeagueType = new LeagueType();
					$retLT = $LeagueType->getData($leagueTypeID);
					if($points < $retLT['data']['PointBorderBottom']){
						$uG = 0;
					}
					else{
						$uG =$retLT['data']['PointBorderBottom'];
					}
					$oG = $retLT['data']['PointBorderTop'];
						
					$spanne = $oG - $uG;
					$lvlBreite = $spanne/UserLeague::totalLeagueLvl;
					$ret['debug'] .= p($lvlBreite." ".$spanne,1);
					$leagueLvl =  (int) floor(($points-$uG)/$lvlBreite);
						
					break;
				case "6":
					$leagueLvl = "";
					break;
				default:
					$LeagueType = new LeagueType();
					$retLT = $LeagueType->getData($leagueTypeID);
					$uG = $retLT['data']['PointBorderBottom'];
					$oG = $retLT['data']['PointBorderTop'];

					$spanne = $oG - $uG;
					$lvlBreite = $spanne/UserLeague::totalLeagueLvl;
					$ret['debug'] .= p($lvlBreite." ".$spanne,1);
					$leagueLvl =  (int) floor(($points-$uG)/$lvlBreite);
			}

			if($leagueLvl === 0){
				$leagueLvl = "";
			}

			$ret['data']  = $leagueLvl;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0 oder leagueTypeID == 0";
		}

		$ret['debug'] .= "End getLeagueLvl <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getBorderLvlPoints($leagueTypeID, $leagueLvl){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getBorderLvlPointsOfGivenPoints <br>\n";
		if($leagueTypeID > 0){

			$LeagueType = new LeagueType();
			$retLT = $LeagueType->getData($leagueTypeID);

			$uG = $retLT['data']['PointBorderBottom'];
			$oG = $retLT['data']['PointBorderTop'];

			$spanne = $oG - $uG;
			$lvlBreite = $spanne/UserLeague::totalLeagueLvl;
			$ret['debug'] .= p($lvlBreite." ".$spanne,1);

			switch($leagueLvl){
				case "":
					$startPoints = (int) $uG;
					$endPoints = (int) $uG + $lvlBreite;
					break;
				case (UserLeague::totalLeagueLvl-1):
					$startPoints = (int) $uG + ($lvlBreite * $leagueLvl);
					$endPoints = (int) $oG + 1;
					break;
				default:
					$startPoints = (int) $uG + ($lvlBreite * ($leagueLvl));
					$endPoints = (int) $uG + ($lvlBreite * ($leagueLvl+1));
			}

			$data['StartPoints'] = (int) $startPoints;
			$data['EndPoints'] = (int) $endPoints;

			$ret['data']  = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = " leagueTypeID = 0";
		}

		$ret['debug'] .= "End getBorderLvlPointsOfGivenPoints <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLeagueName($leagueName, $leagueLvl){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLeagueName <br>\n";
		$ret['debug'] .= p($leagueName." Lvl:".$leagueLvl,1);
		switch($leagueLvl){
			case "":
			case 0:
				$leagueName = $leagueName;
				break;
			default:
				$leagueName = $leagueName." Lvl ".$leagueLvl;
		}

		$ret['data']  =$leagueName;
		$ret['status'] = true;


		$ret['debug'] .= "End getLeagueName <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLeaguePermissionsForUser($leagueTypeID, $steamID = 0 ){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLeaguePermissionsForUser <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0 && $leagueTypeID > 0){
			// Allowed MatchModes
			$matchModeArray = array();
			$ret['debug'] .= p($leagueTypeID,1);
			switch($leagueTypeID){
				case "1":
					$matchModeArray[] = 1; // AP
					break;
				case "2":
					$matchModeArray[] = 1; // AP
					$matchModeArray[] = 2; // SD
					$matchModeArray[] = 4; // ARAM
					$matchModeArray[] = 5; // OM
					$matchModeArray[] = 7; // RD
					$matchModeArray[] = 8; // AR
					break;
				default:
					$matchModeArray[] = 1; // AP
					$matchModeArray[] = 2; // SD
					$matchModeArray[] = 4; // ARAM
					$matchModeArray[] = 5; // OM
					$matchModeArray[] = 7; // RD
					$matchModeArray[] = 8; // AR
					$matchModeArray[] = 3; // CM
			}

			$data['matchModes'] = $matchModeArray;

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End getLeaguePermissionsForUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getHighestLeagueBorder(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getHighestLeagueBorder <br>\n";

		
		$sql = "SELECT *
						FROM `LeagueType`
				WHERE PointBorderTop = -1
		";
		$data = $DB->select($sql); 

		$ret['debug'] .= p($sql,1);
		
		$ret['data'] = $data['PointBorderBottom'];
		$ret['status'] = true;
		
		
		$ret['debug'] .= "End getHighestLeagueBorder <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLeagueTypeByPoints($points){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLeagueTypeByPoints <br>\n";

		$retHighestBorder = $this->getHighestLeagueBorder();
		$highestBorder = $retHighestBorder['data'];
		
		if($points >= $highestBorder){
			$sql = "SELECT *
				FROM `LeagueType`
				WHERE PointBorderTop = -1
						";
		}
		else{
			$sql = "SELECT *
				FROM `LeagueType`
				WHERE PointBorderBottom <= ".(int)$points." AND PointBorderTop >= ".(int)$points."
						";
		}

		
		$ret['debug'] .= p($sql,1);
		$data = $DB->select($sql);

		$ret['data']  =$data['LeagueTypeID'];
		$ret['status'] = true;

		$ret['debug'] .= "End getLeagueTypeByPoints <br>\n";

		return $ret;
	}

}

?>