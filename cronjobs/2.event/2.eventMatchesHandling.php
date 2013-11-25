<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
p("Start EVENT MATCHES Handling");
$DB = new DB();
$con = $DB->conDB();
// schauen ob eventMatches beendet wurden
$sql = "SELECT m.*, em.EventID, em.CreatedEventID
		FROM `Match` m JOIN EventMatches em ON m.MatchID = em.MatchID
		JOIN Events e ON e.EventID = em.EventID
		WHERE ((m.TeamWonID != -1 AND m.TimestampClosed != 0) OR m.Canceled = 1 OR m.ManuallyCheck = 1) AND e.EndTimestamp = 0
		";
$data = $DB->multiSelect($sql);

if(is_array($data) && count($data)>0){
	foreach($data as $k => $v){
		// unterscheidung was tun bei normalen eintrag cancel und manually check
		$teamWonID = $v['TeamWonID'];
		$canceled = $v['Canceled'];
		$mCheck = $v['ManuallyCheck'];
		$eventID = $v['EventID'];
		$createdEventID = $v['CreatedEventID'];
		$matchID = $v['MatchID'];

		p("teamWonID:".$teamWonID." C:".$canceled." mC:".$mCheck." EID:".$eventID." cEID:".$createdEventID." matchID:".$matchID);
		if($teamWonID > -1){
			$status = "Win/Lose";
		}
		if($canceled == 1){
			$status = "Canceled";
		}
		if($mCheck == 1){
			$status = "MCheck";
		}
		p($status);
		switch($status){
			case "Win/Lose":
				// bei normalen verlauf -> gewonnenes Team bestimmen, eintragen und in den Pool weiterreichen
				// erstma wer gegeneinander antrat bestimmen
				$EventMatches = new EventMatches();
				$retMatches = $EventMatches->getMatchDataByMatchID($eventID, $createdEventID, $matchID);
				p($retMatches);
				$matchData = $retMatches['data'];
				// unterscheidung wer gewonne hat
					
				$eventTeamWonID = $matchData['Team'.$teamWonID];
					
				$retUpdate = $EventMatches->updateEventMatchesTeamWonID($eventID, $createdEventID, $matchID, $eventTeamWonID);
					
				// typen die gewonnen haben auslesen und wieder in pool tun
				$EventTeams = new EventTeams();
				$retTeam = $EventTeams->getTeam($eventID, $createdEventID, $eventTeamWonID);
				p($retTeam);
				$teamData = $retTeam['data'];
					
				$EventPool = new EventPool();
				$retInsPool = $EventPool->insertWonTeamIntoPool($eventID, $createdEventID, 2, $teamData);
				p($retInsPool);
					
				break;
			case "Canceled":
				$EventMatches = new EventMatches();
				// keinen in Pool lassen und eventMatch auf canceled setzen
				$retUpdate = $EventMatches->updateEventMatchesTeamWonID($eventID, $createdEventID, $matchID, -1);
				p($retUpdate);
				break;
			case "MCheck":
				// erst kontrollieren und dann per hand ergebniss eintragen  dann durch Cronjob automatisch einrtagen lassen
				$EventMatches = new EventMatches();
				$retUpdate = $EventMatches->updateEventMatchesTeamWonID($eventID, $createdEventID, $matchID, -2);
				p($retUpdate);
				break;
		}
	}
}


/**
 * Wenn genug Player in Pool für runde 2 -> dann Teams 5 und 6 generieren
 * vorher noch prüfen ob match nciht von beiden gecanceled -> dann created event cancelen
 * bei nur einem Match canceled hat das team das Event gewonnen welches das eine Match gewonnen hat
 */
p("START Event Matches - Round 2 handling");

$sql = "SELECT DISTINCT em.EventID, em.CreatedEventID, em.TeamWonID
		FROM `EventMatches` em JOIN Events e ON em.EventID = e.EventID
		JOIN `CreatedEvents` ce ON ce.CreatedEventID = em.CreatedEventID
		WHERE e.EndTimestamp = 0 AND e.Canceled = 0 AND ce.Canceled = 0 AND ce.EndTimestamp = 0
		";

