<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class SingleLadder extends Ladder{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGeneralLadderPlayers($steamID=0, $reload=0, $smarty=null, $limit="all"){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getGeneralLadderPlayers <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($limit != "all"){
			$limitSQL = "LIMIT ".(int) $limit;
		}
		else{
			$limitSQL = "";
		}
		$sqlWins = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 1
						";
		$sqlLosses = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 2
						";
		$sqlCredits = "
					SELECT SUM(Vote) as Credits
				FROM `UserCredits`
				WHERE SteamID = u.SteamID
				";
		$sqlWinRate = "
					IF((".$sqlWins.")+(".$sqlLosses.") > 0, 
					ROUND(((".$sqlWins.")/((".$sqlWins.")+(".$sqlLosses.")))*100,2)
				,0)
				";
		$sql="SELECT u.Name, u.Avatar, u.SteamID, u.BasePoints, 
				(".$sqlWins.") as Wins, 
				(".$sqlLosses.") as Loses,
				(".$sqlCredits.") as Credits, 
				".$sqlWinRate." as WinRate, 		
				IF(SUM(up.PointsChange)+u.BasePoints > 0, SUM(up.PointsChange)+u.BasePoints, 0) as Rank
				FROM User u
				LEFT JOIN `UserPoints` up ON up.SteamID = u.SteamID
				LEFT JOIN UserSkillBracket usb ON u.SteamID = usb.SteamID

				-- WHERE usb.SkillBracketTypeID > 1
				GROUP BY u.SteamID
				HAVING Rank > 0
				ORDER BY usb.SkillBRacketTypeID DESC, Rank DESC, Wins DESC

				".$limitSQL."
						";
		$ret['debug'] = p($sql,1);
		$data = $DB->multiSelect($sql);




		if($reload == 1){
			$ret['data'] = $data;
			$ret['debug'] .= $sql;

			// an Variable das Template fetchen
				
			// StartNummber finden von Player
			$startNumber = $this->getStartDataNumber($data, $steamID);
				
				
			$idTable = "SingleLadderTableGeneral";
			$smarty->assign('steamID', $steamID);
			$smarty->assign('data', $data);
			$smarty->assign('TableID',$idTable);
			$table = $smarty->fetch("ladder/tablePrototype.tpl");
			$ret['startDataNumber'] = $startNumber;
			$ret["table"] = $table;
			$ret['TableID'] = $idTable;
			$ret['debug'] .= $sql;
		}
		else{
			// StartNummber finden von Player
			$startNumber = $this->getStartDataNumber($data, $steamID);
			$ret['startDataNumber'] = $startNumber;
			$ret['data'] = $data;
			$ret['debug'] .= $sql;
		}
		$ret['debug'] .= "End getGeneralLadderPlayers <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function loadLadderDataTable($steamID, $matchModeID, $smarty, $limit="all"){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($limit != "all"){
			$limitSQL = "LIMIT ".(int) $limit;
		}
		else{
			$limitSQL = "";
		}

		$ret['debug'] .= "Start 	function loadLadderDataTable <br>\n";
		if($matchModeID > 0){
			$sql="SELECT u.Name, u.Avatar, u.SteamID, ue.Elo, ue.Wins, ue.Loses, ue.WinRate, SUM(Vote) as Credits, ue.WinRate as WinLoseRatio, ue.Elo as Rank
					FROM User u JOIN UserElo ue ON u.SteamID = ue.SteamID
					LEFT JOIN UserCredits uc ON uc.SteamID = u.SteamID
					WHERE MatchModeID = ".(int)$matchModeID." AND MatchTypeID = 1
							AND (ue.Wins > 0 OR ue.Loses > 0)
							GROUP BY u.SteamID
							ORDER BY ue.Elo DESC, ue.Wins DESC
							".$limitSQL."
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);
				
			$ret['data'] = $data;
				
			if($steamID == 0){
				$steamID = $_SESSION['user']['steamID'];
			}
				
			// StartNummber finden von Player
			$startNumber = $this->getStartDataNumber($data, $steamID);
				
			// an Variable das Template fetchen
			$idTable = "SingleLadderTable".$matchModeID;
			$smarty->assign('steamID', $steamID);
			$smarty->assign('data', $data);
			$smarty->assign('TableID',$idTable);
			$table = $smarty->fetch("ladder/tablePrototype.tpl");
			$ret['startDataNumber'] = $startNumber;
			$ret["table"] = $table;
			$ret['TableID'] = $idTable;
		}
		else{
			$ret['debug'] .= "MatchModeID 0";
		}

		$ret['debug'] .= "End 	function loadLadderDataTable <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getStartDataNumber($data, $steamID){
		if($steamID > 0){
			if(is_array($data) && count($data) > 0){
				$startNumber = recursive_array_search($steamID, $data);

				$rest = $startNumber % 10;
					
				$startNumber = $startNumber - $rest;

			}
			else{
				$startNumber = 0;
			}

				
		}
		else{
			$startNumber = 0;
		}

		return $startNumber;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getBestPlayersFetchedData($smarty=null, $matchModeID=0){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$steamID = $_SESSION['user']['steamID'];

		$ret['debug'] .= "Start getBestPlayersFetchedData <br>\n";

		if($matchModeID == "general"){
			$dataRet = $this->getGeneralLadderPlayers(0, 0, null, 5);
			$data = $dataRet['data'];
				
			$ret['debug'] .= p($dataRet,1);
				
			$smarty->assign('data', $data);
			$smarty->assign('steamID', $steamID);
			$table = $smarty->fetch("index/loggedIn/wallOfFame/bestPlayers/bestPlayersTable.tpl");
				
			$ret['data'] = $table;
		}
		else{
			$dataRet = $this->loadLadderDataTable($steamID, $matchModeID, $smarty, 5);
			$data = $dataRet['data'];
				
			$ret['debug'] .= p($dataRet,1);
				
			$smarty->assign('data', $data);
			$smarty->assign('steamID', $steamID);
			$table = $smarty->fetch("index/loggedIn/wallOfFame/bestPlayers/bestPlayersTable.tpl");

			$ret['data'] = $table;
		}


		$ret['status'] = true;

		$ret['debug'] .= "End getBestPlayersFetchedData <br>\n";

		return $ret;

	}

}

?>