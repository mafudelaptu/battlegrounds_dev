<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Event{

	const startSubmissionBorder = 3600;
	const endSubmissionBorder = 600;
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertNewEvent($eventTypeID, $regionID=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertNewEvent <br>\n";

		if($eventTypeID > 0){

			$EventType = new EventType();
			$retData = $EventType->getEventTypeData($eventTypeID, $regionID);
			$data = $retData['data'];
			$ret['debug'] .= p($data,1);
			// next start timestamp berechnen
			$retTimeData = $EventType->calculateNextStartTimestamp($data['StartTime'], $data['StartDay'], $data['RegionID']);
			$startTimestamp = $retTimeData['data'];

			// Start und End Submission zum Event berechnen
			$startSub = $startTimestamp-Event::startSubmissionBorder;
			$endSub = $startTimestamp-Event::endSubmissionBorder;
			$ret['debug'] .= p($startTimestamp." - ".$startSub." - ".$endSub,1);
			$insertArray = array();
			$insertArray['EventTypeID'] = (int) $eventTypeID;
			$insertArray['StartTimestamp'] = (int) $startTimestamp;
			$insertArray['EndTimestamp'] = (int) 0;
			$insertArray['StartSubmissionTimestamp'] = (int) $startSub;
			$insertArray['EndSubmissionTimestamp'] = (int) $endSub;
			$insertArray['RegionID'] = (int) $data['RegionID'];

			$retIns = $DB->insert("Events", $insertArray);

			$ret['status'] = $retIns;
		}
		else{
			$ret['status'] = "eventTypeID = 0";
		}

		$ret['debug'] .= "End insertNewEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getNextEvent(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getNextEvent <br>\n";

		$sql = "SELECT e.*, et.*,
				mm.Name as MatchModeName, mm.Shortcut as MMShortcut,
				r.Name as RegionName, r.Shortcut as RShortcut,
				tt.Name as TTName, tt.Shortcut as TTShortcut,
				er.PointBorder as PointReq, lt.Name as LeagueReq,
				pt.Name as PrizeName, pt.Count as PrizeCount, pt.MaxCost as PrizeCost, pt.Type as PrizeType
				FROM `Events` e
				JOIN `EventTypes` et ON e.EventTypeID = et.EventTypeID
				JOIN MatchMode mm ON mm.MatchModeID = et.MatchModeID
				JOIN Region r ON r.RegionID = et.RegionID
				JOIN TournamentTypes tt ON tt.TournamentTypeID = et.TournamentTypeID
				JOIN EventRequirements er ON er.EventRequirementID = et.EventRequirementID
				LEFT JOIN LeagueType lt ON lt.LeagueTypeID = er.LeagueBorder
				JOIN PrizeType pt ON pt.PrizeTypeID = et.PrizeTypeID
				WHERE e.EndTimestamp = 0 AND e.RegionID = ".(int) $_COOKIE['region']."
						ORDER BY StartTimestamp ASC
						LIMIT 1
						";
		$ret['debug'] .= p($sql,1);
		$data = $DB->select($sql);

		$ret['data']  =$data;
		$ret['status'] = true;

		$ret['debug'] .= "End getNextEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function eventReachedMinSubmissions($eventID, $eventTypeID, $regionID=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start eventReachedMinSubmissions <br>\n";
		if($eventID > 0){
			$EventType = new EventType();
			$EventSubmissions = new EventSubmissions();

			$retData = $EventType->getEventTypeData($eventTypeID, $regionID);
			$ret['debug'] .= p($retData,1);

			$minSubmissions = $retData['data']['MinSubmissions'];

			$retCount = $EventSubmissions->getSubmissionCountOfEvent($eventID);
			$ret['debug'] .= p($retCount,1);
			$count = $retCount['data'];

			if($count >= $minSubmissions){
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End eventReachedMinSubmissions <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function cancelEvent($eventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start cancelEvent <br>\n";
		if($eventID > 0){
			$sql = "UPDATE `Events`
					SET Canceled = 1, EndTimestamp = ".time()."
							WHERE EventID = ".(int) $eventID."
									";
			$ret['debug'] .= p($sql,1);
			$data = $DB->update($sql);
			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End cancelEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function isEventActive(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start isEventActive <br>\n";

		$data = $this->getNextEvent();
		$ret['debug'] .= p($data,1);
		$data = $data['data'];

		// wenn start datum nach jetzt und nicht gecanceled dann activ
		if($data['StartTimestamp'] < time() && $data['Canceled'] == 0){
			$ret['status'] = true;
		}
		else{
			$ret['status'] = false;
		}


		$ret['debug'] .= "End isEventActive <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getEventData($eventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getEventData <br>\n";

		$sql = "SELECT e.*, et.*,
				mm.Name as MatchModeName, mm.Shortcut as MMShortcut,
				r.Name as RegionName, r.Shortcut as RShortcut,
				tt.Name as TTName, tt.Shortcut as TTShortcut,
				er.PointBorder as PointReq, lt.Name as LeagueReq, er.LeagueBorder as LeagueReqID,
				pt.Name as PrizeName, pt.Count as PrizeCount, pt.MaxCost as PrizeCost, pt.Type as PrizeType
				FROM `Events` e JOIN `EventTypes` et ON e.EventTypeID = et.EventTypeID
				JOIN MatchMode mm ON mm.MatchModeID = et.MatchModeID
				JOIN Region r ON r.RegionID = et.RegionID
				JOIN TournamentTypes tt ON tt.TournamentTypeID = et.TournamentTypeID
				JOIN EventRequirements er ON er.EventRequirementID = et.EventRequirementID
				LEFT JOIN LeagueType lt ON lt.LeagueTypeID = er.LeagueBorder
				JOIN PrizeType pt ON pt.PrizeTypeID = et.PrizeTypeID
				WHERE EventID = ".(int) $eventID."
						LIMIT 1
						";
		$ret['debug'] .= p($sql,1);
		$data = $DB->select($sql);
		$ret['data'] = $data;
		$ret['status'] = true;
		$ret['debug'] .= "End getEventData <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPlayerStatus($eventID, $createdEventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPlayerStatus <br>\n";
		if($eventID > 0 && $createdEventID){
			$EventTeams = new EventTeams();
			$EventMatches = new EventMatches();
			for($i=1; $i<=2; $i++){
				$retTeam = $EventTeams->getTeamOfUser($eventID, $createdEventID, $i);
				$playerTeam = $retTeam['data'];
					
				$retMatches = $EventMatches->getMatchData($eventID, $createdEventID, $playerTeam['EventTeamID']);
				$ret['debug'] .= p($retMatches,1);
				$matchesData = $retMatches['data'];
					
				$matchID = $matchesData['MatchID'];

				//opponent Team bestimmen
				if($playerTeam['EventTeamID'] == $matchesData['Team1']){
					$opponentTeam = $matchesData['Team2'];
				}
				else{
					$opponentTeam = $matchesData['Team1'];
				}

				$MatchDetails = new MatchDetails();
				$retMdData = $MatchDetails->getMatchDetailsDataOfPlayer($matchID);
				$mdData = $retMdData['data'];
				$data['round'.$i]['mdData'] = $mdData;
				// Match noch nciht gespielt
				if($mdData['TeamWonID'] == "-1" AND $mdData['Canceled'] == "0" AND $mdData['Submitted'] == "0"){
					$data['round'.$i]['status'] = "You have to play Match: <a href='eventMatch.php?matchID=".$matchID."'>".$matchID."</a>";
				}
				elseif($mdData['TeamWonID'] == "-1" AND $mdData['Canceled'] == "0" AND $mdData['Submitted'] == "1"){
					$data['round'.$i]['status'] = "Waiting for other Players submit a result in Match: <a href='eventMatch.php?matchID=".$matchID."'>".$matchID."</a>";
				}
				elseif($mdData['TeamWonID'] == "-1" AND $mdData['Canceled'] == "1" AND $mdData['Submitted'] == "1"){
					$data['round'.$i]['status'] = "Match was canceled by Players";
				}
				elseif($mdData['TeamWonID'] == $mdData['TeamID'] AND $mdData['Canceled'] == "0" AND $mdData['Submitted'] == "1"){
					$data['round'.$i]['status'] = "You won Match: <a href='eventMatch.php?matchID=".$matchID."'>".$matchID."</a>";
				}
				elseif($mdData['TeamWonID'] != $mdData['TeamID'] AND $mdData['Canceled'] == "0" AND $mdData['Submitted'] == "1"){
					$data['round'.$i]['status'] = "You lost Match: <a href='eventMatch.php?matchID=".$matchID."'>".$matchID."</a>";
				}
				$data['round'.$i]['playerTeam'] = $playerTeam;
				$data['round'.$i]['matchesData'] = $matchesData;
				$data['round'.$i]['opponentTeam'] = $opponentTeam;
			}

			// Status generieren abh�ngig von den gesammelten daten
			//


			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID = 0";
		}

		$ret['debug'] .= "End getPlayerStatus <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function playerLostEvent($eventID, $createdEventID, $forNotification=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start playerLostEvent <br>\n";
		if($eventID > 0 && $createdEventID > 0){
			if($forNotification){
				$CreatedEvents = new CreatedEvents();
				$retEvent = $CreatedEvents->getCreatedEventData($createdEventID);
				$ret['debug'] .= p($retEvent,1);
				$eventData = $retEvent['data'];
				if($eventData != false){
					if($eventData['EndTimestamp'] == "0" AND $eventData['Canceled'] == 0){
						$EventTeams = new EventTeams();
						$retTeamUser = $EventTeams->getTeamOfUser($eventID, $createdEventID, 1);
						$ret['debug'] .= p($retTeamUser,1);
						$playerTeamRound1 = $retTeamUser['data']['EventTeamID'];
							
							
						// auslesen wie die ergebnisse der matches is
						$EventMatches = new EventMatches();
						$retMatchDataR1 = $EventMatches->getMatchData($eventID, $createdEventID, $playerTeamRound1);
						$ret['debug'] .= p($retMatchDataR1,1);
						// erste Runde
						$teamWonR1 = $retMatchDataR1['data']['TeamWonID'];
						if($teamWonR1 != 0){
							if($teamWonR1 == $playerTeamRound1){
									
								// erste RUnde gewonnen -> nun gucken ob auhc 2. gewonnen
								$retTeamUser2 = $EventTeams->getTeamOfUser($eventID, $createdEventID, 2);
								$playerTeamRound2 = $retTeamUser2['data']['EventTeamID'];
								$ret['debug'] .= p($retTeamUser2,1);
								$retMatchDataR2 = $EventMatches->getMatchData($eventID, $createdEventID, $playerTeamRound2);
								$teamWonR2 = $retMatchDataR2['data']['TeamWonID'];
								$ret['debug'] .= p($retMatchDataR2,1);
								if($teamWonR2 != 0){
									if($teamWonR2 == $retMatchDataR2){
										$ret['status'] = false;
									}
									else{
										$ret['status'] = true;
									}
								}
								else{
									$ret['status'] = false;
								}
							}
							else{
								$ret['status'] = true;
							}
						}
						else{
							$ret['status'] = false;
						}
							
					}
					else{
						$ret['status'] = false;
					}
				}
				else{
					$ret['status'] = false;
				}
			}
			else{
				$EventTeams = new EventTeams();
				$retTeamUser = $EventTeams->getTeamOfUser($eventID, $createdEventID, 1);
				$playerTeamRound1 = $retTeamUser['data']['EventTeamID'];
					
					
				// auslesen wie die ergebnisse der matches is
				$EventMatches = new EventMatches();
				$retMatchDataR1 = $EventMatches->getMatchData($eventID, $createdEventID, $playerTeamRound1);
				// erste Runde
				$teamWonR1 = $retMatchDataR1['data']['TeamWonID'];
				if($teamWonR1 != 0){
					if($teamWonR1 == $playerTeamRound1){

						// erste RUnde gewonnen -> nun gucken ob auhc 2. gewonnen
						$retTeamUser2 = $EventTeams->getTeamOfUser($eventID, $createdEventID, 2);
						$playerTeamRound2 = $retTeamUser2['data']['EventTeamID'];
						$retMatchDataR2 = $EventMatches->getMatchData($eventID, $createdEventID, $playerTeamRound2);
						$teamWonR2 = $retMatchDataR2['data']['TeamWonID'];
						if($teamWonR2 != 0){
							if($teamWonR2 == $retMatchDataR2){
								$ret['status'] = false;
							}
							else{
								$ret['status'] = true;
							}
						}
						else{
							$ret['status'] = false;
						}
					}
					else{
						$ret['status'] = true;
					}
				}
				else{
					$ret['status'] = false;
				}

			}



		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | teamID = 0";
		}
		$ret['debug'] .= "End playerLostEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function closeEvent($eventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start closeEvent <br>\n";

		if($eventID > 0){
			$sql = "UPDATE `Events`
					SET EndTimestamp = ".time()."
							WHERE EventID = ".(int) $eventID."
									";
			$data = $DB->update($sql);

			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "eventID = 0";
		}



		$ret['debug'] .= "End closeEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGlobalEventStats(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getGlobalEventStats <br>\n";

		// Events played
		$sql = "SELECT *
				FROM `Events`
				WHERE EndTimestamp != 0 AND Canceled = 0
				";
		$countPlayedEvents = $DB->countRows($sql);
		$ret['debug'] .= p($sql,1);

		// Players signed-in count
		$EventSubmissions = new EventSubmissions();
		$playerSubCountRet = $EventSubmissions->getGlobalEventSubmissionCount();
		$playerSubCount = $playerSubCountRet['data'];

		// Players signed-in count
		$EventSubmissions = new EventSubmissions();
		$playerSubPlayedCountRet = $EventSubmissions->getGlobalEventPlayersPlayedCount();
		$playerSubPlayedCount = $playerSubPlayedCountRet['data'];

		$data['EventsPlayed'] = $countPlayedEvents;
		$data['PlayersSignedIn'] = $playerSubCount;
		$data['PlayersPlayed'] = $playerSubPlayedCount;

		$ret['data'] = $data;
		$ret['status'] = true;
		$ret['debug'] .= "End getGlobalEventStats <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLastEvents($limit="", $winners=false, $eventSubs=true, $createdEvents=true){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLastEvents <br>\n";

		if($limit != ""){
			$limit = "LIMIT ".(int)$limit;
		}
		else{
			$limit = "";
		}

		$sql = "SELECT e.*, et.*,
				mm.Name as MatchModeName, mm.Shortcut as MMShortcut,
				r.Name as RegionName, r.Shortcut as RShortcut,
				tt.Name as TTName, tt.Shortcut as TTShortcut
				FROM `Events` e
				JOIN `EventTypes` et ON e.EventTypeID = et.EventTypeID
				JOIN MatchMode mm ON mm.MatchModeID = et.MatchModeID
				JOIN Region r ON r.RegionID = et.RegionID
				JOIN TournamentTypes tt ON tt.TournamentTypeID = et.TournamentTypeID
				WHERE e.EndTimestamp != 0 AND e.Canceled = 0 AND e.RegionID = ".(int) $_COOKIE['region']."
						ORDER BY EndTimestamp DESC
						".$limit."
								";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);


		// gewinner der Events auslesen
		if($winners){
			$EventWinner = new EventWinner();
			if(is_array($data) && count($data) > 0){
				foreach ($data as $k => $v) {
					$eventID = $v['EventID'];
					$tmpData = $EventWinner->getWinnerOfEvent($eventID);
					$ret['debug'] .= p($tmpData,1);
					$data[$k]['winner'] = $tmpData['data'];
				}
			}
		}

		// Createdevent Data
		if($createdEvents){
			if(is_array($data) && count($data) > 0){
				$CreatedEvents = new CreatedEvents();
				foreach ($data as $k => $v) {
					$eventID = $v['EventID'];
					$createdEventStuff = $CreatedEvents->getAllCreatedEventsOfEvent($eventID);
					$ret['debug'] .= p($createdEventStuff,1);
					$data[$k]['createdEventsData'] = $createdEventStuff['data'];
				}
			}
		}

		// EventSubs hinzufügen
		if($eventSubs){
			if(is_array($data) && count($data) > 0){
				$EventSubmissions = new EventSubmissions();
				foreach ($data as $k => $v) {
					$eventID = $v['EventID'];
					$matchModeID = $v['MatchModeID'];
					$matchTypeID = $v['MatchTypeID'];
					$stuff = $EventSubmissions->getPlayersSubmissionOfEvent($eventID, $matchModeID, $matchTypeID);
					$data[$k]['eventSubsData'] = $stuff['data'];
					$data[$k]['eventSubsCount'] = $stuff['count'];
				}
			}
		}



		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getLastEvents <br>\n";
		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkRequirementsOfPlayer($steamID, $eventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkRequirementsOfPlayer <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0 && $eventID > 0){
			$allowed = array();
			$Event = new Event();
			$retEvent = $Event->getEventData($eventID);
			$eventData = $retEvent['data'];
			// Points checken
			$pointsReq = $eventData['PointsReq'];
			if($pointsReq > 0){
				$UserPoints = new UserPoints();
				$retU = $UserPoints->getGlobalPointsOfUser($steamID);
				$points = $retU['data'];

				if($points >= $pointsReq){
					$allowed[] = true;
				}
				else{
					$allowed[] = false;
				}
			}
				
			// League checken
			$leagueReq = $eventData['LeagueReqID'];
			if($leagueReq > 0){
				$UserLeague = new UserLeague();
				$retUL = $UserLeague->getLeagueOfUser($steamID);
				$leagueData = $retUL['data'];
					
				$leagueTypeID = $leagueData['LeagueTypeID'];
				if($leagueTypeID >= $leagueReq){
					$allowed[] = true;
				}
				else{
					$allowed[] = false;
				}
			}
			$ret['debug'] .= p($allowed,1);
			// kontrollieren ob im ganzen array nur true's sind
			if(in_array(false, $allowed)){
				$ret['status'] = false;
			}
			else{
				$ret['status'] = true;
			}
		}
		else{
			$ret['status'] = "steamID = 0 || eventID = 0";
		}

		$ret['debug'] .= "End checkRequirementsOfPlayer <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function isPlayerInEvent($eventID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start isPlayerInEvent <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		
		if($eventID > 0){
			$sql = "SELECT *
							FROM `EventTeams` et
							WHERE SteamID = ".secureNumber($steamID)." AND EventID = ".(int) $eventID."
			";
			$count = $DB->countRows($sql); 
			$ret['debug'] .= p($sql,1);
			
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
		$ret['debug'] .= "End isPlayerInEvent <br>\n";
		return $ret;
	}

}

?>