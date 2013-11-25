<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
p("Start new EVENT Handling");
$DB = new DB();
$con = $DB->conDB();

// für jeden eingetragengen EventTyp der aktiv ist ein Event eintragen
$sql = "SELECT *
		FROM `EventTypes`
		WHERE Active = 1
		";
p($sql);
$data = $DB->multiSelect($sql);

if(is_array($data) && count($data) > 0){
	foreach($data as $k =>$v){
		$eventTypeID = $v['EventTypeID'];
		$regionID = $v['RegionID'];
		
		// kontrollieren was drin steht  sortiert nach EndDatum
		$sql = "SELECT *
				FROM `Events`
				WHERE EventTypeID = ".(int)$eventTypeID."
						ORDER BY EventID DESC
						LIMIT 1
						";
		p($sql);
		$data = $DB->select($sql);

		$Event = new Event();

		// wenn Datensatz da  dann kontrollieren ob EndTimestamp schon gesetzt
		// wenn ja dann neues Event eintragen
		if(is_array($data) && count($data) > 0){
			if($data['EndTimestamp'] > 0){
				// neues Event in dem EventType anlegen
				$retIns = $Event->insertNewEvent($eventTypeID, $regionID);
				if($retIns['status']){
					p($retIns['status']);
					p("neues Event in EventType:".$eventTypeID." eingetragen");
				}
			}
			else{
				p("neues Event bereits eingetragen");
			}
		}
		// das erste mal wird ein Event eingetragen
		else{
			$retIns = $Event->insertNewEvent($eventTypeID, $regionID);
			if($retIns['status']){
				p($retIns);
				p("neues Event in EventType:".$eventTypeID." eingetragen");
			}

		}
	}
}

p("End new EVENT Handling");

/**
 * Wenn aktuelle Zeit > als Startzeit aktuelles Event
 * dann kontrollieren ob min Submissions erreicht
 * wenn ja dann in min submission teile aufteilen und kopien vom Event erzeugen
 * alle bekommen eine notification mit entsprechendem inhalt( event open, sry leider zu spät signed-in, event canceled)
*/
p("Start CreateEvent");
$EventSubmissions = new EventSubmissions();
// kontrollieren ob Event schon begonnen
$startTime = time();
$sql = "SELECT *
		FROM `Events` e JOIN `EventTypes` et ON e.EventTypeID = et.EventTypeID
		WHERE EndTimestamp = 0 AND StartTimestamp <= ".$startTime." 
			 AND NOT EXISTS (SELECT * FROM `CreatedEvents` cv WHERE cv.EventID = e.EventID)
				LIMIT 1
				";
