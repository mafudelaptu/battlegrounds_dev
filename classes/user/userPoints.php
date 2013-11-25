<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserPoints{
	const experiencedPlayerBorder = 300;
	const pointsForUpload = "5";
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function calculateBasePointsOfUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start calculateBasePointsOfUser <br>\n";

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
			$startPoints = 0;
			$basePoints = 0;

			// (startPoints + totalGames/10(aufgerundet) + Wins/10 + WinRate-50 * 20)
			// prï¿½fen ob WinRate einbezogen werden kann
			$userWinRate = false;
			if($totalGames >= UserPoints::experiencedPlayerBorder){
				$userWinRate = true;
			}

			$tmpValue = $startPoints + (int)ceil($totalGames/10) + (int)ceil($wins/10);
			$ret['debug'] .= p($tmpValue,1);


			if($userWinRate){
				$basePoints = (int) $tmpValue + ceil(($winRate - 50) * 10);
			}
			else{
				$basePoints = (int) $tmpValue;
			}

			$ret['debug'] .= p($basePoints,1);

			$ret['data']  = (int) $basePoints;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End calculateBasePointsOfUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGlobalPointsOfUser($steamID = 0, $matchTypeID = 0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPointsOfUser <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}



		if($steamID > 0){
			$where = "";
			switch($matchTypeID){
				// single 5vs5
				case "0":
				case "1":
					$where = " AND (up.MatchTypeID = 1 OR up.MatchTypeID = 0)";
					break;
					// 1vs1
				case "8":
					$where = " AND up.MatchTypeID = ".(int) $matchTypeID."";
					break;

			}
			// 			if($matchTypeID > 0){
			// 				$where = " AND (up.MatchTypeID = ".(int) $matchTypeID." OR up.MatchTypeID = 0)";
			// 			}

			$sql = "SELECT SUM(up.PointsChange) as Points, u.BasePoints
					FROM `User` u LEFT JOIN `UserPoints` up  ON u.SteamID = up.SteamID ".$where."
							WHERE u.SteamID = ".secureNumber($steamID)."
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			$points = $data['BasePoints'] + $data['Points'];

			if($points < 0){
				$points = 0;
			}
			$ret['points'] = (int)$data['Points'];
			$ret['basePoints'] = $data['BasePoints'];
			$ret['data']  = (int) $points;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End getPointsOfUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertBasePoints($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertBasePoints <br>\n";
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		if($steamID > 0){
			// stats von Dotabuff auslesen
			$User = new User();
			$retStats = $this->calculateBasePointsOfUser($steamID);
			$basePoints = $retStats['data'];

			$insertArray = array();

			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['MatchModeID'] = 0;
			$insertArray['MatchTypeID'] = 0;
			$insertArray['MatchID'] = 0;
			$insertArray['PointsTypeID'] = 4;
			$insertArray['PointsChange'] = (int) $basePoints;
			$insertArray['Timestamp'] = time();

			$retINs = $DB->insert("UserPoints", $insertArray);

			$ret['status'] = $retINs;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End insertBasePoints <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGameStatsOfUser($steamID=0, $matchTypeID = 1){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getGameStatsOfUser <br>\n";
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		if($steamID > 0){
			$wins = 0;
			$losses = 0;
			$totalGames = 0;
			$winRate = 0;

			// Wins
			$sql = "SELECT COUNT(SteamID) as Wins
					FROM `UserPoints`
					WHERE SteamID = ".secureNumber($steamID)."
							AND PointsTypeID = 1
							AND MatchTypeID = ".(int) $matchTypeID."
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$wins = (int) $data['Wins'];

			// Losses
			$sql = "SELECT COUNT(SteamID) as Losses
					FROM `UserPoints`
					WHERE SteamID = ".secureNumber($steamID)."
							AND PointsTypeID = 2
							AND MatchTypeID = ".(int) $matchTypeID."
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$losses = (int) $data['Losses'];

			// Leaves
			$sql = "SELECT COUNT(SteamID) as Leaves
					FROM `UserPoints`
					WHERE SteamID = ".secureNumber($steamID)."
							AND PointsTypeID = 5
							AND MatchTypeID = ".(int) $matchTypeID."
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$leaves = (int) $data['Leaves'];

			$totalGames = (int) $wins+$losses;
			if($totalGames > 0){
				$winRate = round(($wins/$totalGames)*100,2);
			}
			else{
				$winRate = 0;
			}

			$data['Wins'] = $wins;
			$data['Losses'] = $losses;
			$data['TotalGames'] = $totalGames;
			$data['WinRate'] = $winRate;
			$data['Leaves'] = $leaves;

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End getGameStatsOfUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllGlobalPointsOfMatch($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllGlobalPointsOfMatch <br>\n";
		if($matchID > 0){

			$sql = "SELECT *
					FROM `MatchDetails` md JOIN `Match` m ON md.MatchID = m.MatchID
					WHERE md.MatchID = ".(int)$matchID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			if(is_array($data) && count($data) > 0){
				foreach($data as $k =>$v){
					$steamID = $v['SteamID'];
					$matchTypeID = $v['MatchTypeID'];
					$retPoints = $this->getGlobalPointsOfUser($steamID, $matchTypeID);
					$data[$steamID] = $retPoints['data'];
				}
			}

			$ret['data']  = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getAllGlobalPointsOfMatch <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertPointChanges($matchID, $teamWonID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$Match = new Match();
		$ret['debug'] .= "Start insertPointChanges <br>\n";
		if($matchID > 0){
			$MatchDetails = new MatchDetails();
			$data = $MatchDetails->getAllPlayersInMatchSkillBracket($matchID);
			$data = $data['data'];

			$ret['debug'] .= p($data,1);
			$ret['debug'] .= p($aveEloTeams,1);

			// checken obs leaver gab
			$leaver = $Match->getCountOfLeaversInMatch($matchID);

			// wenn leaver vorhanden, dann benachteiligtes Team herausfinden
			if((int) $leaver['count'] > 0){
				$handicappedTeamID = $Match->getHandicappedTeam($matchID);
				$handicappedTeamID = $handicappedTeamID['teamID'];
				$handicappedCountLeaver = $handicappedTeamID['count'];
				$ret['debug'] .= "handicappedTeam: ".$handicappedTeamID." \n";
			}

			$MatchModePointBonus = new MatchModePointBonus();
			$retMMPB = $MatchModePointBonus->getPointBonusForMatchModeByMatchID($matchID);
			$bonusPoints = $retMMPB['data']['Bonus'];

			if(is_array($data) && count($data) > 0){
				foreach($data as $k => $v){
					$steamID = $v['SteamID'];
					$teamID = $v['TeamID'];
					$winPoints = $v['WinPoints'];
					$losePoints =  $v['LosePoints'];

					// Bonus fï¿½r Matchmode hinzufï¿½gen
					$winPoints = $winPoints + $bonusPoints;


					// wenn handicapped Team dann Elo anpassen
					if($handicappedTeamID == $teamID){
						$leaver = $Match->playerLeftTheMatch2($v['SteamID'], $matchID);
						// wenn leaver dann doppelt bestrafen
						if($leaver['left']){
							$eloChange = LEAVEMATCHPUNISHMENT*(-1);
							$pointType = 5;
						}
						// wenn kein Leaver, dann lose auf 0 reduzieren
						else{
							// WIN -> +Elo
							if($teamWonID == $teamID){
								$eloChange = $winPoints;
								$pointType = 1;
							}
							// LOSE -> -Elo
							else{
								$eloChange = -0;
								$pointType = 2;
							}
						}
					}
					else{
						// WIN -> +Elo
						if($teamWonID == $teamID){
							$eloChange = $winPoints;
							$pointType = 1;
						}
						// LOSE -> -Elo
						else{
							$eloChange = $losePoints*(-1);
							$pointType = 2;
						}
					}

					if($pointType > 0){
						$insertArray = array();
						$insertArray['SteamID'] = secureNumber($steamID);
						$insertArray['MatchModeID'] = (int) $v['MatchModeID'];
						$insertArray['MatchTypeID'] = (int) $v['MatchTypeID'];
						$insertArray['MatchID'] = (int) $matchID;
						$insertArray['PointsTypeID'] = (int) $pointType;
						$insertArray['PointsChange'] = (int) $eloChange;
						$insertArray['Timestamp'] = (int) time();

						$ret['debug'] .= p($insertArray,1);
						$retINs = $DB->insert("UserPoints", $insertArray);
						$ret['status'] = $retINs;

						if($pointType == "1"){
							// WinStreamBonus
							$retWS = $this->getWinStreakOfPlayer($steamID);
							$winStreak = $retWS['data'];
							if($winStreak >= WINSTREAKBORDER){
								$diffWinsStream = $winStreak-WINSTREAKBORDER;
								switch($diffWinsStream){
									case "0": // 3 WIns
										$winstreakBonus = 5;
										break;
									case "1": // 4 Wins
										$winstreakBonus = 10;
										break;
									case "2": // 5 Wins
										$winstreakBonus = 20;
										break;
											
									default: // >= 6 Wins
										$winstreakBonus = 40;
											
								}
								$insertArray = array();
								$insertArray['SteamID'] = secureNumber($steamID);
								$insertArray['MatchModeID'] = (int) $v['MatchModeID'];
								$insertArray['MatchTypeID'] = (int) $v['MatchTypeID'];
								$insertArray['MatchID'] = (int) $matchID;
								$insertArray['PointsTypeID'] = (int) 8;
								$insertArray['PointsChange'] = (int) $winstreakBonus;
								$insertArray['Timestamp'] = (int) time();
								$ret['debug'] .= p("pointstype 1",1);
								$ret['debug'] .= p($insertArray,1);
								$retINs = $DB->insert("UserPoints", $insertArray);
								$ret['status'] = $retINs;
							}
						}

					}
					else{
						$ret['status'] = "pointsType = 0";
					}


				}
			}

		}
		else{
			$ret['status'] = "MatchID 0";
		}
		$ret['debug'] .= "End insertPointChanges <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertEventRewardPointBonus($steamID, $eventID, $rewardPoints, $matchTypeID, $matchModeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertEventRewardPointBonus <br>\n";

		if($steamID > 0){
			$insertArray = array();

			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['MatchModeID'] = (int) $matchModeID;
			$insertArray['MatchTypeID'] = (int) $matchTypeID;
			$insertArray['MatchID'] = 0;
			$insertArray['EventID'] = (int) $eventID;
			$insertArray['PointsTypeID'] = 4;
			$insertArray['PointsChange'] = (int) $rewardPoints;
			$insertArray['Timestamp'] = time();

			//$retINs2 = $DB->insert("UserPoints", $insertArray,$con,1);
			$retINs = $DB->insert("UserPoints", $insertArray);

			$ret['status'] = $retINs;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End insertEventRewardPointBonus <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPointChangesOfMatch($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPointChangesOfMatch <br>\n";
		if($matchID > 0){

			$sql = "SELECT *
					FROM `UserPoints`
					WHERE MatchID = ".(int) $matchID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getPointChangesOfMatch <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllPointsOfUser($steamID, $justPlayed=false, $matchModeNames=false){
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
				$matchModeJoin = " JOIN MatchMode mm ON up.MatchModeID = mm.MatchModeID";
				$selectMatchModeJoin = "mm.Name,";
			}

			$sqlPointsEarned = "
					SELECT IF(SUM(PointsChange) > 0, SUM(PointsChange), 0)
					FROM `UserPoints`
					WHERE SteamID = ".secureNumber($steamID)."
							AND MatchModeID = up.MatchModeID
							AND MatchTypeID = up.MatchTypeID
							";
			$sql = "SELECT DISTINCT up.SteamID, up.MatchModeID, ".$selectMatchModeJoin." up.MatchTypeID,
					(".$sqlPointsEarned.") as PointsEarned
							FROM UserPoints up ".$matchModeJoin."
									WHERE SteamID = ".secureNumber($steamID)."

											";
			$ret['debug'] .= p($sql,1);
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
					$ret[$v['MatchTypeID']][$v['MatchModeID']]['PointsEarned'] = $v['PointsEarned'];
					// 					$ret[$v['MatchTypeID']][$v['MatchModeID']]['Wins'] = $v['Wins'];
					// 					$ret[$v['MatchTypeID']][$v['MatchModeID']]['Loses'] = $v['Loses'];
					// 					$ret[$v['MatchTypeID']][$v['MatchModeID']]['WinRate'] = $v['WinRate'];

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
	function getPointRoseData($steamID = ""){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getEloRoseData <br>\n";

		if($steamID == ""){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){

			$data = $this->getAllPointsOfUser($steamID,true,true);

			$ret['debug'] .= p($data,1);

			if(is_array($data[1]) && count($data[1]) > 0){
				foreach($data[1] as $k =>$v){
					$retKeys[] = $v['MatchModeName'];
					$retData[] = (int) $v['PointsEarned'];
					//	$retDataTest[] = (int) rand(1000, 1900);
				}
			}
			$ret['debug'] .= p($retData,1);
			$ret['data'] = $retData;
			$ret['keys'] = $retKeys;

			// 			$ret['silver'] = $retSilverBorder;
			// 			$ret['gold'] = $retGoldBorder;
			// 			$ret['diamond'] = $retDiamondBorder;

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
	function getPointsHistoryData($matchModeID, $matchTypeID, $count="*", $steamID = 0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$matchModeID = (int) $matchModeID;
		$matchTypeID = (int) $matchTypeID;
		$ret['debug'] .= $steamID;
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($matchModeID == "*"){
			$matchSQL = "AND (up.MatchTypeID = ".(int)$matchTypeID." OR up.MatchTypeID = 0)";
		}
		else{
			$matchSQL = "AND up.MatchID > 0
					AND (up.MatchTypeID = ".(int)$matchTypeID." AND up.MatchModeID = ".(int)$matchModeID.")";
		}

		if($steamID > 0 && ($matchModeID > 0 || $matchModeID == "*") && $matchTypeID > 0){
			switch ($matchTypeID){
				case "1":
					$whereMatchTypeID = " AND (up.MatchTypeID = ".(int)$matchTypeID." OR up.MatchTypeID = 0)";
					break;
				default:
					$whereMatchTypeID = " AND up.MatchTypeID = ".(int)$matchTypeID;
			}
			$sql = "SELECT mt.Name as MatchType, mm.Name as MatchMode, up.PointsChange as PointsChange, u.BasePoints, up.MatchID, up.EventID,  pt.Name as PTName
					FROM UserPoints up
					LEFT JOIN `Match` m  ON up.MatchID = m.MatchID
					LEFT JOIN MatchMode mm ON mm.MatchModeID = m.MatchModeID
					LEFT JOIN MatchType mt ON mt.MatchTypeID = m.MatchTypeID
					LEFT JOIN User u ON up.SteamID = u.SteamID
					LEFT JOIN PointsType pt ON pt.PointsTypeID = up.PointsTypeID
					WHERE up.SteamID = ".secureNumber($steamID)." ".$matchSQL.$whereMatchTypeID."
							ORDER BY up.Timestamp ASC
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);

			// fÃ¼r Highchart aufbereiten
			if(is_array($data) && count($data) > 0){
				$i = 1;
				if($matchModeID == "*"){
					$elo = (int)$data[0]['BasePoints'];
				}
				else{
					$elo = 0;
				}

				// Startwert hinzufï¿½gen
				$retData[-1] = $elo;
				$retKeys[-1] = 0;
				$retPointsType[-1] = "";
				$retMatchMode[-1] = "";
				$retMatchType[-1] = "";
				$retPointsChange[-1] = 0;
				$retIDText[-1] = "Initialization";

				foreach ($data as $k => $v) {
					$pointsChange  = $v['PointsChange'];
					$elo += (int)$pointsChange;
					$retData[] = $elo;
					$retKeys[] = $v['MatchID'];
					$retPointsType[] = $v['PTName'];
					$retMatchMode[] = $v['MatchMode'];
					$retMatchType[] = $v['MatchType'];
					$retPointsChange[] =  $v['PointsChange'];

					if($v['MatchID'] == "0"){
						$retIDText[] = "Event #".$v['EventID'];
					}
					else{
						$retIDText[] = "Match #".$v['MatchID'];
					}

					$matchMode = $v['MatchMode'];
					$matchType = $v['MatchType'];

					$i++;
				}

				if($count != "*"){
					(count($retData) > (int)$count ? $anfang=(count($retData)-(int)$count): $anfang=0);
					$retData = array_slice($retData,$anfang,(int)$count);
					$retKeys = array_slice($retKeys,$anfang,(int)$count);
					$retPointsType = array_slice($retPointsType,$anfang,(int)$count);
					$retMatchMode = array_slice($retMatchMode,$anfang,(int)$count);
					$retMatchType = array_slice($retMatchType,$anfang,(int)$count);
					$retPointsChange = array_slice($retPointsChange,$anfang,(int)$count);
					$retIDText = array_slice($retIDText,$anfang,(int)$count);
				}
			}

			// anfang
			$ret['debug'] .= "#########COUNT: ".$count." \n<br>";


			// 			$ret['diamondBorder'] = DIAMONDELO;
			// 			$ret['goldBorder'] = GOLDELO;
			// 			$ret['silverBorder'] = SILVERELO;


			$ret['data'] = $retData;

			$ret['xAxis'] = $retKeys;
			$ret['matchModeArr'] = $retMatchMode;
			$ret['MatchTypeArr'] = $retMatchType;
			$ret['pointsType'] = $retPointsType;
			$ret['pointsChange'] = $retPointsChange;
			$ret["idText"] = $retIDText;
			$ret['matchType'] = $matchType;
			$ret['matchMode'] = $matchMode;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID 0 oder matchmodeID = 0";
		}
		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getWallOfFameData(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$sqlPoints = "SELECT IF(SUM(PointsChange)+u.BasePoints > 0, SUM(PointsChange)+u.BasePoints, 0)
				FROM `UserPoints`
				WHERE SteamID = up.SteamID
				";

		$sqlWins = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 1
				";
		$sqlLosses = "SELECT COUNT(*)
				FROM `UserPoints`
				WHERE SteamID = u.SteamID AND PointsTypeID = 2
				";
		$sqlWinRate = "
				IF((".$sqlWins.")+(".$sqlLosses.") > 0,
						ROUND(((".$sqlWins.")/((".$sqlWins.")+(".$sqlLosses.")))*100,2)
								,0)
								";
		// Diamond

		$sql = "SELECT up.*, u.*, (".$sqlPoints.") as Points, (".$sqlWins.") as Wins,
				(".$sqlLosses.") as Loses, ".$sqlWinRate." as WinRate
						FROM UserPoints up JOIN User u ON u.SteamID = up.SteamID
						LEFT JOIN UserSkillBracket ul ON up.SteamID = ul.SteamID AND ul.MatchTypeID = 1
						LEFT JOIN SkillBracketType lt ON lt.SkillBracketTypeID = ul.SkillBracketTypeID

						WHERE ul.SkillBracketTypeID = 6
						ORDER BY Points DESC
						LIMIT 1
						";
		$ret['debug'] .= p($sql,1);
		$diamondData = $DB->multiSelect($sql);
		if(count($diamondData) > 0){
			$ret['data']['diamond'] = $diamondData[0];
		}

		// Platinum

		$sql = "SELECT up.*, u.*, (".$sqlPoints.") as Points, (".$sqlWins.") as Wins,
				(".$sqlLosses.") as Loses, ".$sqlWinRate." as WinRate
						FROM UserPoints up JOIN User u ON u.SteamID = up.SteamID
						LEFT JOIN UserSkillBracket ul ON up.SteamID = ul.SteamID AND ul.MatchTypeID = 1
						LEFT JOIN SkillBracketType lt ON lt.SkillBracketTypeID = ul.SkillBracketTypeID

						WHERE ul.SkillBracketTypeID = 5
						ORDER BY Points DESC
						LIMIT 1
						";
		$ret['debug'] .= p($sql,1);
		$platinumData = $DB->multiSelect($sql);
		if(count($platinumData) > 0){
			$ret['data']['platinum'] = $platinumData[0];
		}
		// Gold
		$sql = "SELECT up.*, u.*, (".$sqlPoints.") as Points, (".$sqlWins.") as Wins,
				(".$sqlLosses.") as Loses, ".$sqlWinRate." as WinRate
						FROM UserPoints up JOIN User u ON u.SteamID = up.SteamID
						LEFT JOIN UserSkillBracket ul ON up.SteamID = ul.SteamID AND ul.MatchTypeID = 1
						LEFT JOIN SkillBracketType lt ON lt.SkillBracketTypeID = ul.SkillBracketTypeID

						WHERE ul.SkillBracketTypeID = 4
						ORDER BY Points DESC
						LIMIT 1
						";
		$ret['debug'] .= p($sql,1);
		$goldData = $DB->multiSelect($sql);
		if(count($goldData) > 0){
			$ret['data']['gold'] = $goldData[0];
		}


		// Silver
		$sql = "SELECT up.*, u.*, (".$sqlPoints.") as Points, (".$sqlWins.") as Wins,
				(".$sqlLosses.") as Loses, ".$sqlWinRate." as WinRate
						FROM UserPoints up JOIN User u ON u.SteamID = up.SteamID
						LEFT JOIN UserSkillBracket ul ON up.SteamID = ul.SteamID AND ul.MatchTypeID = 1
						LEFT JOIN SkillBracketType lt ON lt.SkillBracketTypeID = ul.SkillBracketTypeID

						WHERE ul.SkillBracketTypeID = 3
						ORDER BY Points DESC
						LIMIT 1
						";
		$ret['debug'] .= p($sql,1);
		$silverData = $DB->multiSelect($sql);
		if(count($silverData) > 0){
			$ret['data']['silver'] = $silverData[0];
		}


		// Bronze
		$sql = "SELECT up.*, u.*, (".$sqlPoints.") as Points, (".$sqlWins.") as Wins,
				(".$sqlLosses.") as Loses, ".$sqlWinRate." as WinRate
						FROM UserPoints up JOIN User u ON u.SteamID = up.SteamID
						LEFT JOIN UserSkillBracket ul ON up.SteamID = ul.SteamID AND ul.MatchTypeID = 1
						LEFT JOIN SkillBracketType lt ON lt.SkillBracketTypeID = ul.SkillBracketTypeID

						WHERE ul.SkillBracketTypeID = 2
						ORDER BY Points DESC
						LIMIT 1
						";
		$ret['debug'] .= p($sql,1);
		$bronzeData = $DB->multiSelect($sql);
		if(count($bronzeData) > 0){
			$ret['data']['bronze'] = $bronzeData[0];
		}

		// Qualifying
		$sql = "SELECT up.*, u.*, (".$sqlPoints.") as Points, (".$sqlWins.") as Wins,
				(".$sqlLosses.") as Loses, ".$sqlWinRate." as WinRate
						FROM UserPoints up JOIN User u ON u.SteamID = up.SteamID
						LEFT JOIN UserSkillBracket ul ON up.SteamID = ul.SteamID AND ul.MatchTypeID = 1
						LEFT JOIN SkillBracketType lt ON lt.SkillBracketTypeID = ul.SkillBracketTypeID

						WHERE ul.SkillBracketTypeID = 1
						ORDER BY Points DESC
						LIMIT 1
						";
		$ret['debug'] .= p($sql,1);
		$qualiData = $DB->multiSelect($sql);
		if(count($qualiData) > 0){
			$ret['data']['quali'] = $qualiData[0];
		}

		$ret['status'] = true;

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getActivePlayersOverTime($start, $end){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getActivePlayersOverTime <br>\n";
		if($end > 0){
			$startWhere = "";
			if($start > 0){
				$startWhere = " AND Timestamp >= ".(int)$start;
			}

			$sqlPointsEarned = "
					SELECT IF(SUM(PointsChange) > 0, SUM(PointsChange), 0)
					FROM `UserPoints`
					WHERE SteamID = up.SteamID
					";
			$sql = "SELECT *, (".$sqlPointsEarned.") as EarnedPoints, u.*
					FROM `UserPoints` up JOIN `User` u ON u.SteamID = up.SteamID
					WHERE Timestamp <= ".(int)$end." ".$startWhere."
							GROUP BY up.SteamID
							ORDER BY EarnedPoints DESC
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "start = 0";
		}

		$ret['debug'] .= "End getActivePlayersOverTime <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getRaceWinnerList($start, $end, $winnerCount, $winnerCountType, $raceCoinPrizesArray=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getRaceWinnerList <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($end > 0){
			$ret['debug'] .= p("S:".$start." E:".$end,1);
			$retUP = $this->getActivePlayersOverTime($start, $end);
			//$ret['debug'] .= p($retUP,1);
			$activePlayersData = $retUP['data'];

			// Players in Winning-List
			$countActive = count($activePlayersData);
			if($winnerCountType == "Percent"){
				$countWinners = ceil($winnerCount/100 * $countActive);
				if(is_array($activePlayersData) && count($activePlayersData) > 0){
					$output = array_slice($activePlayersData, 0, $countWinners);   // liefert "a", "b" und "c"
				}

			}


			// nachgucken ob Spieler im Array schon drin   wenn nciht dann position anzeigen
			if(is_array($activePlayersData) && count($activePlayersData) > 0){
				$key = recursive_array_search($steamID, $activePlayersData);
			}

			//$ret['debug'] .= p("KEYYYYYYYYYYY:".$key,1);
			if(is_numeric($key)){
				$ret['PositionOfUser'] = (int) $key+1;
			}
			else{
				$ret['PositionOfUser'] = false;
			}

			if ($raceCoinPrizesArray) {
				$ret['debug'] .= p("test im here",1);
				if(is_array($output) && count($output) > 0){
					$posi = 1;
					$RaceCoinPrizes = new RaceCoinPrizes();
						
					foreach ($output as $k => $v) {
						$retRCP = $RaceCoinPrizes->getCoinPrizeByPlacement($posi, $raceCoinPrizesArray);
						$output[$k]['CoinPrize'] = $retRCP['data']['RewardCoins'];
						$posi++;
					}
				}
			}
			$ret['data']  =$output;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "start = 0";
		}

		$ret['debug'] .= "End getRaceWinnerList <br>\n";

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

			$Match = new Match();
			$retM = $Match->getMatchData($matchID);
			$matchData = $retM['data'];

			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['MatchModeID'] = (int) $matchData['MatchModeID'];
			$insertArray['MatchTypeID'] = (int) $matchData['MatchTypeID'];
			$insertArray['MatchID'] = $matchID;
			$insertArray['EventID'] = (int) 0;
			$insertArray['PointsTypeID'] = 7;
			$insertArray['PointsChange'] = UserPoints::pointsForUpload;
			$insertArray['Timestamp'] = time();

			$Match = new Match();
			$leaver = $Match->playerLeftTheMatch($steamID, $matchID);
			$ret['debug'] .= p($leaver,1);
			//$retINs2 = $DB->insert("UserPoints", $insertArray,$con,1);
			if(!$leaver['left']){
				$retINs = $DB->insert("UserPoints", $insertArray);
				$ret['status'] = $retINs;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] .= "matchID == 0";
		}
		$ret['debug'] .= "End insertReplayUploadBonus <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getWinStreakOfPlayer($steamID = 0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getWinStreakOfPlayer <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){

			$winStreak = $this->getWinStreakRekusive($steamID);


			$ret['data'] = $winStreak;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getWinStreakOfPlayer <br>\n";
		return $ret;
	}

	private $globalOffsetCount = 0;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getWinStreakRekusive($steamID = 0, $offset=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLastResultOfUser <br>\n";

		if($offset == 0){
			$this->globalOffsetCount = 0;
		}

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT *
					FROM `UserPoints`
					WHERE SteamID = ".secureNumber($steamID)." AND PointsChange != 0
							ORDER BY `Timestamp` DESC
							LIMIT ".(int)$offset.",1
									";

			$data = $DB->select($sql);

			switch($data['PointsTypeID']){

				case "1": // Win
					$this->globalOffsetCount++;
					return 1 + $this->getWinStreakRekusive($steamID, $this->globalOffsetCount);
					break;
				case "": // keine WErte da -> 0
				case "2": // Lose
				case "5": // Leave
					return 0;
				default:
					$this->globalOffsetCount++;
					return 0 +  $this->getWinStreakRekusive($steamID, $this->globalOffsetCount);
			}

			$ret['debug'] .= p($sql,1);

			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getLastResultOfUser <br>\n";
		return $ret;
	}

	function getAllPointsEntriesOfUserForMatch($matchID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllPointsEntriesOfUserForMatch <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($matchID > 0){
			if($steamID > 0){
				$sql = "SELECT up.MatchModeID, up.MatchTypeID, up.PointsTypeID, pt.Name as PointsTypeName, up.EventID, up.PointsChange, up.Timestamp
						FROM `UserPoints` up LEFT JOIN PointsType pt ON up.PointsTypeID = pt.PointsTypeID
						WHERE up.SteamID = ".secureNumber($steamID)." AND up.MatchID = ".(int) $matchID."
								";
				$data = $DB->multiSelect($sql);
				$ret['debug'] .= p($sql,1);

				$ret['data'] = $data;
				$ret['status'] = true;
			}
			else{
				$ret['status'] = "steamID == 0";
			}
		}
		else{
			$ret['status'] = "matchID == 0";
		}
		$ret['debug'] .= "End getAllPointsEntriesOfUserForMatch <br>\n";
		return $ret;
	}

	function saveUserPointChanges($matchID, $steamID, $userPointsData){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start saveUserPointChanges <br>\n";
		$ret['debug'] .= p($userPointsData,1);

		if($matchID > 0 && $steamID > 0){

			// Matchdaten auslesen
			$Match = new Match();
			$retM = $Match->getMatchData($matchID);
			$ret['debug'] .= p($retM,1);

			$matchData = $retM['data'];

			// vorher alle Löschen dann neu einfügen
			$sql = "DELETE FROM `UserPoints`
					WHERE SteamID = ".secureNumber($steamID)." AND MatchID = ".(int)$matchID."
							";
			$retDel = $DB->delete($sql);
			$ret['debug'] .= p($sql,1);
			$ret['debug'] .= p($retDel,1);

			$insertArray = array();
			if(is_array($userPointsData) && count($userPointsData) > 0){
				foreach ($userPointsData as $k => $v) {
					$val = $v['value'];
					$ptID = $v['pointsTypeID'];

					$tmp['SteamID'] = secureNumber($steamID);
					$tmp['MatchModeID'] = (int)$matchData['MatchModeID'];
					$tmp['MatchTypeID'] = (int)$matchData['MatchTypeID'];
					$tmp['MatchID'] = (int)($matchID);
					$tmp['PointsTypeID'] = (int)$ptID;
					$tmp['PointsChange'] = (int)$val;
					$tmp['Timestamp'] = time();

					switch ($ptID){
						case "9":
						case "10":
						case "11":
							$tmp['MatchModeID'] = 0;
							$tmp['MatchTypeID'] = 0;
							break;
						case "12":
							$tmp['MatchModeID'] = 0;
							$tmp['MatchTypeID'] = 0;
							$tmp['MatchID'] = 0;
					}

					$insertArray[] = $tmp;
				}
				$retIns = $DB->multiInsert("UserPoints", $insertArray);
				$ret['debug'] .= p($retIns,1);
				$ret['status'] = $retIns;
			}
			else{
				$ret['status'] = "no data given";
			}
		}
		else{
			$ret['status'] = "matchID == 0 || steamID == 0";
		}
		$ret['debug'] .= "End saveUserPointChanges <br>\n";
		return $ret;
	}

	function deleteAllUserPoints($matchID, $steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start deleteAllUserPoints <br>\n";

		if($matchID > 0 && $steamID > 0){
			$sql = "DELETE FROM `UserPoints`
					WHERE SteamID = ".secureNumber($steamID)." AND MatchID = ".(int) $matchID."
							";
			$data = $DB->delete($sql);
			$ret['debug'] .= p($sql,1);

			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "matchID == 0 || steamID = 0";
		}
		$ret['debug'] .= "End deleteAllUserPoints <br>\n";
		return $ret;
	}

	function deletePointsTypeOfMatchOfUser($matchID, $steamID, $pointsTypeID ){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start deletePointsTypeOfMatchOfUser <br>\n";

		if($matchID > 0 && $pointsTypeID > 0 && $steamID > 0){
			$sql = "DELETE FROM `UserPoints`
					WHERE SteamID = ".secureNumber($steamID)." AND MatchID = ".(int) $matchID." AND PointsTypeID = ".(int) $pointsTypeID."
							";
			$data = $DB->delete($sql);
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID == 0 || pointsType || steamID";
		}
		$ret['debug'] .= "End deletePointsTypeOfMatchOfUser <br>\n";
		return $ret;
	}

	function insertLeave($matchID, $steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start createChatFile <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($matchID > 0 && $steamID > 0){
			// Matchdaten auslesen
			$Match = new Match();
			$retM = $Match->getMatchData($matchID);
			$ret['debug'] .= p($retM,1);
			
			$matchData = $retM['data'];
			
			$tmp = array();
			$tmp['SteamID'] = secureNumber($steamID);
			$tmp['MatchModeID'] = (int)$matchData['MatchModeID'];
			$tmp['MatchTypeID'] = (int)$matchData['MatchTypeID'];
			$tmp['MatchID'] = (int)($matchID);
			$tmp['PointsTypeID'] = (int)5;
			$tmp['PointsChange'] = (int)-LEAVEMATCHPUNISHMENT;
			$tmp['Timestamp'] = time();
			
			$retIns = $DB->insert("UserPoints", $tmp);
			
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID == 0 || steamID = 0";
		}
		$ret['debug'] .= "End createChatFile <br>\n";
		return $ret;
	}
}

?>