<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-09-02
*/
class UserSkillBracket{
	const totalSkillBracketLvl = 5;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-09-02
	*/
	function insertFirstSkillBracketForUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertFirstSkillBracketForUser <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT *
					FROM `UserSkillBracket`
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
				$insertArray['MatchTypeID'] = 1;
				$insertArray['SkillBracketTypeID'] = 2;
				$insertArray['SkillBracketTypeIDBefore'] = 0;
				$insertArray['ChangeTimestamp'] = 0;

				$retINs = $DB->insert("UserSkillBracket", $insertArray);
				$ret['status'] = $retINs;
			}


		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End insertFirstSkillBracketForUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-09-02
	*/
	function getSkillBracketOfUser($steamID=0, $matchTypeID=1){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getSkillBracketOfUser <br>\n";
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		if($steamID > 0){
			$sql = "SELECT *
					FROM `UserSkillBracket` ul JOIN SkillBracketType lt ON lt.SkillBracketTypeID = ul.SkillBracketTypeID
					WHERE ul.SteamID = ".secureNumber($steamID)." AND ul.MatchTypeID = ".(int) $matchTypeID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End getSkillBracketOfUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
// 	function getSkillBracketLvl($steamID, $SkillBracketTypeID, $points){
// 		$ret = array();
// 		$DB = new DB();
// 		$con = $DB->conDB();
// 		$ret['debug'] .= "Start getSkillBracketLvl <br>\n";
// 		if($steamID > 0 && $SkillBracketTypeID > 0){
// 			$SkillBracketLvl = "";
// 			$ret['debug'] .= p($SkillBracketTypeID,1);
				
// 			if($points < 0){
// 				$points = 0;
// 			}
				
// 			switch($SkillBracketTypeID){
// 				// Qualifying -> keine Lvl
// 				case "1":
// 					$SkillBracketLvl = "";
// 					break;
// 					// Diamond nach oben offen
// 				case "2":
// 					$SkillBracketType = new SkillBracketType();
// 					$retLT = $SkillBracketType->getData($SkillBracketTypeID);
// 					if($points < $retLT['data']['PointBorderBottom']){
// 						$uG = 0;
// 					}
// 					else{
// 						$uG =$retLT['data']['PointBorderBottom'];
// 					}
// 					$oG = $retLT['data']['PointBorderTop'];
						
// 					$spanne = $oG - $uG;
// 					$lvlBreite = $spanne/$this::totalSkillBracketLvl;
// 					$ret['debug'] .= p($lvlBreite." ".$spanne,1);
// 					$SkillBracketLvl =  (int) floor(($points-$uG)/$lvlBreite);
						
// 					break;
// 				case "6":
// 					$SkillBracketLvl = "";
// 					break;
// 				default:
// 					$SkillBracketType = new SkillBracketType();
// 					$retLT = $SkillBracketType->getData($SkillBracketTypeID);
// 					$uG = $retLT['data']['PointBorderBottom'];
// 					$oG = $retLT['data']['PointBorderTop'];

// 					$spanne = $oG - $uG;
// 					$lvlBreite = $spanne/$this::totalSkillBracketLvl;
// 					$ret['debug'] .= p($lvlBreite." ".$spanne,1);
// 					$SkillBracketLvl =  (int) floor(($points-$uG)/$lvlBreite);
// 			}

// 			if($SkillBracketLvl === 0){
// 				$SkillBracketLvl = "";
// 			}

// 			$ret['data']  = $SkillBracketLvl;
// 			$ret['status'] = true;
// 		}
// 		else{
// 			$ret['status'] = "steamID = 0 oder SkillBracketTypeID == 0";
// 		}

// 		$ret['debug'] .= "End getSkillBracketLvl <br>\n";

// 		return $ret;
// 	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
// 	function getBorderLvlPoints($SkillBracketTypeID, $SkillBracketLvl){
// 		$ret = array();
// 		$DB = new DB();
// 		$con = $DB->conDB();
// 		$ret['debug'] .= "Start getBorderLvlPointsOfGivenPoints <br>\n";
// 		if($SkillBracketTypeID > 0){

// 			$SkillBracketType = new SkillBracketType();
// 			$retLT = $SkillBracketType->getData($SkillBracketTypeID);

// 			$uG = $retLT['data']['PointBorderBottom'];
// 			$oG = $retLT['data']['PointBorderTop'];

// 			$spanne = $oG - $uG;
// 			$lvlBreite = $spanne/$this::totalSkillBracketLvl;
// 			$ret['debug'] .= p($lvlBreite." ".$spanne,1);

// 			switch($SkillBracketLvl){
// 				case "":
// 					$startPoints = (int) $uG;
// 					$endPoints = (int) $uG + $lvlBreite;
// 					break;
// 				case ($this::totalSkillBracketLvl-1):
// 					$startPoints = (int) $uG + ($lvlBreite * $SkillBracketLvl);
// 					$endPoints = (int) $oG + 1;
// 					break;
// 				default:
// 					$startPoints = (int) $uG + ($lvlBreite * ($SkillBracketLvl));
// 					$endPoints = (int) $uG + ($lvlBreite * ($SkillBracketLvl+1));
// 			}

// 			$data['StartPoints'] = (int) $startPoints;
// 			$data['EndPoints'] = (int) $endPoints;

// 			$ret['data']  = $data;
// 			$ret['status'] = true;
// 		}
// 		else{
// 			$ret['status'] = " SkillBracketTypeID = 0";
// 		}

// 		$ret['debug'] .= "End getBorderLvlPointsOfGivenPoints <br>\n";

// 		return $ret;
// 	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
// 	function getSkillBracketName($SkillBracketName, $SkillBracketLvl){
// 		$ret = array();
// 		$DB = new DB();
// 		$con = $DB->conDB();
// 		$ret['debug'] .= "Start getSkillBracketName <br>\n";
// 		$ret['debug'] .= p($SkillBracketName." Lvl:".$SkillBracketLvl,1);
// 		switch($SkillBracketLvl){
// 			case "":
// 			case 0:
// 				$SkillBracketName = $SkillBracketName;
// 				break;
// 			default:
// 				$SkillBracketName = $SkillBracketName." Lvl ".$SkillBracketLvl;
// 		}

// 		$ret['data']  =$SkillBracketName;
// 		$ret['status'] = true;


// 		$ret['debug'] .= "End getSkillBracketName <br>\n";

// 		return $ret;
// 	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-09-02
	*/
	function getSkillBracketPermissionsForUser($SkillBracketTypeID, $steamID = 0 ){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getSkillBracketPermissionsForUser <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0 && $SkillBracketTypeID > 0){
			// Allowed MatchModes
			$matchModeArray = array();
			$ret['debug'] .= p($SkillBracketTypeID,1);
			switch($SkillBracketTypeID){
				case "1":
				case "2":
				case "3":
					$matchModeArray[] = 1; // AP
					$matchModeArray[] = 2; // SD
					$matchModeArray[] = 4; // ARAM
					$matchModeArray[] = 5; // OM
					$matchModeArray[] = 7; // RD
					$matchModeArray[] = 8; // AR
					$matchModeArray[] = 3; // CM
					$matchModeArray[] = 9; // CD
					break;
				default:
					$matchModeArray[] = 1; // AP
					$matchModeArray[] = 2; // SD
					$matchModeArray[] = 4; // ARAM
					$matchModeArray[] = 5; // OM
					$matchModeArray[] = 7; // RD
					$matchModeArray[] = 8; // AR
					$matchModeArray[] = 3; // CM
					$matchModeArray[] = 9; // CD
			}

			$data['matchModes'] = $matchModeArray;

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End getSkillBracketPermissionsForUser <br>\n";

		return $ret;
	}

}

?>