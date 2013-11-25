<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class MatchDetails{
	const screenshotGrenze = 2;
	const winValue = 1;
	const loseValue = -1;
	const chancelValue = 2;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function deleteMatchDetails($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start deleteMatchDetails <br>\n";

		$steamID = $_SESSION['user']['steamID'];

		$log = new KLogger ( "log.txt" , KLogger::INFO );
		$log->LogInfo("Clean MatchDetails(".$matchID.")! initiated by:".$_SESSION['user']["steamID"]);	//Prints to the log file
		if($matchID > 0 && $steamID > 0){
			$Match = new Match();
			$retM = $Match->getMatchData($matchID);
			$matchData = $retM['data'];

			$sql = "SELECT *
					FROM `MatchDetails` md LEFT JOIN `Match` m ON m.MatchID = md.MatchID
					WHERE md.SteamID = ".secureNumber($steamID)." AND md.MatchID = ".(int) $matchID."
							";
			$count = $DB->countRows($sql);
			$ret['debug'] .= p($sql,1);
			$ret['debug'] .= p("Count:".$count,1);
			$ret['debug'] .= p("TimestampCreated:".$matchData['TimestampCreated'].">=".(time()-120),1);

			if($matchData['TimestampCreated'] >= (time()-120) && $count > 0){
				$sql = "DELETE FROM `MatchDetails`
						WHERE MatchID = ".(int) $matchID."";
				$DB->delete($sql);

				$ret['status'] = true;
			}
			else{
				$ret['debug'] .= "because fuck you!";
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "MatchID = 0 || steamID = 0";
		}



		$ret['debug'] .= "End deleteMatchDetails <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPercentageOfAlreadySubmittedResults($matchID){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();

		if($matchID > 0){

			$sql = "SELECT SteamID
					FROM `MatchDetails`
					WHERE MatchID = ".(int)$matchID." AND Submitted = 1
							";
			$countSubmitted = $DB->countRows($sql);

			$value = round($countSubmitted/10*100,0);
			$ret['value'] = $value;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID wrong";
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkForStrangeSubmissions($matchID){
		$DB = new DB();
		$con = $DB->conDB();
		$ret = array();

		if($matchID > 0){
			// MatchTypeID auslesen
			$sql = "SELECT MatchTypeID
					FROM `Match`
					WHERE MatchID = ".(int)$matchID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$matchTypeID = $data['MatchTypeID'];
			// wenn 1vs1 dann immer screenshot verlangen
			switch($matchTypeID){
				case "8":
					// SCREENSHOT Handling
					$ret['screenshot'] = "screenshot";
					$ret['falscherWert'] = 0;
					$ret['oneVsOne'] = true;

					break;
				default:
					// SCREENSHOT Handling
					$falscherWert = $this->checkForStrangeWinLoseSubmission($matchID);
					$falscherWert = $falscherWert['result'];

					if($falscherWert > MatchDetails::screenshotGrenze){
						$ret['screenshot'] = "screenshot";
						$ret['falscherWert'] = $falscherWert;
					}
					else{
						$ret['screenshot'] = false;
						$ret['status'] = true;
					}

					// LEAVE Handling

					// leaver Daten auslesen
					$MatchDetailsLeaverVotes = new MatchDetailsLeaverVotes();
					$ret['leaverData'] = $MatchDetailsLeaverVotes->getLeaverData($matchID);

					// anzahl votes auslesen
					$countTMP = $MatchDetailsLeaverVotes->getLeaverVoteCount($matchID);
					$count = $countTMP['count'];

					if($count > 2){
						$ret['leaver'] = "leaver";
					}
					else{
						$ret['leaver'] = false;
					}
					$ret['oneVsOne'] = false;
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID wrong";
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMatchDetailsDataOfPlayer($matchID, $steamID=0, $select="*"){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchDetailsDataOfPlayer ";
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			if($matchID > 0){
				$sql = "SELECT ".$select."
						FROM `MatchDetails` md JOIN `Match` m ON md.MatchID = m.MatchID
						-- JOIN UserElo ue ON md.SteamID = ue.SteamID AND ue.MatchModeID = m.MatchModeID AND ue.MatchTypeID = m.MatchTypeID

						WHERE md.MatchID = ".(int) $matchID." AND md.SteamID = ".secureNumber($steamID)."
								";
				$ret['debug'] .= p($sql,1);
				$ret['data'] = $DB->select($sql);
				$ret['status'] = true;
			}
			else{
				$ret['status'] = "matchID = 0";
			}
		}
		else{
			$ret['status'] = "not logged in!";
		}
		$ret['debug'] .= "End getMatchDetailsDataOfPlayer ";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkForStrangeWinLoseSubmission($matchID, $data=null){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		if($matchID > 0){
			if($data == null){
				$sql = "SELECT SubmissionFor, TeamID
						FROM `MatchDetails`
						WHERE MatchID = ".(int)$matchID." AND Submitted = 1
								";
				$data = $DB->multiSelect($sql);
			}


			$falscherWert = $this->checkForStrangeWinLoseSubmissionData($data);
			$falscherWert = $falscherWert['falscherWert'];

			// bestimmen was größer ist

			$ret['result'] = $falscherWert;
			$ret['status'] = true;

		}
		else{
			$ret['status'] = "MatchID 0";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkForStrangeWinLoseSubmissionData($data){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if(is_array($data) && count($data) > 0){
			$countWin = array();
			$countLose = array();
			//p($data);
			foreach ($data as $k => $v){
				$teamID = $v['TeamID'];
				// WIN
				if($v['SubmissionFor'] == MatchDetails::winValue){
					$countWin[$teamID]++;
				}
				// LOSE
				else if($v['SubmissionFor'] == MatchDetails::loseValue){
					$countLose[$teamID]++;
				}
			}

			// wenn von beiden seiten auf win getippt wurde -> haben n problem
			$falscherWert = 0;
			if($countWin[1] > 0  && $countWin[2] > 0){

				$min = min($countWin[1],$countWin[2] );

				// �ber 2 haben dagegen gestimmt -> nun haben wir wirklich n problem
				$falscherWert = $min;
			}
			$ret['falscherWert'] = $falscherWert;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SQL wrong";
		}

		return $ret;

	}

	function checkForStrangeSubmissions1vs1($matchDetails){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkForStrangeSubmissions1vs1 <br>\n";

		if(is_array($matchDetails) && count($matchDetails) > 0){
			$countWins = 0;
			foreach ($matchDetails as $k => $v) {
				if($v['SubmissionFor'] == 1){
					$countWins++;
				}
			}
			if($countWins > 1){
				$ret['status'] = false;
			}
			else{
				$ret['status'] = true;
			}
		}
		else{
			$ret['status'] = "matchDetails empty";
		}
		$ret['debug'] .= "End checkForStrangeSubmissions1vs1 <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getTeamWon($matchID, $data=null){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		if($matchID > 0){
			$falscherWert = $this->checkForStrangeWinLoseSubmission($matchID, $data);
			$falscherWert = $falscherWert['result'];

			// wenn keine komischen submitts
			if($falscherWert <= MatchDetails::screenshotGrenze){
				if($data == null){
					$sql = "SELECT TeamID
							FROM `MatchDetails`
							WHERE MatchID = ".(int)$matchID." AND Submitted = 1 AND SubmissionFor = 1
									";
					$ret['debug'] .= p($sql,1);
					$data = $DB->multiSelect($sql);
				}

				$ret = $this->getTeamWonData($data);
			}
			else{
				$ret['teamWonID'] = 0;
				$ret['status'] = "komische submitts";
			}

		}
		else{
			$ret['status'] = "MatchID 0";
		}

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getTeamWon1vs1($matchDetails){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getTeamWon1vs1 <br>\n";
		if(is_array($matchDetails) && count($matchDetails) > 0){
			$teamWonID = -1;
			foreach ($matchDetails as $k => $v) {
				if($v['SubmissionFor'] == 1){
					$teamWonID = $v['TeamID'];
				}
			}
			$ret['teamWonID'] = $teamWonID;
		}
		else{
			$ret['status'] = "matchDetails empty";
		}
		$ret['debug'] .= "End getTeamWon1vs1 <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getTeamWonData($data){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();


		if(is_array($data) && count($data) > 0){
			$count = array();
			foreach($data as $k => $v){
				$count[$v['TeamID']]++;
			}
			if($count[1] == $count[2]){
				$teamWonID = -1;
			}
			else{
				arsort($count);
				$teamWonID = key($count);
			}

			$ret['teamWonID'] = $teamWonID;
			$ret['status'] = true;
		}
		else{
			$ret['teamWonID'] = -1;
			$ret['status'] = "SQL wrong";
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllPlayersInMatch($matchID, $select="*") {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllPlayersInMatch <br>\n";
		if($matchID > 0){
			$Match = new Match();
			$retM = $Match->getMatchData($matchID);
			$matchTypeID = $retM['data']['MatchTypeID'];

			$sql = "SELECT ".$select."
					FROM `MatchDetails` md JOIN `Match` m ON md.MatchID = m.MatchID
					LEFT JOIN `UserSkillBracket` usb ON md.SteamID = usb.SteamID AND usb.MatchTypeID = ".(int) $matchTypeID."
							LEFT JOIN `SkillBracketType` sb ON usb.SkillBracketTypeID = sb.SkillBracketTypeID
							WHERE m.MatchID = ".(int) $matchID."
									";
			$data = $DB->multiSelect($sql);

			// 			if(is_array($data) && count($data) > 0){
			// 				$UserPoints = new UserPoints();
			// 				foreach($data as $k =>$v){
			// 					$steamID = $v['SteamID'];
			// 					$retUP = $UserPoints->getGlobalPointsOfUser($steamID);
			// 					$points = $retUP['data'];
			// 					$data[$k]['Points'] = (int) $points;
			// 				}
			// 			}

			$ret['data'] = $data;
			$ret['debug'] .= $sql;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID 0";
		}
		$ret['debug'] .= "End getAllPlayersInMatch <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllPlayersInMatchSkillBracket($matchID, $select="*") {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllPlayersInMatchSkillBracket <br>\n";
		if($matchID > 0){
			$Match = new Match();
			$retM = $Match->getMatchData($matchID);
			$matchTypeID = $retM['data']['MatchTypeID'];
				
			$sql = "SELECT ".$select."
					FROM `MatchDetails` md JOIN `Match` m ON md.MatchID = m.MatchID
					LEFT JOIN `UserSkillBracket` usb ON md.SteamID = usb.SteamID AND usb.MatchTypeID = ".(int) $matchTypeID."
							LEFT JOIN `SkillBracketType` sbt ON usb.SkillBracketTypeID = sbt.SkillBracketTypeID
							WHERE m.MatchID = ".(int) $matchID."
									";
			$data = $DB->multiSelect($sql);

			// 			if(is_array($data) && count($data) > 0){
			// 				$UserPoints = new UserPoints();
			// 				foreach($data as $k =>$v){
			// 					$steamID = $v['SteamID'];
			// 					$retUP = $UserPoints->getGlobalPointsOfUser($steamID);
			// 					$points = $retUP['data'];
			// 					$data[$k]['Points'] = (int) $points;
			// 				}
			// 			}

			$ret['data'] = $data;
			$ret['debug'] .= $sql;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID 0";
		}
		$ret['debug'] .= "End getAllPlayersInMatchSkillBracket <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertEloChangesForMatchDetails($matchID, $teamWonID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$Match = new Match();
		$ret['debug'] .= "Start insertEloChangesForMatch <br>\n";
		if($matchID > 0){
			$data = $this->getAllPlayersInMatch($matchID);
			$data = $data['data'];
			$aveEloTeams = $this->getAverageEloOfTeams($matchID);
			$aveTeam1 = $aveEloTeams['ave_rank_team1'];
			$aveTeam2 = $aveEloTeams['ave_rank_team2'];
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

			if(is_array($data) && count($data) > 0){
				foreach($data as $k => $v){
					$rank = $v['Elo'];
					$teamID = $v['TeamID'];

					if($teamID == 1){
						$rankOther = $aveTeam2;
						$rank = $aveTeam1;
					}
					else{
						$rankOther = $aveTeam1;
						$rank = $aveTeam2;
					}

					$winLose = $Match->calculateWinLoseEloForPlayer($rank, $rankOther);
					$ret['debug'] .= "WINLOSEELO: ".p($winLose,1)."<br>\n";

					// wenn handicapped Team dann Elo anpassen
					if($handicappedTeamID == $teamID){

						$leaver = $Match->playerLeftTheMatch($v['SteamID'], $matchID);
						// wenn leaver dann doppelt bestrafen
						if($leaver['left']){
							$elo  = substr($winLose['LoseElo'], 1);
							$elo = 2*$elo;
							$winLose['LoseElo'] = "-".$elo;
						}
						// wenn kein Leaver, dann lose auf 0 reduzieren
						else{
							$winLose['LoseElo'] = "-0";
						}
					}

					// WIN -> +Elo
					if($teamWonID == $teamID){
						$eloChange = $winLose['WinElo'];
					}
					// LOSE -> -Elo
					else{
						$eloChange = $winLose['LoseElo'];
					}

					$sql = "UPDATE `MatchDetails`
							SET EloChange = '".$eloChange."'
									WHERE MatchID = ".(int) $matchID." AND SteamID = ".secureNumber($v['SteamID'])."
											";
					$ret['debug'] .= $sql."<br>\n";
					$DB->update($sql);
				}
			}

		}
		else{
			$ret['status'] = "MatchID 0";
		}
		$ret['debug'] .= "End insertEloChangesForMatch <br>\n";
		return $ret;
	}



	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAverageEloOfTeams($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($matchID > 0){
			$sql = "SELECT ue.Elo, md.TeamID
					FROM `MatchDetails` md JOIN `Match` m ON md.MatchID = m.MatchID
					JOIN UserElo ue ON md.SteamID = ue.SteamID AND ue.MatchModeID = m.MatchModeID AND ue.MatchTypeID = m.MatchTypeID
					WHERE m.MatchID = ".(int) $matchID."
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= $sql."<br>\n";
			//$ret['debug'] .= p($data,1)."<br>\n";
			if(is_array($data) && count($data) > 0){
				$team1EloSum = 0;
				$team2EloSum = 0;
				foreach($data as $k => $v){
					if($v['TeamID'] == 1){
						$team1EloSum += (int) $v['Elo'];
					}
					if($v['TeamID'] == 2){
						$team2EloSum += (int) $v['Elo'];
					}
				}
				$ret['debug'] .= "T1: ".$team1EloSum." T2:".$team2EloSum."<br>\n";
				$aveTeam1 = round(($team1EloSum/5),0);
				$aveTeam2 = round(($team2EloSum/5),0);

				$ret['ave_rank_team1'] = $aveTeam1;
				$ret['ave_rank_team2'] = $aveTeam2;

				$ret['status'] = true;

			}
			else{
				$ret['status'] = "SQL wrong";
			}
		}
		else{
			$ret['status'] = "MatchID 0";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLastMatchDetailsOfPlayer($steamID, $count="*", $orderBy="DESC"){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID > 0){
			$limit = "";
			if($count != "*"){
				$limit = " LIMIT ".(int) $count;
			}
			$Match = new Match();
			$sql = "SELECT md.MatchID, m.TeamWonID, md.EloChange, m.MatchTypeID, mm.Name as MatchMode, mm.Shortcut as MatchModeShortcut, md.TeamID, m.TimestampCreated, IF(COUNT(mdlv.VoteForPlayer) >= ".$Match->leaverGrenze." OR md.EloChange = '-".LEAVEMATCHELOPUNISH."',1,0) as Leaver, m.Canceled
					FROM `Match` m JOIN `MatchDetails` md ON m.MatchID = md.MatchID
					LEFT JOIN MatchDetailsLeaverVotes as mdlv ON mdlv.VoteForPlayer = md.SteamID AND mdlv.MatchID = md.MatchID
					LEFT JOIN MatchDetailsCancelMatchVotes as mdcmv ON mdcmv.VoteForPlayer = md.SteamID AND mdcmv.MatchID = md.MatchID
					JOIN MatchMode mm ON m.MatchModeID = mm.MatchModeID
					WHERE md.SteamID = ".secureNumber($steamID)." AND (m.TeamWonID != -1 OR m.Canceled = 1)
							GROUP BY md.MatchID
							ORDER BY m.TimestampClosed ".$orderBy."
									".$limit."
											";
			$data = $DB->multiSelect($sql);
			//p($sql);
			$ret['debug'] .= $sql;
			$ret['debug'] .= p($data,1);

			if(is_array($data) && count($data) > 0){
				$ret['data'] = $data;
			}
			else{
				$ret['status'] = "SQL wrong";
			}

			$ret['status'] = true;

		}
		else{
			$ret['status'] = "steamID 0";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLastMatchDetailsOfPlayer2($steamID, $count="*", $orderBy="DESC"){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID > 0){
			$limit = "";
			if($count != "*"){
				$limit = " LIMIT ".(int) $count;
			}
			$Match = new Match();
				
			$sqlLeaverVotes = "SELECT Count(VoteForPlayer) as Count
					FROM `MatchDetailsLeaverVotes`
					WHERE MatchID = md.MatchID AND VoteForPlayer = ".secureNumber($steamID)."
							GROUP BY VoteForPlayer
							";
			$sqlCancelMatchVotes = "SELECT Count(VoteForPlayer) as Count
					FROM `MatchDetailsCancelMatchVotes`
					WHERE MatchID = md.MatchID AND VoteForPlayer = ".secureNumber($steamID)."
							GROUP BY VoteForPlayer
							";
			$sqlPoints = "SELECT SUM(PointsChange)
					FROM `UserPoints`
					WHERE SteamID = ".secureNumber($steamID)." AND MatchID = md.MatchID
							";
			$sql = "SELECT md.MatchID, m.TeamWonID, m.MatchTypeID, mm.Name as MatchMode, m.Canceled,
					mm.Shortcut as MatchModeShortcut, md.TeamID, m.TimestampClosed
					,
					IF((".$sqlLeaverVotes.") >= ".$Match->leaverGrenze."
							OR (".$sqlCancelMatchVotes.") >= ".$Match->leaverGrenze.",1,0) as Leaver,
									(".$sqlPoints.") as PointsChange
											FROM `Match` m JOIN `MatchDetails` md ON m.MatchID = md.MatchID
											JOIN MatchMode mm ON m.MatchModeID = mm.MatchModeID
											WHERE md.SteamID = ".secureNumber($steamID)." AND (m.TeamWonID != -1 OR m.Canceled = 1)
													GROUP BY md.MatchID
													ORDER BY m.TimestampCreated ".$orderBy."
															".$limit."
																	";
			$data = $DB->multiSelect($sql);
			//p($sql);
			$ret['debug'] .= $sql;
			$ret['debug'] .= p($data,1);

			if(is_array($data) && count($data) > 0){
				$ret['data'] = $data;
			}
			else{
				$ret['status'] = "SQL wrong";
			}

			$ret['status'] = true;

		}
		else{
			$ret['status'] = "steamID 0";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGeneralWinRateTrendData($steamID, $count="10", $data = array()){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID > 0){
			if(count($data) == 0){
				$data = $this->getLastMatchDetailsOfPlayer($steamID, "*", "ASC");
			}
			$ret['debug'] .= p($data['data'],1);



			if(is_array($data['data']) && count($data['data']) > 0){
				foreach($data['data'] as $k => $v){
					$ret['debug'] .= $v['TeamWonID']." == ".$v['TeamID']." \n";
					if($v['Leaver'] == 0 && $v['Canceled'] == 0){
						if($v['TeamWonID'] == $v['TeamID']){
							$winLoseArray[] = "1";
						}
						else{
							$winLoseArray[] = "0";
						}
					}
				}
			}
			//$ret['debug'] .= p($winLoseArray,1);
			if(is_array($winLoseArray) && count($winLoseArray) > 0){
				$i = 1;
				$summe = 0;
				foreach($winLoseArray as $k => $v){
					$summe += $v;
					$trendArray[($i-1)] = round($summe/$i*100,2);
					$i++;
					$keyArray[] = ($data['data'][($i-1)]['MatchID']);
						
				}

				// anfang
				if($count != "*"){
					(count($trendArray) > (int)$count ? $anfang=(count($trendArray)-(int)$count): $anfang=0);
					$trendArray = array_slice($trendArray,$anfang,$count);
					$keyArray = array_slice($keyArray,$anfang,$count);
				}
			}
			$ret['debug'] .= p($trendArray,1);



			$ret['debug'] .= p($trendArray,1);
			$ret['data'] = $trendArray;
			$ret['xAxis'] = $keyArray;
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
	function getOpenSubmitsOfPlayer($steamID, $select="MatchID"){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID > 0){
			$sql="SELECT md.MatchID, COUNT(mdcmv.SteamID) as CountCancelSubmitts
					FROM `MatchDetails` md JOIN `Match` m ON m.MatchID = md.MatchID
					LEFT JOIN MatchDetailsCancelMatchVotes mdcmv ON mdcmv.SteamID = md.SteamID AND mdcmv.MatchID = m.MatchID
					WHERE md.SteamID = ".secureNumber($steamID)." AND Submitted = 0 AND m.Canceled = 0
							GROUP BY md.MatchID
							";
			$data = $DB->multiSelect($sql);
			//p($sql);
			$ret['debug'] .= $sql;
			$ret['data'] = $data;
			//p($data);
			if(count($data)>0){
				if(is_array($data) && count($data) > 0 && $data[0]['MatchID'] != ""){
					$ret['openSubmits'] = true;
					$check = true;
					foreach($data as $k =>$v){
						if($v['CountCancelSubmitts'] == 0){
							$ret['openSubmits'] = true;
							$check = false;
							break;
						}
					}
					if($check){
						$ret['openSubmits'] = false;
					}
				}

			}
			else {
				$ret['openSubmits'] = false;
			}

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
	function getEloHistoryData($matchModeID, $matchTypeID, $count="*", $steamID = 0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$matchModeID = (int) $matchModeID;
		$matchTypeID = (int) $matchTypeID;
		$ret['debug'] .= $steamID;
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0 && $matchModeID > 0 && $matchTypeID > 0){

			$sql="SELECT Elo, EloChange, mt.Name as MatchType, mm.Name as MatchMode
					FROM `MatchDetails` md JOIN `Match` m ON md.MatchID = m.MatchID
					JOIN MatchMode mm ON mm.MatchModeID = m.MatchModeID
					JOIN MatchType mt ON mt.MatchTypeID = m.MatchTypeID
					WHERE SteamID = ".secureNumber($steamID)." AND m.TeamWonID != -1 AND m.MatchTypeID = ".(int)$matchTypeID." AND m.MatchModeID = ".(int)$matchModeID."
							ORDER BY m.MatchID
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= $sql;

			// für Highchart aufbereiten
			if(is_array($data) && count($data) > 0){
				$i = 1;
				foreach ($data as $k => $v) {
					$eloChange  = substr($v['EloChange'], 1);

					if(strpos($v['EloChange'], "+") === 0){
						$elo = $v['Elo'] + $eloChange;
					}
					else{
						$elo = $v['Elo'] - $eloChange;
					}

					$retData[] = $elo;
					$retKeys[] = $i;

					$matchMode = $v['MatchMode'];
					$matchType = $v['MatchType'];

					$i++;
				}
				if($count != "*"){
					(count($retData) > (int)$count ? $anfang=(count($retData)-(int)$count): $anfang=0);
					$retData = array_slice($retData,$anfang,(int)$count);
					$retKeys = array_slice($retKeys,$anfang,(int)$count);
				}
			}

			// anfang
			$ret['debug'] .= "#########COUNT: ".$count." \n<br>";


			$ret['diamondBorder'] = DIAMONDELO;
			$ret['goldBorder'] = GOLDELO;
			$ret['silverBorder'] = SILVERELO;

				
			$ret['data'] = $retData;
			$ret['xAxis'] = $retKeys;
			$ret['matchType'] = $matchType;
			$ret['matchMode'] = $matchMode;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID 0 oder matchmodeID = 0";
		}
		return $ret;

	}

}
?>