<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class SkillBracket {
	const totalGamesAmateur = 20;
	const totalGamesSkilled = 50;
	const totalGamesExpert = 100;
	const totalGamesMaster = 150;
	const winRateAmateur = 45;
	const winRateSkilled = 48;
	const winRateExpert = 51;
	const winRateMaster = 52.5;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getSkillBracketOfUserByStats($steamID = 0, $matchTypeID=1) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getSkillBracketOfUserByStats <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {

			// Get current total Games and winrate of user
			$UserPoints = new UserPoints ();
			$retUP = $UserPoints->getGameStatsOfUser ( $steamID, $matchTypeID );
			$userStats = $retUP ['data'];
			$ret ['debug'] .= p ( $retUP, 1 );
			// get current CreditCount
			$UserCredits = new UserCredits ();
			$retUC = $UserCredits->getCreditCountOfPlayer ( $steamID );
			$creditsCount = $retUC ['data'];
			$ret ['debug'] .= p ( $retUC, 1 );
			// get current active Bans of User
			$Banlist = new Banlist();
			$retB = $Banlist->getAllBansOfPlayer($steamID,true);
			$activeBans = $retB['count'];
			
			// get Prison stats
			$retBC = $Banlist->getCurrentBanDataOfPlayer($steamID);
			$ret['debug'] .= p($retBC,1);
			if($retBC['banned'] == false && $retBC['display'] != 1){
				$calcSkillBracket = $this->getFitSkillBracketByStats ( $userStats ['TotalGames'], $userStats ['WinRate'], $creditsCount, $activeBans );
				$ret ['debug'] .= p ( $calcSkillBracket, 1 );
				$ret ['data'] = $calcSkillBracket ['data'];
			}
			else{
				// sonst Prison
				$ret ['data'] = 1;
			}

			$ret ['status'] = true;
		} else {
			$ret ['status'] = "steamID == 0";
		}
		$ret ['debug'] .= "End getSkillBracketOfUserByStats <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getFitSkillBracketByStats($totalGames, $winRate, $creditsCount, $activeBans) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getFitSkillBracketByStats <br>\n";
		
		$warnsBorder = $activeBans*10;
		
		$ret ['debug'] .= p ( "tG:" . $totalGames . " wR:" . $winRate . " cC:" . $creditsCount . " aB:" . $activeBans." wB:".$warnsBorder, 1 );

		// test Master
		if ($totalGames >= SkillBracket::totalGamesMaster && $winRate >= SkillBracket::winRateMaster && $creditsCount > 0 && $totalGames >= $warnsBorder) {
			$ret ['data'] = 6;
			return $ret;
		} else if ($totalGames >= SkillBracket::totalGamesExpert && $winRate >= SkillBracket::winRateExpert && $creditsCount > 0 && $totalGames >= $warnsBorder) {
			$ret ['data'] = 5;
			return $ret;
		} else if ($totalGames >= SkillBracket::totalGamesSkilled && $winRate >= SkillBracket::winRateSkilled && $creditsCount > 0 && $totalGames >= $warnsBorder) {
			$ret ['data'] = 4;
			return $ret;
		} else if ($totalGames >= SkillBracket::totalGamesAmateur && $winRate >= SkillBracket::winRateAmateur && $creditsCount > 0 && $totalGames >= $warnsBorder) {
			$ret ['data'] = 3;
			return $ret;
		} else {
			$ret ['data'] = 2;
			return $ret;
		}
		$ret ['debug'] .= "End getFitSkillBracketByStats <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getRequirementsForNextSkillBracket($steamID = 0, $matchTypeID=1) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getRequirementsForNextSkillBracket <br>\n";

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {

			// Get current total Games and winrate of user
			$UserPoints = new UserPoints ();
			$retUP = $UserPoints->getGameStatsOfUser ( $steamID, $matchTypeID);
			$userStats = $retUP ['data'];
			$ret ['debug'] .= p ( $retUP, 1 );

			// get current CreditCount
			$UserCredits = new UserCredits ();
			$retUC = $UserCredits->getCreditCountOfPlayer ( $steamID );
			$creditsCount = $retUC ['data'];

			// get current active Bans of User
			$activeBans = 0;
			$UserSkillBracket = new UserSkillBracket();
			$retUSB = $UserSkillBracket->getSkillBracketOfUser($steamID, $matchTypeID);
			$ret ['debug'] .= p ( $retUSB, 1 );

			$retData = $this->getSkillBracketData(($retUSB['data']['SkillBracketTypeID']+1));


			$nextSkillBracketData = $retData ['data'];
			$nextSkillBracketTypeID = $nextSkillBracketData ['SkillBracketTypeID'];
			$data['NextSkillBracketName'] = $nextSkillBracketData['Name'];
			// 			$nextSkillBracketTypeID = 4;
			switch ($nextSkillBracketTypeID) {
				case 2 :
					$nextTotalGames = 0;
					$nextWinRate = 0;
					break;
				case 3 :
					$nextTotalGames = SkillBracket::totalGamesAmateur;
					$nextWinRate = SkillBracket::winRateAmateur;
					break;
				case 4 :
					$nextTotalGames = SkillBracket::totalGamesSkilled;
					$nextWinRate = SkillBracket::winRateSkilled;
					break;
				case 5 :
					$nextTotalGames = SkillBracket::totalGamesExpert;
					$nextWinRate = SkillBracket::winRateExpert;
					break;
				case 6 :
					$nextTotalGames = SkillBracket::totalGamesMaster;
					$nextWinRate = SkillBracket::winRateMaster;
					break;
			}

			$data['nextTotalGames'] = (int)$nextTotalGames;
			$data['nextWinRate'] = (int)$nextWinRate;

			// berechnung wieviel noch fehlt
			$currentGames = $userStats ['TotalGames'];
			$currentWinRate = $userStats ['WinRate'];

			$data['currentGames'] = $currentGames;
			$data['currentWinRate'] = $currentWinRate;
			$data['currentCredits'] = $creditsCount;
			// 			$currentGames = 10;
			// 			$currentWinRate = 45;
			// 			$creditsCount = -18;

			$data['neededGames'] = (($nextTotalGames - $currentGames) > 0 ? ($nextTotalGames - $currentGames) : 0);
			$data['neededWinRate'] = round((($nextWinRate - $currentWinRate) > 0 ? ($nextWinRate - $currentWinRate) : 0),2);
			$data['neededCredits'] = (($creditsCount > 0 ? 0 : ((-1)*$creditsCount)+1));
			
			// get current Active Warns
			$Banlist = new Banlist();
			$retB = $Banlist->getAllBansOfPlayer($steamID,1);
			$currentActiveWarns = $retB['count'];
			$neededWarnGames = $currentActiveWarns*10;
			
			$data['activeWarns'] = (int)$currentActiveWarns;
			$data['neededWarnTotalGames'] = (int)$neededWarnGames;
			if($neededWarnGames <= $currentGames){
				$data['neededWarnGames'] = 0;
			}
			else{
				$data['neededWarnGames'] = $neededWarnGames-$currentGames;
			}

			$ret ['data'] = $data;
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "steamID == 0";
		}
		$ret ['debug'] .= "End getRequirementsForNextSkillBracket <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getSkillBracketData($skillBracketTypeID) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getSkillBracketData <br>\n";

		if ($skillBracketTypeID > 0) {

			$sql = "SELECT *
					FROM `SkillBracketType`
					WHERE SkillBracketTypeID = " . ( int ) $skillBracketTypeID . "
							";
			$data = $DB->select ( $sql );
			$ret ['debug'] .= p ( $sql, 1 );

			$ret ['data'] = $data;
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "skillBracketTypeID == 0";
		}
		$ret ['debug'] .= "End getSkillBracketData <br>\n";
		return $ret;
	}


	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkAndSetSkillBracketOfUser($steamID = 0) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkAndSetSkillBracketOfUser <br>\n";
		
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
		
		if($steamID > 0){
			for($i=1; $i<=2; $i++){
				switch($i){
					case 1:
						$matchTypeID = 1;
						break;
					case 2:
						$matchTypeID = 8;
						break;
					case 3:
						$matchTypeID = 5;
						break;
				}
				$checkTimestamp = 120; // 2 mins
				$sql = "SELECT *
							FROM `UserSkillBracket`
							WHERE SteamID = ".secureNumber($steamID)." AND MatchTypeID = ".(int) $matchTypeID." -- AND LastCheckTimestamp <= ".(time()-$checkTimestamp)."
			";
				$data = $DB->select($sql);
				$ret['debug'] .= p($sql,1);
				if(is_array($data) && count($data) > 0){
					//$ret['debug'] .= p($data,1);
					if($data['LastCheckTimestamp'] <= (time()-$checkTimestamp)){
						$currentSkillBracket = $data['SkillBracketTypeID'];
						$retSB = $this->getSkillBracketOfUserByStats($steamID, $matchTypeID);
						$skillBracketTypeIDByStats = $retSB['data'];
						if($currentSkillBracket != $skillBracketTypeIDByStats){
							$ret['debug'] .= p("SkillBRacket-Änderung - (SB: ".$currentSkillBracket." SBCalc:".$skillBracketTypeIDByStats.")!",1);
							// league updaten
							$newLeague = $skillBracketTypeIDByStats;
							$sql = "UPDATE `UserSkillBracket`
					SET SkillBracketTypeID = ".(int) $newLeague.", SkillBracketTypeIDBefore = ".(int) $currentSkillBracket.", ChangeTimestamp = ".time().", LastCheckTimestamp = ".time()."
								WHERE SteamID = ".secureNumber($steamID)." AND MatchTypeID = ".(int) $matchTypeID."
								";
							$ret['debug'] .= p($sql,1);
							$data2 = $DB->update($sql);
							$ret['debug'] .= p($data2,1);
							$ret['status'] = "skillbracket erfolgreich geändert";
						}
						else{
							$sql = "UPDATE `UserSkillBracket`
					SET LastCheckTimestamp = ".time()."
								WHERE SteamID = ".secureNumber($steamID)." AND MatchTypeID = ".(int) $matchTypeID."
								";
							$ret['debug'] .= p($sql,1);
							$data2 = $DB->update($sql);
							$ret['status'] = "keine Aenderung";
						}
					}
					else{
						$ret['status'] = "zu frueh";
					}
				
				}
				else{
					// MatchType noch nciht angelegt
					$ret['debug'] .= p("MatchType noch nciht angelegt",1);
					$insertArray = array();
					$insertArray['SteamID'] = secureNumber($steamID);
					$insertArray['MatchTypeID'] = (int)$matchTypeID;
					$insertArray['SkillBracketTypeID'] = 2;
					$insertArray['SkillBracketTypeIDBefore'] = 0;
					
					$retIns = $DB->insert("UserSkillBracket", $insertArray);
					$ret['debug'] .= p($retIns,1);
				}
			}
			
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End checkAndSetSkillBracketOfUser <br>\n";

		return $ret;
	}
}

?>