$data = $DB->multiSelect($sql);
p($data);
if(is_array($data) && count($data)>0){
	foreach($data as $k => $v){
		$eventID = $v['EventID'];
		$createdEventID = $v['CreatedEventID'];

		// matches auslesen aus event
		$EventMatches = new EventMatches();
		$retMatchesStatus = $EventMatches->getEventMatchesStatus($eventID, $createdEventID);
		p($retMatchesStatus);
		$matchesStatus = $retMatchesStatus['data'];

		// für Round 1 gucken ob nicht beide gecanceled
		if(is_array($matchesStatus[1]) && count($matchesStatus[1])>0){
			$canceledCount = 0;
			$matchesEndedCount = 0;
			foreach($matchesStatus[1] as $k => $v){
				if($v['TeamWonID'] != "0"){
					$matchesEndedCount++;
				}
				if($v['TeamWonID'] == "-1"){
					$canceledCount++;
				}

			}

			if($canceledCount == 2 AND $matchesEndedCount == 2){
				// wenn beide Mathces gecanceled wurden -> dann CreatedEvent cancelen
				$CreatedEvents = new CreatedEvents();
				$retUpdate = $CreatedEvents->cancelCreatedEvent($createdEventID);
			}
			elseif ($canceledCount == 1 AND $matchesEndedCount == 2){
				// wenn nur ein Match gecanceled wurde -> team raussuchen welches gewonnen hat und als gewinner erklären
				foreach($matchesStatus[1] as $k => $v){
					if($v['TeamWonID'] > 0){
						$teamWonID = $v['TeamWonID'];
						break;
					}
				}
				// created Match beenden und gewinner festlegen
				$CreatedEvents = new CreatedEvents();
				$retWinner = $CreatedEvents->setWinnerForCreatedEvent($createdEventID, $teamWonID);
				p($retWinner);
				// use die gewonnen haben in UserWonEvents eintragen
				$EventTeams = new EventTeams();
				$retTeamData = $EventTeams->getTeam($eventID, $createdEventID, $teamWonID);
				p($retTeamData);
				$teamData = $retTeamData['data'];
				if(is_array($teamData) && count($teamData)>0){
					$UserWonEvents = new UserWonEvents();
					// zuerst Event daten auslesen
					$Event = new Event();
					$retEventData = $Event->getEventData($eventID);
					p($retEventData);
					$matchModeID = $retEventData['data']['MatchModeID'];
					$matchTypeID = $retEventData['data']['MatchTypeID'];

					foreach($teamData as $k => $v){
						$steamID = $v['SteamID'];
						$retIns = $UserWonEvents->insertUserWonEvent($steamID, $eventID, $createdEventID);
						p($retIns);

						// checken ob überhaupt der Preis ein Point-Boost ist
						if($retEventData['data']['PrizeType'] == "Point-Boost"){
							// belohnung +50 Points hinzufügen
							$UserPoints = new UserPoints();
							$retUP = $UserPoints->insertEventRewardPointBonus($steamID, $eventID, EVENTBONUS, $matchTypeID, $matchModeID);
						}

						// 						$UserElo = new UserElo();
						// 						$UserStats = new UserStats();
							
						// 						$retUserEloUp = $UserElo->changeUserElo($matchModeID, $matchTypeID, $steamID, 50, true);
						// 						p($retUserEloUp);
						// 						$retUserStatsUp = $UserStats->changeUserRank($steamID, 50, true);
					}
				}
			}
			elseif ($canceledCount === 0 AND $matchesEndedCount == 2){
				// vorher KOntrollieren ob nciht schon erzeugt das ganze
				$EventMatches = new EventMatches();
				$retCheck = $EventMatches->checkIfRound2AlreadyCreated($eventID, $createdEventID);
				p($retCheck);
				if(!$retCheck['status']){
					$Event = new Event();
					$retEventData = $Event->getEventData($eventID);
					p($retEventData);
					$matchModeID = $retEventData['data']['MatchModeID'];
					$matchTypeID = $retEventData['data']['MatchTypeID'];
					$regionID = $retEventData['data']['RegionID'];
					// wenn beide Matches gespielt -> dann kontrollieren ob genügend spieler in Pool für round 2
					$EventPool = new EventPool();
					$retEventPool = $EventPool->checkPoolEnoughPlayerForRound2($eventID, $createdEventID);
					p($retEventPool);
					$eventPoolData = $retEventPool['data'];
					$eventPoolCount = $retEventPool['count'];
					if($eventPoolCount == 10){
						// gesamtanzahl der spieler im pool durch 5 = Team anzahl
						$countTeams = 2;
						$Matchmaking = new Matchmaking();
						$retSepTeams = $Matchmaking->seperate10PlayersInto2BalancedTeams($eventPoolData);
						$sepTeams = $retSepTeams['data'];
						if(is_array($sepTeams) && count($sepTeams) > 0){
							$teamID = 5;
							foreach($sepTeams as $k =>$v){
								if(is_array($v) && count($v) > 0){
									foreach($v as $kPlayer =>$vPlayer){
										$steamID = $vPlayer['SteamID'];
										$elo = $vPlayer['Elo'];
										// Spieler in das Team hinzufügen
										$retInsTeams = $EventTeams->insertPlayerIntoTeam($eventID, $createdEventID, $teamID, $steamID, $elo, 2);
										p($retInsTeams);

									}
								}
								$teamID++;
							}
							// Nun die erzeugten Teams für Runde 2 auslesen und eintragen
							$retRoundTwoData = $EventTeams->getSecondRoundTeams($eventID, $createdEventID, 10);
							p($retRoundTwoData);
							$roundTwoData = $retRoundTwoData['data'];
								
							// für jeden eintrage ein Match erzeugen
							if(is_array($roundTwoData) && count($roundTwoData) > 0){
								$Match = new Match();
								$EventMatches = new EventMatches();
								$round = 2;
								$team1 = 5;
								$team2 = 6;
									
									
								// erzeuge Matches für Runde 2   T5<>T6
								$matchID = $Match->insertNewFoundMatch($regionID, $matchTypeID, $matchModeID);
								p($matchID);
								$matchID = $matchID['matchID'];
									
								$retSave = $Match->saveMatchDetailsForEventMatch($matchID, $roundTwoData[1], $roundTwoData[2]);
								p($retSave);
									
								$MatchDetailsHostLobby = new MatchDetailsHostLobby();
								$lobbyHost = $MatchDetailsHostLobby->setLobbyHostForMatch($matchID);
								
								
								// und schließlich EventMatch eintragen mit MatchID
								$retEvMatches = $EventMatches->insertEventMatchToEvent($eventID, $createdEventID, $matchID, $round, $team1, $team2);
							}
						}
					}
				}
				else{
					p("Round 2 Match schon eingetragen!");
				}

			}
			else{
				p("POOL keine 10 PLayer");
			}
				
		}
	}
}

