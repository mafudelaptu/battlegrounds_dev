<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserElo{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getEloOfMatchMode($steamID, $matchTypeID, $matchModeID){
		$ret = 0;
		if($steamID > 0){
			$DB = new DB();
			$con = $DB->conDB();
			$sql = "SELECT Elo
					FROM UserElo
					WHERE SteamID = ".$steamID." AND MatchTypeID = ".(int)$matchTypeID." AND MatchModeID = ".(int)$matchModeID."
							";
			$ret = $DB->select($sql);
			$ret = $ret['Elo'];
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllEloValuesOfUser($steamID, $justPlayed=false, $matchModeNames=false){
		$ret = array();
		if($steamID > 0){
			$DB = new DB();
			$con = $DB->conDB();


			$zusatz = "";
			if($justPlayed){
				$zusatz = " AND (Wins != 0 OR Loses != 0)";
			}

			$matchModeJoin = "";
			if($matchModeNames){
				$matchModeJoin = " ue JOIN MatchMode mm ON ue.MatchModeID = mm.MatchModeID";
			}

			$sql = "SELECT *
					FROM UserElo ".$matchModeJoin."
							WHERE SteamID = ".secureNumber($steamID)." ".$zusatz."
									";
			$data = $DB->multiSelect($sql);

			// Daten umformen in folgende Struktur:
			// 			array(
			// 					[{matchTypeID}] = array(
			// 							[{matchModeID}] = {Elo},
			// 							[{matchModeID}] = {Elo},
			// 							...
			// 					),
			// 					[{matchTypeID}] = array(
			// 							[{matchModeID}] = {Elo},
			// 							[{matchModeID}] = {Elo},
			// 							...
			// 					),
			// 					..
			// 					)
			if(is_array($data) && count($data)>0){
				foreach($data as $k => $v){
					$ret[$v['MatchTypeID']][$v['MatchModeID']]['Elo'] = $v['Elo'];
					$ret[$v['MatchTypeID']][$v['MatchModeID']]['Wins'] = $v['Wins'];
					$ret[$v['MatchTypeID']][$v['MatchModeID']]['Loses'] = $v['Loses'];
					$ret[$v['MatchTypeID']][$v['MatchModeID']]['WinRate'] = $v['WinRate'];

					if($matchModeNames){
						$ret[$v['MatchTypeID']][$v['MatchModeID']]['MatchModeName'] = $v['Name'];
					}

				}
			}
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertFirstElo($steamID, $stats){
		$ret = array();
		$ret['status'] = false;

		if((int) $steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$DB = new DB();
			$con = $DB->conDB();

			$MatchMode = new MatchMode();
			$all_matchmodes = $MatchMode->getAllMatchModes("MatchModeID",false);
			$MatchType = new MatchType();
			$all_matchtypes = $MatchType->getAllMatchTypes("MatchTypeID",false);
			$elo = $this->calculateFirstElo($stats);

			if(is_array($all_matchtypes) AND count($all_matchtypes)>0){
				$insertArray = array();
				foreach($all_matchtypes as $k_mt => $v_mt){
					if(is_array($all_matchmodes) AND count($all_matchmodes)>0){
							
						foreach($all_matchmodes as $k_mm => $v_mm){
							$tmp = array();
							$tmp['SteamID'] = secureNumber($steamID);
							$tmp['MatchTypeID'] = (int)$v_mt['MatchTypeID'];
							$tmp['MatchModeID'] =  (int)$v_mm['MatchModeID'];
							$tmp['Elo'] = (int)$elo;
							$insertArray[] = $tmp;
						}
					}
				}

				$DB->multiInsert("UserElo", $insertArray);
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}

		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function calculateFirstElo($stats=false, $steamID=0){
		if($stats){
			$elo = 0;
			if($stats["ratio"] != 0){
				$elo = (int)(START_ELO + (10*($stats['ratio']-50)));
			}
			else{
				$elo = (int)(START_ELO);
			}
		}
		else{
			$UserStats = new UserStats();
			$stats = $UserStats->getUserStats($steamID, "WinLoseRatio");
			$ratio = $stats['data']['WinLoseRatio'];
			if($ratio != 0){
				$elo = (int)(START_ELO + (10*($ratio-50)));
			}
			else{
				$elo = (int)(START_ELO);
			}
		}
		return $elo;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertEloToMatchMode($steamID, $elo, $matchModeID, $matchTypeID){
		$ret = array();
		$ret['debug'] .= "Start insertEloToMatchMode <br>\n";
		if($steamID > 0){
			$DB = new DB();
			$con = $DB->conDB();

			$sql = "UPDATE UserElo
					Set Elo=".(int)$elo."
							WHERE SteamID = ".secureNumber($steamID)." AND MatchModeID = ".(int)$matchModeID." AND MatchTypeID = ".(int)$matchTypeID."
									";
			$DB->update($sql);

			$ret['debug'] .= $sql;
		}
		$ret['debug'] .= "End insertEloToMatchMode <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function changeUserEloAfterMatchSubmit($matchModeID, $matchTypeID, $teamWonID, $matchID){
		$ret = array();
		$ret['debug'] .= "Start changeUserEloAfterMatchSubmit <br>\n";
		$ret['debug'] .= "MATCHID: ".$matchID." <br>\n";
		if($matchID > 0){

			$DB = new DB();
			$con = $DB->conDB();
			$MatchDetails = new MatchDetails();
			$Match = new Match();
			$allPlayers = $MatchDetails->getAllPlayersInMatch($matchID);
			$ret['debug'] .= p($allPlayers,1);
			$allPlayers = $allPlayers['data'];


			if(is_array($allPlayers) && count($allPlayers) > 0){
				foreach($allPlayers as $k => $v){
					$playerSteamID = $v['SteamID'];
					$eloChange = $v['EloChange'];
					$teamID = $v['TeamID'];
					$playerElo = $this->getEloOfMatchMode($playerSteamID, $matchTypeID, $matchModeID);

					$eloChange = substr($eloChange, 1);

					if($teamWonID == $teamID){
						$endElo = $playerElo + (int) $eloChange;
						$result = 1;
					}
					else{
						$endElo = $playerElo - (int) $eloChange;
						$result = -1;
					}

					// Allgemeine Stats updaten
					$UserStats = new UserStats();
					$retUpdateUserStatsElo = $UserStats->updateUserStatsElo($playerSteamID, $eloChange, $result);
					$ret['debug'] .= $retUpdateUserStatsElo['debug'];
					$ret['debug'] .= "SteamID: ".$playerSteamID." ELO: ".$playerElo." EloChange: ".$eloChange." EndElo: ".$endElo."<br>\n";
					$retUpdate .= $this->insertEloToMatchMode($playerSteamID, $endElo, $matchModeID, $matchTypeID);
					$ret['debug'] .= p($retUpdate['debug'],1);
				}
			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End changeUserEloAfterMatchSubmit <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function updateUserEloStats($steamID, $result, $matchTypeID, $matchModeID){
		$ret = array();
		if($steamID > 0){
			$DB = new DB();
			$con = $DB->conDB();

			switch($result){
				// WIN
				case "1":
					$set = "SET Wins = Wins+1
							";
					break;
					// LOSE
				case "-1":
					$set = "SET Loses = Loses+1
							";
					break;
				default: $set = "SET Wins = Wins";
			}

			// neue WinRate setzen
			$set .= ", WinRate = (Wins/(Wins+Loses))*100";

			$sql = "UPDATE UserElo
					".$set."
							WHERE SteamID = ".secureNumber($steamID)." AND MatchTypeID = ".(int) $matchTypeID." AND MatchModeID = ".(int) $matchModeID."
									";
			$DB->update($sql);

			$ret['debug'] = $sql;
			$ret['status'] = true;
		}
		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getEloRoseData($steamID = ""){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getEloRoseData <br>\n";

		if($steamID == ""){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){

			$data = $this->getAllEloValuesOfUser($steamID,true,true);

			$ret['debug'] .= p($data,1);

			if(is_array($data[1]) && count($data[1]) > 0){
				foreach($data[1] as $k =>$v){
					$retKeys[] = $v['MatchModeName'];
					$retData[] = (int) $v['Elo'];
					$retSilverBorder[] = (int) SILVERELO;
					$retGoldBorder[] = (int) GOLDELO;
					$retDiamondBorder[] = (int) DIAMONDELO;
					$retDataTest[] = (int) rand(1000, 1900);
				}
			}
			$ret['debug'] .= p($retData,1);
			$ret['data'] = $retData;
			$ret['keys'] = $retKeys;

			$ret['silver'] = $retSilverBorder;
			$ret['gold'] = $retGoldBorder;
			$ret['diamond'] = $retDiamondBorder;

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End getEloRoseData <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function changeUserElo($matchModeID, $matchTypeID, $steamID, $eloChange, $positive=true){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start changeUserElo <br>\n";

		if($matchModeID > 0 && $matchTypeID > 0 && $steamID > 0 && $eloChange > 0){

			if($positive){
				$zeichen = "+";
			}
			else{
				$zeichen = "-";
			}

			$sql = "UPDATE `UserElo`
					SET Elo = Elo ".$zeichen." ".(int)$eloChange."
							WHERE SteamID = ".secureNumber($steamID)." AND MatchModeID = ".(int)$matchModeID."	AND MatchTypeID = ".(int) $matchTypeID."

									";
			$ret['debug'] .= p($sql,1);
			$retUpdate = $DB->update($sql);

			$ret['status'] = $retUpdate;
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | teamID = 0";
		}


		$ret['debug'] .= "End changeUserElo <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkIfUserHaveEloForMatchType($matchTypeID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkIfUserHaveEloForMatchType <br>\n";

		if((int) $steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT *
					FROM `UserElo`
					WHERE SteamID = ".secureNumber($steamID)." AND MatchTypeID = ".(int) $matchTypeID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->countRows($sql);
				
			if($data > 0){
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End checkIfUserHaveEloForMatchType <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertFirstEloForMatchType($matchTypeID,$steamID=0){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start insertFirstEloForMatchType <br>\n";
		if((int) $steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$DB = new DB();
			$con = $DB->conDB();

			$MatchMode = new MatchMode();
			$all_matchmodes = $MatchMode->getAllMatchModes("MatchModeID",false);
			$elo = $this->calculateFirstElo($stats);

			if(is_array($all_matchmodes) AND count($all_matchmodes)>0){
					
				foreach($all_matchmodes as $k_mm => $v_mm){
					$tmp = array();
					$tmp['SteamID'] = secureNumber($steamID);
					$tmp['MatchTypeID'] = (int)$matchTypeID;
					$tmp['MatchModeID'] =  (int)$v_mm['MatchModeID'];
					$tmp['Elo'] = (int)$elo;
					$insertArray[] = $tmp;
				}
			}
			$ret['debug'] .= $DB->multiInsert("UserElo", $insertArray,1);
			$DB->multiInsert("UserElo", $insertArray);
			$ret['status'] = true;

		}
		$ret['debug'] .= "End insertFirstEloForMatchType <br>\n";
		return $ret;
	}

}

?>