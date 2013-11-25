<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserElos{
	const experiencedPlayerBorder = 300;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function calculateBaseEloOfUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start calculateBaseEloOfUser <br>\n";
		
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		
		if($steamID > 0){
			
			$DomParser = new DomParser();
			$dotaBuffRet = $DomParser->parseDotaBuffStats($steamID);
			$ret['debug'] .= p($dotaBuffRet,1);
			
			$wins = $dotaBuffRet['data']['Wins'];
			$loses = $dotaBuffRet['data']['Loses'];
			$totalGames =  $dotaBuffRet['data']['TotalGames'];
			$winRate =  $dotaBuffRet['data']['WinRate'];
			$startElo = START_ELO;
			$baseElo = 0;
			
			// (startElo + totalGames/10(aufgerundet) + Wins/10 + WinRate-50 * 20) 
			// prüfen ob WinRate einbezogen werden kann
			$userWinRate = false;
			if($totalGames >= UserElos::experiencedPlayerBorder){
				$userWinRate = true;
			}
			
			$tmpValue = $startElo + (int)ceil($totalGames/10) + (int)ceil($wins/10);
			$ret['debug'] .= p($tmpValue,1);
			
			
			if($userWinRate){
				$baseElo = (int) $tmpValue + ceil(($winRate - 50) * 20);
			}
			else{
				$baseElo = (int) $tmpValue;
			}
			
			$ret['debug'] .= p($baseElo,1);
			
			$ret['data']  = (int) $baseElo;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End calculateBaseEloOfUser <br>\n";

		return $ret;
	}
}

?>