p("END Event Matches - Round 2 handling");

/**
 * Nun kontrollieren ob schon Runde 2 zuende gespiel wurde
 * wenn das spielgecanceled wird -> createdEvent cancel  und keinen gewinner
 * wenn einer gewinnt, dann created event teamWonId eintragen, userWonEvent eintragen, Eloboost geben
*/

p("START Event Matches - Round 2 Match Handling");

$sql = "SELECT DISTINCT em.EventID, em.CreatedEventID, em.TeamWonID
		FROM `EventMatches` em JOIN Events e ON em.EventID = e.EventID
		JOIN `CreatedEvents` ce ON ce.CreatedEventID = em.CreatedEventID
		WHERE e.EndTimestamp = 0 AND e.Canceled = 0 AND ce.Canceled = 0 AND ce.EndTimestamp = 0
		AND em.Team1 = 5 AND em.Team2 = 6 AND em.TeamWonID != 0
		";
p($sql);
$data = $DB->multiSelect($sql);
p($data);

if(is_array($data) && count($data)>0){
	foreach($data as $k => $v){
		// jenachdem wie das spiel ausgegangen ist  behandeln
		$teamWonID = $v['TeamWonID'];
		$createdEventID = $v['CreatedEventID'];
		$eventID =  $v['EventID'];
		
		switch($teamWonID){
			case "-1":
				p("Letztes SPiel wurde gecanceled -> createdEvent canceln");
				// created Event canceln
				$CreatedEvents = new CreatedEvents();
				$retUpdate = $CreatedEvents->cancelCreatedEvent($createdEventID);
				p($retUpdate);
				break;
			case "-2":
				// von Admin begutachten lassen, per hand Wert ändern, dann per cronjob normal eintragen
				p("Ergebnis untersuchen lassen von Admin");
				break;
			default:
				// ein normales ergebnis
				p("NORMALES ERGEBNIS (5 oder 6)");
					
				// created Match beenden und gewinner festlegen
				$CreatedEvents = new CreatedEvents();
				$retWinner = $CreatedEvents->setWinnerForCreatedEvent($createdEventID, $teamWonID);
				p($retWinner);
				// use die gewonnen haben in UserWonEvents eintragen
				$EventTeams = new EventTeams();
				$retTeamData = $EventTeams->getTeam($eventID, $createdEventID, $teamWonID);
				p($retTeamData);
				$teamData = $retTeamData['data'];
				if(is_array($teamData) && count($teamData)>0){
					$UserWonEvents = new UserWonEvents();
					// zuerst Event daten auslesen
					$Event = new Event();
					$retEventData = $Event->getEventData($eventID);
					p($retEventData);
					$matchModeID = $retEventData['data']['MatchModeID'];
					$matchTypeID = $retEventData['data']['MatchTypeID'];
						
					foreach($teamData as $k => $v){
						$steamID = $v['SteamID'];
						$retIns = $UserWonEvents->insertUserWonEvent($steamID, $eventID, $createdEventID);
						p($retIns);
							
						// checken ob überhaupt der Preis ein Point-Boost ist
						p($retEventData['data']['PrizeType']);
						if($retEventData['data']['PrizeType'] == "Point-Boost"){
							// belohnung +50 Points hinzufügen
							$UserPoints = new UserPoints();
							$retUP = $UserPoints->insertEventRewardPointBonus($steamID, $eventID, EVENTBONUS, $matchTypeID, $matchModeID);
							p($retUP);
						}
						
						// belohnung +50 elo hinzufügen
// 						$UserElo = new UserElo();
// 						$UserStats = new UserStats();

// 						$retUserEloUp = $UserElo->changeUserElo($matchModeID, $matchTypeID, $steamID, 50, true);
// 						p($retUserEloUp);
// 						$retUserStatsUp = $UserStats->changeUserRank($steamID, 50, true);
					}
				}
				break;
		}
	}
}

p("END Event Matches - Round 2 Match Handling");
p("End EVENT MATCHES Handling");
?>