<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Match{

	public $leaverGrenze = 6;
	const submissionForCancel = 2;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchData($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchData <br>\n";
		if($matchID > 0){
			$sql="SELECT m.*, mm.Name as MatchMode, mt.Name as MatchType, mm.Shortcut, r.Name as Region, r.Shortcut as RegionShortcut
					FROM `Match` m JOIN MatchMode mm ON mm.MatchModeID = m.MatchModeID
					JOIN MatchType mt ON mt.MatchTypeID = m.MatchTypeID
					JOIN Region r ON m.Region = r.RegionID
					WHERE MatchID = ".(int) $matchID."
							";
			$data = $DB->select($sql);
			$ret['data'] =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getMatchData <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function acceptMatch($matchID){
		$ret = array();
		$matchID = (int) $matchID;
		$DB = new DB();
		$con = $DB->conDB();
		$Queue = new Queue();
		$steamID = $_SESSION['user']["steamID"];

		$log = new KLogger ( "log.txt" , KLogger::INFO );

		if($steamID > 0 && $matchID > 0){
			$sql = "UPDATE MatchTeams
					SET Ready = 1
					WHERE SteamID = ".secureNumber($steamID)." AND MatchID = ".(int)$matchID."
							";
			$DB->update($sql,0);
			$updateSuccess = mysql_affected_rows();
			if($updateSuccess == 0){
				$sql = "SELECT *
						FROM `MatchTeams`
						WHERE SteamID = ".secureNumber($steamID)." AND MatchID = ".(int)$matchID."
								";
				$data = $DB->multiSelect($sql);

				$log->LogInfo("update MatchTeams beim accepten fehlgeschlagen - SteamID:".$steamID." MatchID:".$matchID." Daten in MatchTeams �ber spieler: ".print_r($data,1)." BrowserDaten:".print_r(get_browser(null, true),1));	//Prints to the log file
			}
			else{
				$log->LogInfo("update MatchTeams OK - SteamID:".$steamID." MatchID:".$matchID);	//Prints to the log file
			}

			//$ret['sql'] = $sql;
			// Aus Queue entfernen, da schon n schritt weiter
			//$Queue->leaveQueue();

			$ret['status'] = true;
		}
		else{
			$log->LogInfo("accept Match fehlgeschlagen - SteamID:".$steamID." MatchID:".$matchID);	//Prints to the log file
			$ret['status'] = false;
		}

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function declineMatch($matchID){
		$ret = array();
		$matchID = (int) $matchID;
		$DB = new DB();
		$con = $DB->conDB();
		$steamID = $_SESSION['user']["steamID"];
		if($steamID > 0){
			$sql = "UPDATE MatchTeams
					SET Ready = 0
					WHERE SteamID = ".secureNumber($steamID)." AND MatchID = ".(int)$matchID."
							";
			$DB->update($sql,0);
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "not logged in!";
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkAllReadyForMatch($matchID){
		$ret = array();
		$matchID = (int) $matchID;
		$DB = new DB();
		$con = $DB->conDB();
		$steamID = $_SESSION['user']["steamID"];
		if($steamID > 0){
			$sql = "SELECT mt.*, u.Name as Name, u.Avatar as Avatar, m.MatchTypeID
					FROM MatchTeams mt JOIN User u ON u.SteamID = mt.SteamID
					JOIN `Match` m ON mt.MatchID = m.MatchID
					WHERE mt.MatchID = ".(int)$matchID." -- AND Ready = 1
							LIMIT 10
							";
			$data = $DB->multiSelect($sql);

			// Count berechnen
			if(is_array($data) && count($data) > 0){
				$count = 0;
				foreach($data as $k =>$v){
					if($v['Ready'] == 1){
						$count++;
					}
					$matchTypeID = $v['MatchTypeID'];
				}
			}

			$ret['status'] = true;
			$ret['data'] = $data;
			$ret['matchTypeID'] = (int) $matchTypeID;
			$ret['countReady'] = $count;
		}
		else{
			$ret['status'] = "not logged in!";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getReadyMatchData($matchID){
		$ret = array();
		$matchID = (int) $matchID;
		$DB = new DB();
		$con = $DB->conDB();

		$matchID = (int) $matchID;
		if($matchID > 0){

			// MatchTypeID auslesen
			$sql = "SELECT MatchTypeID, ManuallyCheck, MatchModeID
					FROM `Match`
					WHERE MatchID = ".(int)$matchID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$matchTypeID = $data['MatchTypeID'];
			$matchModeID = $data['MatchModeID'];
			$manuallyCheck = $data['ManuallyCheck'];

			// zuerst auslesen ob cancel Aufträge existieren gruppiert nach MatchID
			$sql = "SELECT COUNT(VoteForPlayer) as Count, VoteForPlayer
					FROM `MatchDetailsCancelMatchVotes`
					WHERE Reason = 1 AND MatchID = ".(int)$matchID."
							GROUP BY MatchID, VoteForPlayer
							HAVING Count >= ".(int)RELEVANTCANCELCOUNT."
									";

			$countLeaver = $DB->countRows($sql);
			$ret['debug'] .= p($sql,1);
			$ret['debug'] .= p($countLeaver,1);

			// Prüfen ob genügend votes eingegangen sind
			// Count berechnen anhand der potentiellen Leaver
			$tmpCount = (int) floor((10 - $countLeaver) / 100 * MATCHCANCELBORDER);

			$creditSQL = "SELECT SUM(uc.Vote) FROM UserCredits uc WHERE uc.SteamID = md.SteamID";

			$pointsChange = "SELECT SUM(up.PointsChange) as PointsChange
					FROM UserPoints up
					WHERE up.SteamID = md.SteamID AND md.MatchID = up.MatchID";


			$sqlWins = "SELECT COUNT(*)
					FROM `UserPoints`
					WHERE SteamID = u.SteamID AND PointsTypeID = 1
					";
			$sqlLosses = "SELECT COUNT(*)
					FROM `UserPoints`
					WHERE SteamID = u.SteamID AND PointsTypeID = 2
					";
			$sqlLeaves = "SELECT COUNT(*)
					FROM `UserPoints`
					WHERE SteamID = u.SteamID AND PointsTypeID = 5
					";

			$sqlWinRate = "
					IF((".$sqlWins.")+(".$sqlLosses.") > 0,
							ROUND(((".$sqlWins.")/((".$sqlWins.")+(".$sqlLosses.")))*100,2)
									,0)
									";

			$sql = "SELECT u.SteamID, u.Name, u.Avatar, u.ProfileURL, t.Name as Team, md.TeamID, g.Name as GroupName, md.GroupID,
					(".$creditSQL.") as Credits,
							sb.WinPoints as WinPoints, sb.LosePoints as LosePoints,
							(".$pointsChange.") as PointsChange, Elo as Points,
									(".$sqlWins.") as Wins, (".$sqlLosses.") as Losses, (".$sqlWinRate.") as WinRate, (".$sqlLeaves.") as Leaves
											FROM MatchDetails as md
											LEFT JOIN UserSkillBracket usb ON usb.SteamID = md.SteamID AND usb.MatchTypeID = ".(int) $matchTypeID."
													LEFT JOIN SkillBracketType sb ON sb.SkillBracketTypeID = usb.SkillBracketTypeID
													LEFT JOIN UserPoints up ON up.SteamID = md.SteamID AND md.MatchID = up.MatchID
													LEFT JOIN `Group` g ON md.GroupID = g.GroupID
													LEFT JOIN `User` u ON md.SteamID = u.SteamID
													LEFT JOIN `Team` t ON t.TeamID = md.TeamID
														
													WHERE md.MatchID = ".(int)$matchID."
															GROUP BY md.SteamID
															LIMIT 10
															";
			$ret['debug'] .= $sql;
			$data = $DB->multiSelect($sql);
			//$ret['debug'] .= p($data,1);
			// Points auslesen
			// 			$UserPoints = new UserPoints();
			// 			$retUP = $UserPoints->getAllGlobalPointsOfMatch($matchID);
			// 			$pointsArray = $retUP['data'];

			// Host f�r Match auslesen
			$MatchDetailsHostLobby = new MatchDetailsHostLobby();
			$hostRet = $MatchDetailsHostLobby->getHostForMatch($matchID);
			$hostSteamID = $hostRet['data']['SteamID'];
			$hostName = $hostRet['data']['Name'];
			$hostAvatar = $hostRet['data']['Avatar'];

			// data aufbereiten, Player in richtige Unterarrays aufteilen team1 und team2
			if(is_array($data) && count($data)>0){
				$ret['data']['team1'] = array();
				$ret['data']['team2'] = array();

				foreach($data as $k => $v){
					$steamID = $v['SteamID'];
					//$v['Points'] = $pointsArray[$v['SteamID']];

					// ist leaver?
					$leaverData = $this->playerLeftTheMatch($steamID, $matchID);
					$v['Leaver'] = $leaverData['left'];

					if($v['TeamID'] == 1){
						array_push($ret['data']['team1'],$v);
					}
					else{
						array_push($ret['data']['team2'],$v);
					}
				}

				// Matchdetails berechnen, wie duruchschnitts-Elo von team1 ect
				$ret['matchdetails'] = array();

				$ret['matchdetails']['host'] = $hostSteamID;
				$ret['matchdetails']['hostName'] = $hostName;
				$ret['matchdetails']['hostAvatar'] = $hostAvatar;

				$ret['matchdetails']['manuallyCheck'] = $manuallyCheck;

				// für team1 und team2
				for($i=1; $i<=2; $i++){
					$elo_sum = 0;
					foreach($ret['data']['team'.$i.''] as $k => $v){
						$elo_sum += $v['Points'];
					}
					switch($matchTypeID){
						case "8":
							$rank_team = (int) $elo_sum;
							break;
						case "9":
							$rank_team = (int) $elo_sum/3;
							break;
						default:
							$rank_team = (int) $elo_sum / 5;
					}

					$ret['matchdetails']['ave_rank_team'.$i.''] = round($rank_team,0);
				}

				//$ret = $this->calculateAllWinLoseElos($ret);

				// PointBonus auslesen
				$MatchModePointBonus = new MatchModePointBonus();
				$retMMPB = $MatchModePointBonus->getPointBonusForMatchModeByMatchID($matchID);
				$ret['matchdetails']['PointBonus'] = $retMMPB['data']['Bonus'];
				// Teams nach Points sortieren
				$ret['data']['team1'] = orderArrayBy($ret['data']['team1'],'Points',SORT_DESC);
				$ret['data']['team2'] = orderArrayBy($ret['data']['team2'],'Points',SORT_DESC);

				// get Uploaded Screenshots
				$retUSC = $this->getAllUploadedScreenshotsToMatch($matchID);
				$ret['screenshots'] = $retUSC['data'];

				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
				$ret['sql'] = $sql;
			}

		}
		else{
			$ret['status'] = "MatchID is wrong!";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function isPlayerInMatch($matchID=0, $steamID=0){
		$matchID = (int) $matchID;

		$DB = new DB();
		$con = $DB->conDB();
		$ret = false;

		if($steamID == 0){
			$steamID = $_SESSION['user']["steamID"];
		}

		if($steamID > 0){
			if($matchID > 0){

				$sql = "SELECT SteamID
						FROM MatchDetails
						WHERE MatchID = ".(int)$matchID." AND SteamID = ".secureNumber($steamID)."
								LIMIT 1
								";
				$count = $DB->countRows($sql,0);
				if($count == 1){

					$ret = true;
				}
				else{
					$ret = false;
				}
			}
			else{
				$sql = "SELECT m.MatchID
						FROM `Match` m JOIN `MatchDetails` md ON m.MatchID = md.MatchID
						WHERE md.SteamID = ".secureNumber($steamID)."
							 AND md.Submitted = 0 AND md.SubmissionFor = 0 AND SubmissionTimestamp = 0
								AND m.TeamWonID = -1 AND m.Canceled = 0 AND m.ManuallyCheck = 0 AND m.TimestampClosed = 0
								";
				$count = $DB->countRows($sql);

				if($count > 0){
					$ret = true;
				}
				else{
					$ret = false;
				}

			}
		}
		else{
			$ret = "not logged in!";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function isMatchReadyToPlay($matchID, $steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start isMatchReadyToPlay <br>\n";

		if($matchID > 0){
			$retMatchData = $this->getMatchData($matchID);
			$matchTypeID = $retMatchData['data']['MatchTypeID'];
			switch ($matchTypeID){
				// 1vs1
				case "8":
					$countSql = "(SELECT COUNT(*) FROM MatchTeams WHERE MatchID = ".(int)$matchID." AND Ready = 1)=2";
					break;
					// single 5vs5
				case "1":
					$countSql = "(SELECT COUNT(*) FROM MatchTeams WHERE MatchID = ".(int)$matchID." AND Ready = 1)=10";
					break;
			}

			$sql = "SELECT *
					FROM `Match` m LEFT JOIN MatchTeams mt ON m.MatchID = mt.MatchID
					WHERE m.MatchID = ".(int)$matchID." AND ( ".$countSql." OR m.TimestampCreated < ".(time()-60).")
							LIMIT 1
							";
			$ret['debug'] .= p($sql,1);
			$count = $DB->countRows($sql,0);

			if($count > 0){
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "matchID == 0";
		}
		$ret['debug'] .= "End isMatchReadyToPlay <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function deleteCreatedMatch($matchID){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();
		$matchID = (int) $matchID;

		$log = new KLogger ( "klogs/match/log-".date("Y-m-d").".txt" , KLogger::INFO );


		$Match = new Match();
		$retM = $Match->getMatchData($matchID);
		$matchData = $retM['data'];
		$ret['debug'] .= p($matchData,1);
		if($matchData['TimestampCreated'] >= (time()-120)){
			$log->LogInfo("Clean created Match(".$matchID.")! initiated by:".$_SESSION['user']["steamID"]);	//Prints to the log file
			$sql = "DELETE FROM `Match`
					WHERE MatchID = ".(int)$matchID."
							";
			$ret['debug'] .= p($sql,1);
			$retDEl = $DB->delete($sql);
			$ret['status'] = $retDEl;
		}
		else{
			$log->LogInfo("Couldnt Clean created Match(".$matchID.")! initiated by:".$_SESSION['user']["steamID"]." - ".$matchData['TimestampCreated']." <".(time()-120)." inMatch:".$retInMatch);	//Prints to the log file
			$ret['status'] = false;
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function calculateWinLoseEloForPlayer($rank, $rankOther){
		$ret = array();

		// Win-Elo
		$w = 1; // Win= 100%
		$c1 = 50; // Anfangskonstante
		$c2 = 400; // andere ominöse Konstante
		$eloDiff = $rank-$rankOther;
		$eTemp = $eloDiff/$c2 * (-1);
		$eNenner = 1 + pow(10,$eTemp);
		$e = 1/$eNenner;
		$winElo = (int) $c1 * ($w - $e);
		$ret['debug_elo_calc'] = "eDiff:".$eloDiff." eTemp:".$eTemp." eNenner:".$eNenner." e:".$e." winElo:".$winElo;
		$ret['WinElo'] = "+".round($winElo,0);

		// LoseElo
		$w = 0; // Lose = 0%
		$loseElo = (int) $c1 * ($w - $e);
		$ret['LoseElo'] = round($loseElo,0);

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function calculateAllWinLoseElos($array){
		$ret = array();

		for($i=1; $i<=2; $i++){
			if($i == 1){
				$rankOther = $array['matchdetails']['ave_rank_team2'];
				$rank = $array['matchdetails']['ave_rank_team1'];
			}
			else{
				$rankOther = $array['matchdetails']['ave_rank_team1'];
				$rank = $array['matchdetails']['ave_rank_team2'];
			}
			// Win/Lose Elo berechnen
			foreach($array['data']['team'.$i.''] as $k => $v){
				$tmp_array = array_merge($array['data']['team'.$i.''][$k], $this->calculateWinLoseEloForPlayer($rank, $rankOther));
				$array['data']['team'.$i.''][$k] = $tmp_array;
			}
		}
		//$array['test_elo'] = $this->calculateWinLoseEloForPlayer(1400, 1400);
		$ret = $array;
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function submitResult($selection, $matchID, $screenshot, $leaver){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();
		$matchID = (int) $matchID;
		$steamID = $_SESSION['user']["steamID"];
		$ret['debug'] .= "selection: ".$selection;
		$ret['debug'] .= "MatchID: ".$matchID." - ".$steamID."\n";
		if($steamID > 0){
			if($matchID > 0){
				$MatchDetails = new MatchDetails();

				$ret['debug'] .= "Start getMatchDataOfPlayer\n";
				$matchData =  $this->getMatchDataOfPlayer($matchID);
				$ret['debug'] .= $matchData["debug"];
				$ret['debug'] .= "End getMatchDataOfPlayer\n";
				$matchModeID = $matchData['data']['MatchModeID'];
				$matchTypeID = $matchData['data']['MatchTypeID'];

				$ret['debug'] .= "Start getReadyMatchData\n";
				//$matchDetailsData = $this->getReadyMatchData($matchID);
				$ret['debug'] .= "End getReadyMatchData\n";

				switch($selection){
					case "won":
						//$eloChangeValue = $eloChange['WinElo'];
						$submissionFor = MatchDetails::winValue;
						break;
					case "lost":
						//$eloChangeValue = $eloChange['LoseElo'];
						$submissionFor = MatchDetails::loseValue;
						break;
				}
				// Screenshot handling
				if($screenshot != ""){
					$screenshotUploaded = 1;
				}
				else{
					$screenshotUploaded = 0;
				}

				// MatchDetails updaten
				$sql = "UPDATE MatchDetails
						SET Submitted = 1, ScreenshotUploaded = ".$screenshotUploaded.", SubmissionFor = ".$submissionFor.", SubmissionTimestamp = ".time()."
								WHERE MatchID = ".(int) $matchID." AND SteamID = ".secureNumber($steamID)."
										";

				$ret['debug'] .= $sql;

				$DB->update($sql);

				// MatchDetailsLEaverVotes updaten
				$MatchDetailsLeaverVotes = new MatchDetailsLeaverVotes();
				$insertLeaverDebug = $MatchDetailsLeaverVotes->insertSelectedLeaver($matchID, $leaver, $steamID);
				$ret['debug'] .= $insertLeaverDebug['debug'];

				// Match Lock rausnehmen
				$_SESSION['matchLock'] = false;

				$ret['status'] = true;
			}
		}
		else{
			$ret['status'] = "not logged in!";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function saveMatchDetails($matchID){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();
		$matchID = (int) $matchID;
		$steamID = $_SESSION['user']["steamID"];
		//$Matchmaking = new Matchmaking();

		if($matchID > 0){

			// check if already exisits
			$sql = "SELECT MatchID
					FROM `MatchDetails`
					WHERE MatchID = ".(int) $matchID."
							";
			$count = $DB->countRows($sql,0);

			$ret['debug'] .= $sql."\n";
			// wenn noch nciht eingetragen, dann eintragen und MatchTeams löschen
			if($count == 0){
				$log = new KLogger ( "log.txt" , KLogger::INFO );
				$log->LogInfo("MatchDetails werden eingetragen: MatchID:".$matchID." SteamID:".$steamID);	//Prints to the log file

				$sql = "SELECT *
						FROM `MatchTeams`
						WHERE MatchID = ".(int) $matchID."
								";
				$data = $DB->multiSelect($sql);
				$log->LogInfo("Data in MatchTeams zu MatchID:".$matchID." SteamID:".$steamID." ".print_r($data,1));	//Prints to the log file

				$ret['debug'] .= print_r($data,1)."\n";

				if(is_array($data) && count($data) > 0){
					$values = "";
					foreach($data as $k => $v){
						$tmpArray = array();

						if($values != ""){
							$values .= ",";
						}
						$tmpArray['MatchID'] = (int) $matchID;
						$tmpArray['SteamID'] = secureNumber($v['SteamID']);
						$tmpArray['TeamID'] = (int) $v['TeamID'];
						$tmpArray['TimeStamp'] = time();
						$tmpArray['Elo'] = (int) $v['Rank'];

						//$values .= "(NULL, ".(int)$matchID.", ".$v['SteamID'].", 0, ".$v['TeamID'].", 0, ".time().", 0, '', 0, 0, )";
						$insertArray[] = $tmpArray;
					}

					// 					$sql = "INSERT INTO `MatchDetails`
					// 							VALUES ".$values."
					// 									";
					$DB->multiInsert("MatchDetails", $insertArray);
					//$DB->select($sql, 0);

					$ret['debug'] .= $sql."\n";

					$ret['status'] = true;
				}
			}
			else{
				$ret['debug'] .= "bereits eingetragen! \n";
				$ret['status'] = true;
			}
		}
		else{
			$ret['status'] = false;
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function saveMatchDetails2($matchID){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();
		$matchID = (int) $matchID;
		$steamID = $_SESSION['user']["steamID"];
		//$Matchmaking = new Matchmaking();

		if($matchID > 0){

			// check if already exisits
			$sql = "SELECT MatchID
					FROM `MatchDetails`
					WHERE MatchID = ".(int) $matchID."
							";
			$count = $DB->countRows($sql,0);

			$ret['debug'] .= $sql."\n";
			// wenn noch nciht eingetragen, dann eintragen und MatchTeams löschen
			if($count == 0){

				$sql = "SELECT *
						FROM `MatchTeams`
						WHERE MatchID = ".(int) $matchID."
								";
				$data = $DB->multiSelect($sql);

				$ret['debug'] .= print_r($data,1)."\n";

				if(is_array($data) && count($data) > 0){
					$values = "";
					foreach($data as $k => $v){
						$tmpArray = array();

						if($values != ""){
							$values .= ",";
						}
						$tmpArray['MatchID'] = (int) $matchID;
						$tmpArray['SteamID'] = secureNumber($v['SteamID']);
						$tmpArray['TeamID'] = (int) $v['TeamID'];
						$tmpArray['TimeStamp'] = time();
						$tmpArray['Elo'] = (int) $v['Rank'];
						$tmpArray['GroupID'] = (int) $v['GroupID'];
						//$values .= "(NULL, ".(int)$matchID.", ".$v['SteamID'].", 0, ".$v['TeamID'].", 0, ".time().", 0, '', 0, 0, )";
						$insertArray[] = $tmpArray;
					}

					// 					$sql = "INSERT INTO `MatchDetails`
					// 							VALUES ".$values."
					// 									";
					$DB->multiInsert("MatchDetails", $insertArray);
					//$DB->select($sql, 0);

					$ret['debug'] .= $sql."\n";

					$ret['status'] = true;
				}
			}
			else{
				$ret['debug'] .= "bereits eingetragen! \n";
				$ret['status'] = true;
			}
		}
		else{
			$ret['status'] = false;
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function saveMatchDetailsForEventMatch($matchID, $team1Data, $team2Data){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();
		//$Matchmaking = new Matchmaking();

		if($matchID > 0){

			// check if already exisits
			$sql = "SELECT MatchID
					FROM `MatchDetails`
					WHERE MatchID = ".(int) $matchID."
							";
			$count = $DB->countRows($sql,0);

			$ret['debug'] .= $sql."\n";
			// wenn noch nciht eingetragen, dann eintragen und MatchTeams löschen
			if($count == 0){
				for($i=0; $i<2; $i++){
					$insertArray = array();
					if($i==0){
						$data = $team1Data;
					}else{
						$data = $team2Data;
					}
					// F�r die 5 eintragen
					if(is_array($data) && count($data) > 0){
						$values = "";
						foreach($data as $k => $v){
							$tmpArray = array();
							$tmpArray['MatchID'] = (int) $matchID;
							$tmpArray['SteamID'] = secureNumber($v['SteamID']);
							$tmpArray['TeamID'] = (int) $v['TeamID'];
							$tmpArray['TimeStamp'] = time();
							$tmpArray['Elo'] = (int) $v['Elo'];

							$insertArray[] = $tmpArray;
						}
						$DB->multiInsert("MatchDetails", $insertArray);
					}
				}
				$ret['status'] = true;
			}
			else{
				$ret['debug'] .= "bereits eingetragen! \n";
				$ret['status'] = true;
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
	// return MatchID
	function insertNewFoundMatch($regionID, $matchTypeID, $matchModeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
			
		$insertArray["MatchID"] = NULL;
		$insertArray["TeamWonID"] = -1;
		$insertArray["MatchTypeID"] = $matchTypeID;
		$insertArray["MatchModeID"] = $matchModeID;
		$insertArray['TimestampCreated'] = time();
		$insertArray["Region"] = (int) $regionID;

		$DB->insert("Match",$insertArray);
		$matchID = mysql_insert_id();

		$log = new KLogger ( "log.txt" , KLogger::INFO );
		$log->LogInfo("Match created! MatchID:".$matchID." at ".time());	//Prints to the log file
			
		$ret['matchID'] = $matchID;
			
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertNewFoundMatch2($regionID, $matchTypeID, $matchModeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
			
		$insertArray["MatchID"] = NULL;
		$insertArray["TeamWonID"] = -1;
		$insertArray["MatchTypeID"] = (int)$matchTypeID;
		$insertArray["MatchModeID"] = (int)$matchModeID;
		$insertArray['TimestampCreated'] = time();
		$insertArray["Region"] = (int) $regionID;

		$DB->insert("Match",$insertArray);
		$matchID = mysql_insert_id();


		$ret['matchID'] = $matchID;
			
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchDataOfPlayer($matchID, $select = "*"){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		if($matchID > 0){
			$sql = "SELECT ".$select."
					FROM `Match`
					WHERE MatchID = ".(int) $matchID."
							";
			$ret['debug'] = $sql;
			$ret['data'] = $DB->select($sql);
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

	function checkSubmissions($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();


		if($matchID > 0){
			$MatchDetails = new MatchDetails();

			// wieviele leute in % schon abgestimmt
			$alreadySubmittedPercentage = $MatchDetails->getPercentageOfAlreadySubmittedResults($matchID);

			// schon über 60% Stimme abgegeben?
			if($alreadySubmittedPercentage > 60){
				$strangeSubmissions = $MatchDetails->checkForStrangeSubmissions($matchID);
			}
			else{
					
			}
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
	function matchSubmitted($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();


		if($matchID > 0){
			$sql = "SELECT TeamWonID, TimestampClosed, Canceled
					FROM `Match`
					WHERE MatchID = ".(int) $matchID."";
			$ret['debug'] = $sql;

			$data = $DB->select($sql);

			if($data['TeamWonID'] > 0 && $data['TimestampClosed'] > 0){
				$ret['submitted'] = true;
			}
			elseif($data['Canceled'] == 1){
				$ret['submitted'] = true;
			}
			else{
				$ret['submitted'] = false;
			}
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
	function getTeamWonIDOfMatch($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getTeamWonIDOfMatch <br>\n";
		if($matchID > 0){
			$sql = "SELECT TeamWonID
					FROM `Match`
					WHERE MatchID = ".(int) $matchID."
							";
			$data = $DB->select($sql);

			$ret['TeamWonID'] = $data['TeamWonID'];

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getTeamWonIDOfMatch <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getOpenMatches($steamID, $guest=false, $matchTypeID=""){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= p($matchTypeID,1);
		if($steamID > 0){
			switch($matchTypeID){
				case "8":
					$sqlmatchTypeID = "AND m.MatchTypeID = ".(int) $matchTypeID."";
					$countMembers = 2;
					$sqlMembers = "AND
							(SELECT Count(Submitted) as Count FROM `MatchDetails` WHERE Submitted = 0 AND MatchID = m.MatchID) = ".(int) $countMembers."";
					break;
				case "9":
					$sqlmatchTypeID = "AND m.MatchTypeID = ".(int) $matchTypeID."";
					$countMembers = 6;
					$sqlMembers = "AND
							(SELECT Count(Submitted) as Count FROM `MatchDetails` WHERE Submitted = 0 AND MatchID = m.MatchID) = ".(int) $countMembers."";
					break;
				case "":
					$sqlMembers = "";
					$sqlmatchTypeID = "";
					break;
				default:
					$sqlmatchTypeID = "AND m.MatchTypeID = ".(int) $matchTypeID."";
					$countMembers = 10;
					$sqlMembers = "AND
							(SELECT Count(Submitted) as Count FROM `MatchDetails` WHERE Submitted = 0 AND MatchID = m.MatchID) = ".(int) $countMembers."";
			}

			if($guest){
				$sql="SELECT DISTINCT m.MatchID, m.TimestampCreated, m.MatchTypeID, mm.Name as MatchMode, mm.Shortcut as MatchModeShortcut, md.Submitted
						FROM `Match` m JOIN `MatchDetails` md ON m.MatchID = md.MatchID
						JOIN MatchMode mm ON m.MatchModeID = mm.MatchModeID
						WHERE m.TeamWonID = -1 AND Canceled = 0  ".$sqlmatchTypeID." ".$sqlMembers."
								";
			}
			else{
				$sql="SELECT m.MatchID, m.TimestampCreated, m.MatchTypeID, mm.Name as MatchMode, mm.Shortcut as MatchModeShortcut, md.Submitted
						FROM `Match` m JOIN `MatchDetails` md ON m.MatchID = md.MatchID
						JOIN MatchMode mm ON m.MatchModeID = mm.MatchModeID
						WHERE SteamID = ".secureNumber($steamID)." ".$sqlmatchTypeID." AND ((m.TeamWonID = -1 AND Canceled = 0 ) OR md.Submitted = 0)
								";
			}


			$ret['debug'] .= $sql;
			$data = $DB->multiSelect($sql);

			if(is_array($data) && count($data) > 0){
				$tmpData2 = array();
				foreach($data as $k => $v){
					$tmpData = array();
					if($where != ""){
						$where .= " OR ";
					}
					$where .= "MatchID = ".(int) $v['MatchID'];

					$tmpData['MatchID'] = $v['MatchID'];
					$tmpData['MatchMode'] = $v['MatchMode'];
					$tmpData['MatchModeShortcut'] = $v['MatchModeShortcut'];
					$tmpData['MatchTypeID'] = $v['MatchTypeID'];
					$tmpData['TimestampCreated'] = $v['TimestampCreated'];
					$tmpData['Submitted'] = $v['Submitted'];
					$tmpData2[] = $tmpData;
				}
			}
			//$ret['debug'] .= p($tmpData2,1);

			$sql= "SELECT MatchID, COUNT(Submitted) AS SubmittedCount
					FROM MatchDetails
					WHERE (".$where.") AND Submitted = 1
							GROUP BY MatchID
							";
			$ret['debug'] .= $sql;
			$data2 = $DB->multiSelect($sql);

			// 			if(is_array($tmpData2) && count($tmpData2) > 0){
			// 				foreach($tmpData2 as $k => $v){
			// 					if(is_array($data2) && count($data2) > 0){
			// 						foreach($data2 as $kk => $vv){
			// 							if($v['MatchID'] == $vv['MatchID']){

			// 								$tmp['MatchID'] = $v['MatchID'];
			// 								$tmp['MatchMode'] = $v['MatchMode'];
			// 								$tmp['MatchModeShortcut'] = $v['MatchModeShortcut'];
			// 								$tmp['MatchTypeID'] = $v['MatchTypeID'];
			// 								$tmp['TimestampCreated'] = $v['TimestampCreated'];
			// 								$tmp['Submitted'] = $v['Submitted'];

			// 								$tmp['SubmittedCount'] = $vv['SubmittedCount'];

			// 								$retData[] = $tmp;
			// 							}
			// 						}
			// 					}
			// 				}
			// 			}
			if(is_array($data2) && count($data2) > 0){
				foreach($data2 as $kk => $vv){

					// 					$key = recursive_array_search($vv['MatchID'], $tmpData2);
					if(is_array($tmpData2) && count($tmpData2) > 0){
						foreach ($tmpData2 as $kkk => $vvv) {
							if($vvv['MatchID'] == $vv['MatchID']){
								$tmpData2[$kkk]['SubmittedCount'] = $vv['SubmittedCount'];
							}
						}
					}
					//$ret['debug'] .= p("KEY:".$key,1);
				}
			}

			$retData = $tmpData2;

			$ret['data'] = $retData;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID 0";
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchesPlayedCount(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$sql="SELECT MatchID
				FROM `Match`
				WHERE TeamWonID != -1 AND TimestampClosed != 0
				";
		$count = $DB->countRows($sql);

		$ret['count'] = $count;
		$ret['status'] = true;

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getCountOfLeaversInMatch($matchID){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getCountOfLeaversInMatch <br>\n";

		if($matchID > 0){
			$count = 0;

			$sql = "SELECT Count(VoteForPlayer) as Count, VoteForPlayer
					FROM `MatchDetailsLeaverVotes`
					WHERE MatchID = ".(int) $matchID."
							GROUP BY VoteForPlayer;
							";
			$data = $DB->multiSelect($sql);

			$count=0;

			if(is_array($data) && count($data) > 0){
				foreach($data as $k =>$v){
					if($v['Count'] >= $this->leaverGrenze){
						$count++;
					}
				}
			}

			$ret['count'] = $count;

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getCountOfLeaversInMatch <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function playerLeftTheMatch($steamID, $matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start playerLeftTheMatch <br>\n";
		if($steamID > 0 && $matchID > 0){

			$sql = "SELECT Count(VoteForPlayer) as Count, VoteForPlayer
					FROM `MatchDetailsLeaverVotes`
					WHERE MatchID = ".(int) $matchID." AND VoteForPlayer = ".secureNumber($steamID)."
							GROUP BY VoteForPlayer;
							";
			$data = $DB->select($sql);

			$sql = "SELECT Count(VoteForPlayer) as Count, VoteForPlayer
					FROM `MatchDetailsCancelMatchVotes`
					WHERE MatchID = ".(int) $matchID." AND VoteForPlayer = ".secureNumber($steamID)."
							GROUP BY VoteForPlayer;
							";
			$data2 = $DB->select($sql);

			if($data['Count'] >= $this->leaverGrenze || $data2['Count'] >= $this->leaverGrenze){
				$MatchDetails = new MatchDetails();
				$data2 = $MatchDetails->getMatchDetailsDataOfPlayer($matchID, $steamID);
				$ret['data'] = $data2['data'];
				$ret['left'] = true;
			}
			else{
				$ret['left'] = false;
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0 or matchic = 0";
		}

		$ret['debug'] .= "End playerLeftTheMatch <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function playerLeftTheMatch2($steamID, $matchID, $type=""){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start playerLeftTheMatch2 <br>\n";
		if($steamID > 0 && $matchID > 0){

			switch($type){
				case "cancelLeaver":
					$sqlTeam1 = "SELECT COUNT(*)
							FROM MatchDetailsCancelMatchVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
							WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 1
									AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer  AND tmdlv.VoteForPlayer = ".secureNumber($steamID)."
											";
					$sqlTeam2 = "SELECT COUNT(*)
							FROM MatchDetailsCancelMatchVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
							WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 2
									AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer  AND tmdlv.VoteForPlayer = ".secureNumber($steamID)."
											";

					$sql = "SELECT (".$sqlTeam1.") as team1Votes, (".$sqlTeam2.") as team2Votes, mdlv.VoteForPlayer
							FROM MatchDetailsCancelMatchVotes mdlv
							WHERE mdlv.MatchID = ".(int)$matchID."  AND mdlv.VoteForPlayer = ".secureNumber($steamID)."
									GROUP BY mdlv.VoteForPlayer
									";
					$data2 = $DB->select($sql);
					break;
				case "leaver":
					$sqlTeam1 = "SELECT COUNT(*)
							FROM MatchDetailsLeaverVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
							WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 1
									AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer  AND tmdlv.VoteForPlayer = ".secureNumber($steamID)."
											";
					$sqlTeam2 = "SELECT COUNT(*)
							FROM MatchDetailsLeaverVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
							WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 2
									AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer  AND tmdlv.VoteForPlayer = ".secureNumber($steamID)."
											";

					$sql = "SELECT (".$sqlTeam1.") as team1Votes, (".$sqlTeam2.") as team2Votes, mdlv.VoteForPlayer
							FROM MatchDetailsLeaverVotes mdlv
							WHERE mdlv.MatchID = ".(int)$matchID."  AND mdlv.VoteForPlayer = ".secureNumber($steamID)."
									GROUP BY mdlv.VoteForPlayer
									";
					$data = $DB->select($sql);
					break;
				default:
					$sqlTeam1 = "SELECT COUNT(*)
							FROM MatchDetailsLeaverVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
							WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 1
									AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer  AND tmdlv.VoteForPlayer = ".secureNumber($steamID)."
											";
					$sqlTeam2 = "SELECT COUNT(*)
							FROM MatchDetailsLeaverVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
							WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 2
									AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer  AND tmdlv.VoteForPlayer = ".secureNumber($steamID)."
											";

					$sql = "SELECT (".$sqlTeam1.") as team1Votes, (".$sqlTeam2.") as team2Votes, mdlv.VoteForPlayer
							FROM MatchDetailsLeaverVotes mdlv
							WHERE mdlv.MatchID = ".(int)$matchID."  AND mdlv.VoteForPlayer = ".secureNumber($steamID)."
									GROUP BY mdlv.VoteForPlayer
									";
					$data = $DB->select($sql);

					$sqlTeam1 = "SELECT COUNT(*)
							FROM MatchDetailsCancelMatchVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
							WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 1
									AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer  AND tmdlv.VoteForPlayer = ".secureNumber($steamID)."
											";
					$sqlTeam2 = "SELECT COUNT(*)
							FROM MatchDetailsCancelMatchVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
							WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 2
									AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer  AND tmdlv.VoteForPlayer = ".secureNumber($steamID)."
											";

					$sql = "SELECT (".$sqlTeam1.") as team1Votes, (".$sqlTeam2.") as team2Votes, mdlv.VoteForPlayer
							FROM MatchDetailsCancelMatchVotes mdlv
							WHERE mdlv.MatchID = ".(int)$matchID."  AND mdlv.VoteForPlayer = ".secureNumber($steamID)."
									GROUP BY mdlv.VoteForPlayer
									";
					$data2 = $DB->select($sql);

			}
			$ret['debug'] .= p($sql,1);
			$t1Count = $data['team1Votes'];
			$t2Count = $data['team2Votes'];
			$gesamtCount = $t1Count + $t2Count;
			$ret['debug'] .= p("G: ".$gesamtCount." T1:".$t1Count." T2:".$t2Count,1);

			$t1CountCancel = $data2['team1Votes'];
			$t2CountCancel = $data2['team2Votes'];
			$gesamtCountCancel = $t1CountCancel + $t2CountCancel;

			$ret['debug'] .= p("GC: ".$gesamtCountCancel." T1C:".$t1CountCancel." T2C:".$t2CountCancel,1);

			if(($gesamtCount >= $this->leaverGrenze || ($t1Count >= 2 AND $t2Count >= 2)) ||
			($gesamtCountCancel >= $this->leaverGrenze || ($t1CountCancel >= 2 AND $t2CountCancel >= 2))){
				$MatchDetails = new MatchDetails();
				$data2 = $MatchDetails->getMatchDetailsDataOfPlayer($matchID, $steamID);
				$ret['data'] = $data2['data'];
				$ret['left'] = true;
			}
			else{
				$ret['left'] = false;
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0 or matchic = 0";
		}

		$ret['debug'] .= "End playerLeftTheMatch2 <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPlayersWhoLeftTheMatch($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPlayersWhoLeftTheMatch <br>\n";
		if($matchID > 0){

			$sql = "SELECT Count(VoteForPlayer) as Count, VoteForPlayer
					FROM `MatchDetailsLeaverVotes`
					WHERE MatchID = ".(int) $matchID."
							GROUP BY VoteForPlayer;
							";
			$data = $DB->multiSelect($sql);
			if(is_array($data) && count($data) > 0){
				foreach($data as $k =>$v){
					if($v['Count'] >= $this->leaverGrenze){
						$MatchDetails = new MatchDetails();
						$data2 = $MatchDetails->getMatchDetailsDataOfPlayer($matchID, $v['VoteForPlayer']);
						$ret['data'][] = $data2['data'];
					}
				}
			}



			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getPlayersWhoLeftTheMatch <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPlayersWhoLeftTheMatch2($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPlayersWhoLeftTheMatch2 <br>\n";
		if($matchID > 0){

			$sqlTeam1 = "SELECT COUNT(*)
					FROM MatchDetailsLeaverVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
					WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 1
							AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer
							";
			$sqlTeam2 = "SELECT COUNT(*)
					FROM MatchDetailsLeaverVotes tmdlv LEFT JOIN MatchDetails md ON md.SteamID = tmdlv.SteamID
					WHERE tmdlv.MatchID = ".(int)$matchID." AND md.MatchID = ".(int)$matchID." AND md.TeamID = 2
							AND tmdlv.VoteForPlayer = mdlv.VoteForPlayer
							";

			$sql = "SELECT (".$sqlTeam1.") as team1Votes, (".$sqlTeam2.") as team2Votes, mdlv.VoteForPlayer
					FROM MatchDetailsLeaverVotes mdlv
					WHERE mdlv.MatchID = ".(int)$matchID."
							GROUP BY mdlv.VoteForPlayer
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);
			if(is_array($data) && count($data) > 0){
				foreach($data as $k =>$v){
					$t1Count = $v['team1Votes'];
					$t2Count = $v['team2Votes'];
					$gesamtCount = $t1Count + $t2Count;
					$ret['debug'] .= p("G: ".$gesamtCount." T1:".$t1Count." T2:".$t2Count,1);
					if($gesamtCount >= $this->leaverGrenze || ($t1Count >= 2 AND $t2Count >= 2)){
						$MatchDetails = new MatchDetails();
						$data2 = $MatchDetails->getMatchDetailsDataOfPlayer($matchID, $v['VoteForPlayer']);
						$ret['data'][] = $data2['data'];
					}
				}
				$ret['status'] = true;
			}else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getPlayersWhoLeftTheMatch2 <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getHandicappedTeam($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getHandicappedTeam <br>\n";
		if($matchID > 0){

			$data = $this->getPlayersWhoLeftTheMatch2($matchID);
			$ret['debug'] .= p($data,1);
			$data = $data['data'];

			// bestimmten welches Team benachteiligt war
			$count = array();
			if(is_array($data) && count($data) > 0){
				foreach($data as $k =>$v){
					$ret['debug'] .= p($v,1);
					$count[$v['TeamID']]++;
				}
			}
			$ret['debug'] .= p($count,1);
			// gr��eren Wert nach vorne bringen
			arsort($count);
			$teamID = key($count);
			$ret['debug'] .= p($count,1);

			// wenn gr��erer wert minus kleinerer gr��er als 1 dann ein Team benachteiligt sonst fair

			switch($teamID){
				case 1:
					$otherTeam = 2;
					break;
				case 2:
					$otherTeam = 1;
					break;
			}

			$check = $count[$teamID] - $count[$otherTeam];

			$ret['debug'] .= "check danach: ".$check." \n";
			if($check > 0){
				$ret['teamID'] = $teamID;
				$ret['count'] = $count[$teamID];
			}
			else{
				$ret['teamID'] = false;
				$ret['count'] = 0;
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getHandicappedTeam <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function cancelMatch($matchID, $voteArray, $reason){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$steamID = $_SESSION['user']['steamID'];

		if($matchID > 0 && $steamID > 0){

			// Match erstma hard canceln (MatchDetails)
			$this->cancelMatchHard($matchID);

			if(is_array($voteArray) && count($voteArray) > 0){
				$insertArray = array();
				foreach ($voteArray as $k => $v) {
					$tmp['SteamID'] = secureNumber($steamID);
					$tmp['MatchID'] = (int)$matchID;
					$tmp['VoteForPlayer'] = secureNumber($v);
					$tmp['Timestamp'] = time();
					$tmp['Reason'] = (int) $reason;
					$ret['debug'] .= p($tmp,1);
					$DB->insert("MatchDetailsCancelMatchVotes", $tmp);
				}

				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
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
	function playerVotedForCancelingMatch($steamID, $matchID){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID > 0 && $matchID > 0){

			$sql = "SELECT *
					FROM `MatchDetails` md  LEFT JOIN `MatchDetailsCancelMatchVotes` mdcmv ON md.MatchID = mdcmv.MatchID AND md.SteamID = mdcmv.SteamID
					WHERE (mdcmv.SteamID = ".secureNumber($steamID)." AND mdcmv.MatchID = ".(int)$matchID.") OR
							(md.SubmissionFor = ".Match::submissionForCancel." AND md.MatchID = ".(int)$matchID." AND md.SteamID = ".secureNumber($steamID).")
									";
			$count = $DB->countRows($sql);
			$ret['debug'] .= p($sql,1);

			if ($count > 0) {
				$ret['canceled'] = true;
			}
			else {
				$ret['canceled'] = false;
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID 0 && matchID 0";
		}
		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function matchAlreadyCanceled($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start matchAlreadyCanceled <br>\n";

		if($matchID > 0){

			$sql = "SELECT *
					FROM `Match`
					WHERE Canceled = 1 AND MatchID = ".(int) $matchID."
							";
			$count = $DB->countRows($sql);
			$ret['debug'] .= p($sql,1);

			if($count > 0){
				$ret['canceled'] = true;
			}
			else{
				$ret['canceled'] = false;
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End matchAlreadyCanceled <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function cancelMatchHard($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start cancelMatchHard <br>\n";

		$steamID = $_SESSION['user']['steamID'];

		if($matchID > 0 && $steamID > 0){
			$MatchDetails = new MatchDetails();

			$cancelValue = (int) MatchDetails::chancelValue;

			// in MatchDetails protokollieren
			$sql = "UPDATE `MatchDetails`
					SET Submitted = 1, SubmissionTimestamp = ".time().", SubmissionFor = ".$cancelValue."
							WHERE SteamID = ".secureNumber($steamID)." AND MatchID = ".(int) $matchID."
									";

			$DB->update($sql);

			// 			$sql="UPDATE `Match`
			// 			 		SET Canceled = 1
			// 					WHERE MatchID = ".(int) $matchID."
			// 					";
			// 			$DB->update($sql);

			$log = new KLogger ( "log.txt" , KLogger::INFO );
			$log->LogInfo("Match ".$matchID." canceled by ".$steamID."");	//Prints to the log file

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End cancelMatchHard <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchStateForUser($matchID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		$ret['debug'] .= "Start getMatchStateForUser <br>\n";
		if($matchID > 0 && $steamID>0){

			$retSubmitted = $this->matchSubmitted($matchID);
			$submitted = $retSubmitted['submitted'];

			$playerInMatch = $this->isPlayerInMatch($matchID, $steamID);

			$playerSubmitted = $this->gerPlayerSubmittedMatchResult($matchID, $steamID);
			$ret['debug'] .= p($playerSubmitted,1);
			$ret['data']['playerOfMatch'] = $playerInMatch;
			$ret['data']['MatchClosed'] = $submitted;
			$ret['data']['playerSubmitted'] = $playerSubmitted['submitted'];

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getMatchStateForUser <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function gerPlayerSubmittedMatchResult($matchID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start gerPlayerSubmittedMatchResult <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($matchID > 0 && $steamID > 0){

			$playerInMatch = $this->isPlayerInMatch($matchID, $steamID);

			if($playerInMatch){
				$sql = "SELECT *
						FROM `MatchDetails`
						WHERE SteamID = ".secureNumber($steamID)." AND MatchID = ".(int) $matchID."
								AND Submitted != 0 AND SubmissionFor != 0 AND SubmissionTimestamp != 0
								";
				$ret['debug'] .= p($sql,1);
				$count = $DB->countRows($sql);
					
				if($count > 0){
					$ret['submitted'] = true;
				}
				else{
					$ret['submitted'] = false;
				}
			}
			else{
				$ret['submitted'] = "not in match";
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End gerPlayerSubmittedMatchResult <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLastPlayedMatches($matchModeID=0, $smarty=null, $limit=6){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLastPlayedMatches <br>\n";

		if($matchModeID > 0){
			$where = "AND m.MatchModeID = ".(int)$matchModeID;
		}
		if($limit>0){
			$sql = "SELECT m.*, mm.Name as MatchModeName, mm.Shortcut as MatchModeShortcut
					FROM `Match` m JOIN MatchMode mm ON mm.MatchModeID = m.MatchModeID
					WHERE m.TeamWonID != -1 AND m.TimestampClosed != 0
					AND m.Canceled = 0 AND ManuallyCheck = 0 ".$where."

							ORDER BY m.MatchID DESC
							LIMIT ".(int)$limit."
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			if($smarty != null){
				$smarty->assign('data', $data);
				$table = $smarty->fetch("index/loggedIn/wallOfFame/lastMatches/lastMatchesTable.tpl");
				$ret['data'] = $table;
			}
			else{
				$ret['data'] = $data;
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "limit = 0";
		}

		$ret['debug'] .= "End getLastPlayedMatches <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchesPlayedOverTime($start, $end){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchesPlayedOverTime <br>\n";
		if($end > 0){
			$startWhere = "";
			if($start > 0){
				$startWhere = " AND TimestampClosed >= ".(int)$start;
			}
			$sql = "SELECT *
					FROM `Match`
					WHERE TimestampClosed <= ".(int)$end." ".$startWhere."
							AND Canceled = 0 AND TeamWonID != -1
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "end = 0";
		}

		$ret['debug'] .= "End getMatchesPlayedOverTime <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchesPlayedCountYesterdayOfAllPlayers($filter=VOTEBONUSFORMATCHES){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchesPlayedCountYesterdayOfAllPlayers <br>\n";

		if($filter > 0){
			$having = " HAVING MatchesCount >= ".(int)$filter;
		}

			
		$startTS = strtotime("yesterday");
		$endTS = time();
			
		$sql = "SELECT u.SteamID, COUNT(m.MatchID) as MatchesCount
				FROM `User` u LEFT JOIN `MatchDetails` md ON u.SteamID = md.SteamID
				LEFT JOIN `Match` m ON md.MatchID = m.MatchID
				WHERE m.TimestampClosed >= ".(int)$startTS." AND m.TimestampClosed <= ".$endTS."
						GROUP BY u.SteamID
						".$having."
								";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);
			
		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getMatchesPlayedCountYesterdayOfAllPlayers <br>\n";
		return $ret;
	}


	function getAllUploadedScreenshotsToMatch($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllUploadedScreenshotsToMatch <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($matchID > 0){
			$alle = glob(DR.HOME_PATH_REL.'uploads/match/screenshots/'.(int)$matchID.'/*');
			if(is_array($alle) && count($alle) > 0){
				$images = array();
				foreach ($alle as $k => $v) {
					$host = $_SERVER['HTTP_HOST'].HOME_PATH_REL;
					$tmp = explode("/uploads/", $v);
					$pathToImage = $tmp[1];
					$path = "http://".$host."/uploads/".$pathToImage;

					$images[] = $path;
				}
					
				$ret['data'] = $images;
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "matchID == 0";
		}
		$ret['debug'] .= "End getAllUploadedScreenshotsToMatch <br>\n";
		return $ret;
	}

	function getMatchDataForChangeMatchResults($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchDataForChangeMatchResults <br>\n";

		if($matchID > 0){
			// MatchTypeID auslesen
			$sql = "SELECT m.*, mm.Name as MatchMode, mt.Name as MatchType, mm.Shortcut as MatchModeShortcut, r.Name as Region, r.Shortcut as RegionShortcut
					FROM `Match` m JOIN MatchMode mm ON mm.MatchModeID = m.MatchModeID
					JOIN MatchType mt ON mt.MatchTypeID = m.MatchTypeID
					JOIN Region r ON m.Region = r.RegionID
					WHERE MatchID = ".(int) $matchID."
							";
			$ret['debug'] .= p($sql,1);
			$generalMatchData = $DB->select($sql);
				
			$ret['generalMatchData'] = $generalMatchData;
				
			$matchTypeID = $generalMatchData['MatchTypeID'];
			$matchModeID = $generalMatchData['MatchModeID'];
			$manuallyCheck = $generalMatchData['ManuallyCheck'];
				
			$creditSQL = "SELECT SUM(uc.Vote) FROM UserCredits uc WHERE uc.SteamID = md.SteamID";
				
			$pointsChange = "SELECT SUM(up.PointsChange) as PointsChange
					FROM UserPoints up
					WHERE up.SteamID = md.SteamID AND md.MatchID = up.MatchID";
				
				
			$sqlWins = "SELECT COUNT(*)
					FROM `UserPoints`
					WHERE SteamID = u.SteamID AND PointsTypeID = 1
					";
			$sqlLosses = "SELECT COUNT(*)
					FROM `UserPoints`
					WHERE SteamID = u.SteamID AND PointsTypeID = 2
					";
			$sqlLeaves = "SELECT COUNT(*)
					FROM `UserPoints`
					WHERE SteamID = u.SteamID AND PointsTypeID = 5
					";
				
			$sqlWinRate = "
					IF((".$sqlWins.")+(".$sqlLosses.") > 0,
							ROUND(((".$sqlWins.")/((".$sqlWins.")+(".$sqlLosses.")))*100,2)
									,0)
									";
				
			$sql = "SELECT u.SteamID, u.Name, u.Avatar, u.ProfileURL, t.Name as Team, md.TeamID,
					(".$creditSQL.") as Credits,
							sb.WinPoints as WinPoints, sb.LosePoints as LosePoints,
							(".$pointsChange.") as PointsChange, Elo as Points,
									(".$sqlWins.") as Wins, (".$sqlLosses.") as Losses, (".$sqlWinRate.") as WinRate, (".$sqlLeaves.") as Leaves
											FROM MatchDetails as md
											LEFT JOIN UserSkillBracket usb ON usb.SteamID = md.SteamID AND usb.MatchTypeID = ".(int) $matchTypeID."
													LEFT JOIN SkillBracketType sb ON sb.SkillBracketTypeID = usb.SkillBracketTypeID
													LEFT JOIN UserPoints up ON up.SteamID = md.SteamID AND md.MatchID = up.MatchID,
													User as u, Team as t
													WHERE (md.SteamID = u.SteamID AND md.TeamID = t.TeamID)
													AND
													md.MatchID = ".(int)$matchID."
															GROUP BY md.SteamID
															LIMIT 10
															";
			$ret['debug'] .= $sql;
			$data = $DB->multiSelect($sql);
			//$ret['debug'] .= p($data,1);
			// Points auslesen
			// 			$UserPoints = new UserPoints();
			// 			$retUP = $UserPoints->getAllGlobalPointsOfMatch($matchID);
			// 			$pointsArray = $retUP['data'];
				
			// Host f�r Match auslesen
			$MatchDetailsHostLobby = new MatchDetailsHostLobby();
			$hostRet = $MatchDetailsHostLobby->getHostForMatch($matchID);
			$hostSteamID = $hostRet['data']['SteamID'];
			$hostName = $hostRet['data']['Name'];
			$hostAvatar = $hostRet['data']['Avatar'];
				
			// data aufbereiten, Player in richtige Unterarrays aufteilen team1 und team2
			if(is_array($data) && count($data)>0){
				$ret['data']['team1'] = array();
				$ret['data']['team2'] = array();
					
				foreach($data as $k => $v){
					$steamID = $v['SteamID'];
					//$v['Points'] = $pointsArray[$v['SteamID']];
						
					// ist leaver?
					$leaverData = $this->playerLeftTheMatch($steamID, $matchID);
					$v['Leaver'] = $leaverData['left'];
						
					// get all UserPoints
					$UserPoints = new UserPoints();
					$retUP = $UserPoints->getAllPointsEntriesOfUserForMatch($matchID, $steamID);
					$v['PointChanges'] = $retUP['data'];
						
					if($v['TeamID'] == 1){
						array_push($ret['data']['team1'],$v);
					}
					else{
						array_push($ret['data']['team2'],$v);
					}
				}
					
				// Matchdetails berechnen, wie duruchschnitts-Elo von team1 ect
				$ret['matchdetails'] = array();
					
				$ret['matchdetails']['host'] = $hostSteamID;
				$ret['matchdetails']['hostName'] = $hostName;
				$ret['matchdetails']['hostAvatar'] = $hostAvatar;
					
				$ret['matchdetails']['manuallyCheck'] = $manuallyCheck;
					
				// für team1 und team2
				for($i=1; $i<=2; $i++){
					$elo_sum = 0;
					foreach($ret['data']['team'.$i.''] as $k => $v){
						$elo_sum += $v['Points'];
					}
					switch($matchTypeID){
						case "8":
							$rank_team = (int) $elo_sum;
							break;
						case "9":
							$rank_team = (int) $elo_sum/3;
							break;
						default:
							$rank_team = (int) $elo_sum / 5;
					}
						
					$ret['matchdetails']['ave_rank_team'.$i.''] = round($rank_team,0);
				}
					
				//$ret = $this->calculateAllWinLoseElos($ret);
					
				// PointBonus auslesen
				$MatchModePointBonus = new MatchModePointBonus();
				$retMMPB = $MatchModePointBonus->getPointBonusForMatchModeByMatchID($matchID);
				$ret['matchdetails']['PointBonus'] = $retMMPB['data']['Bonus'];
				// Teams nach Points sortieren
				$ret['data']['team1'] = orderArrayBy($ret['data']['team1'],'Points',SORT_DESC);
				$ret['data']['team2'] = orderArrayBy($ret['data']['team2'],'Points',SORT_DESC);
					
				// get Uploaded Screenshots
				$retUSC = $this->getAllUploadedScreenshotsToMatch($matchID);
				$ret['screenshots'] = $retUSC['data'];
					
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
				$ret['sql'] = $sql;
			}
		}
		else{
			$ret['status'] = "matchID == 0";
		}
		$ret['debug'] .= "End getMatchDataForChangeMatchResults <br>\n";
		return $ret;

	}

	function getMatchesPlayedCountOfUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchesPlayedCountOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT *
					FROM `Match` m JOIN `MatchDetails` md ON md.MatchID = m.MatchID
					WHERE Canceled = 0 AND TeamWonID != -1 AND md.SteamID = ".secureNumber($steamID)."
							";
			$ret['debug'] .= p($sql,1);
			$count = $DB->countRows($sql);
			
			$ret['data'] = (int) $count;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getMatchesPlayedCountOfUser <br>\n";
		return $ret;
	}

}

?>