p($sql);
$data = $DB->select($sql);
p($data);
// wenn Event gefunden, dann kontrollieren ob min Submissions erreicht
if(is_array($data) && count($data) > 0){
	$eventID = $data['EventID'];
	$eventTypeID = $data['EventTypeID'];
	$minSubmissions = $data['MinSubmissions'];
	$matchModeID = $data['MatchModeID'];
	$matchTypeID = $data['MatchTypeID'];
	$regionID = $data['RegionID'];
	
	// Alle Players rauswerfen die nicht rdy geklickt haben
	$retThroughOut = $EventSubmissions->throwOutPlayersOutOfEventWhoDontRdy($eventID);
	p($retThroughOut);
	$retReached = $Event->eventReachedMinSubmissions($eventID, $eventTypeID, $regionID);
	p($retReached);

	// wenn minimum erreicht, dann Event kopien anfertigen
	if($retReached['status']){
		// leute aufteilen
		$retChunk = $EventSubmissions->chunkSingedInPlayersInEvent($eventID, $minSubmissions, $matchModeID, $matchTypeID);
		$chunkedArray = $retChunk['data'];
		p($retChunk);
		$stackCount = count($chunkedArray);
		p("STACK_COUNT:".$stackCount);
		$CreatedEvents = new CreatedEvents();

		for($i=0; $i<$stackCount; $i++){
			$playerData = $chunkedArray[$i];
			$countPlayer = count($playerData);
			p("COUNT_PLAYER:".$countPlayer." PLAYERDATA:".print_r($playerData,1));
			// wenn genug spieler im Array
			if($countPlayer == $minSubmissions){
				p("GENUG SPIELER");
				// Event kopie erzeugen
				$retIns = $CreatedEvents->insertNewCreatedEvent($eventID, $eventTypeID);
				p($retIns);
				$createdEventID = $retIns['data'];
				if($createdEventID > 0){
					// Player zum erzeugten Event kopie zuweisen
					if(is_array($playerData) && count($playerData) > 0){
						foreach($playerData as $k =>$v){
							$steamID = $v['SteamID'];
							$retUpdate = $EventSubmissions->updateCreatedEventValueOfPlayer($steamID, $eventID, $createdEventID);
							p($retUpdate);
								
							// notification werden automatisch in profile.php getNotification erstellt.
								
						}
					}
					// die erfolgreich zugelosten spieler nun in teams aufteilen

					// gesamtanzahl der spieler im pool durch 5 = Team anzahl
					$countTeams = $minSubmissions/5;

						
						$EventTeams = new EventTeams();
						$Matchmaking = new Matchmaking();
						$retSepTeams = $Matchmaking->seperate20PlayersInto4BalancedTeams($playerData);
						p($retSepTeams);
						$sepTeams = $retSepTeams['data'];
						
						if(is_array($sepTeams) && count($sepTeams) > 0){
							$j = 1;
							foreach($sepTeams as $k =>$v){
								$teamID = $j;
								if(is_array($v) && count($v) > 0){
									foreach($v as $kPlayer =>$vPlayer){
										$steamID = $vPlayer['SteamID'];
										$elo = $vPlayer['Elo'];
										// Spieler in das Team hinzufügen
										$retInsTeams = $EventTeams->insertPlayerIntoTeam($eventID, $createdEventID, $teamID, $steamID, $elo,1);
										p($retInsTeams);
									}
								}
								$j++;
							}
						}
					
				
						// Nun die erzeugten Teams für Runde 1 auslesen und eintragen
						$retRoundOneData = $EventTeams->getFirstRoundTeams($eventID, $createdEventID, $minSubmissions);
						p($retRoundOneData);
						$roundOneData = $retRoundOneData['data'];
						
						// für jeden eintrage ein Match erzeugen
						if(is_array($roundOneData) && count($roundOneData) > 0){
							$Match = new Match();
							$EventMatches = new EventMatches();
							$z = 0;
							$round = 1;
							foreach($roundOneData as $k =>$v){
								if($z == 0){
									$team1 = 1;
									$team2 = 2;
								}
								else{
									$team1 = 3;
									$team2 = 4;
								}
								// erzeuge Matches für Runde 1   T1<>T2    und T3<>T4
								$matchID = $Match->insertNewFoundMatch($regionID, $matchTypeID, $matchModeID);
								p($matchID);
								$matchID = $matchID['matchID'];
								
								
								$retSave = $Match->saveMatchDetailsForEventMatch($matchID, $v[1], $v[2]);
								p($retSave);
								
								$MatchDetailsHostLobby = new MatchDetailsHostLobby();
								$lobbyHost = $MatchDetailsHostLobby->setLobbyHostForMatch($matchID);
								
								// und schließlich EventMatch eintragen mit MatchID
								$retEvMatches = $EventMatches->insertEventMatchToEvent($eventID, $createdEventID, $matchID, $round, $team1, $team2);
								
								$z++;
							}
						}
				}
				else{
					p("CREATED EVENT <= 0");
				}
			}
			else{
				// leider zu spät gekommen   -> notification schicken das too late und so
				// CreatedEvent auf -1 setzen um zu erkennen wer zu spät ist
				if(is_array($playerData) && count($playerData) > 0){
					foreach($playerData as $k =>$v){
						$steamID = $v['SteamID'];
						$retUpdate = $EventSubmissions->updateCreatedEventValueOfPlayer($steamID, $eventID, -1);
						p($retUpdate);
					}
				}
			}
		}
	}
	// Notification an alle singed in User senden das Event canceled wurde
	else{
		// Event auf canceled setzen
		$retCancel = $Event->cancelEvent($eventID);
		p($retCancel);

		// alle Signed-In user auslesen
		$retPlayer = $EventSubmissions->getPlayersSubmissionOfEvent($eventID);
		p($retPlayer);
		$playerData = $retPlayer['data'];
		if(is_array($playerData) && count($playerData) > 0){
			foreach($playerData as $k =>$v){
				// Created Event auf -2 setzen -> Event Canceled
				$steamID = $v['SteamID'];
				$retUpdate = $EventSubmissions->updateCreatedEventValueOfPlayer($steamID, $eventID, -2);
				p($retUpdate);
			}
		}
	}
}

p("End CreateEvent");

?>