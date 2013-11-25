<?php
/*
 * Copyright 2013 Artur Leinweber Date: 2013-01-01
*/
class Matchmaking {
	const maxEloUnterschied = 100; // Ab wann in anderen MatchModi mitgesucht werden soll
	const eloChangePerIteration = 10; // Um weiviel Elo sich bei jeder Iteration die range vergrößern/verkleinern soll
	const maxEloRange = 200; // um wieviel sich die Range ausweiten darf
	/*
	* Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function cleanDBMatchmaking($matchID) {
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret = array ();
		$matchID = ( int ) $matchID;

		$log = new KLogger ( "log.txt", KLogger::INFO );
		$log->LogInfo ( "Clean MatchTeams(" . $matchID . ")! initiated by:" . $_SESSION ['user'] ["steamID"] ); // Prints to the log file

		$MatchTeams = new MatchTeams ();

		$retM = $MatchTeams->checkIfPlayerAlreadyInMatchTeams ();

		if ($retM ['status']) {
			$sql = "DELETE FROM MatchTeams
					WHERE MatchID = " . ( int ) $matchID . "
							";
			$DB->delete ( $sql, 0 );
			$ret ['status'] = true;
		} else {
			$ret ['status'] = false;
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function singleQueueSearch($spielmodi, $regions, $forceSearch) {
		$retTest .= "- START singleQueueSearch - \n\n";
		$DB = new DB ();
		$con = $DB->conDB ();
		$UserStats = new UserStats ();
		$Queue = new Queue ();
		$ret = array ();
		$steamID = $_SESSION ['user'] ["steamID"];
		$matchType = 1;

		$retTest .= print_r ( $regions, 1 );
		if ($steamID > 0 && $_SESSION ['accepted'] == false) {
			// erste Iteration -> Session counts für jeden Modus initialisieren
			$retTest .= "QUEUE:" . is_array ( $_SESSION ['queue'] ) . " - " . count ( $_SESSION ['queue'] ) . " ELO:" . is_array ( $_SESSION ['elo'] ) . " - " . count ( $_SESSION ['elo'] ) . " \n\n";
			if (! is_array ( $_SESSION ['queue'] ) && ! count ( $_SESSION ['queue'] ) > 0 && is_array ( $_SESSION ['elo'] ) && count ( $_SESSION ['elo'] ) > 0) {
				$anzahlModi = count ( $spielmodi );
				if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {

					foreach ( $spielmodi as $k => $modus ) {
						// Count von gefundenen Spielern auf 1 setzen
						$_SESSION ['queue'] [$modus] = "1";

						// Anfangsrange setzen = aktueller Elo
						$_SESSION ['range'] [$modus] ['obere_grenze'] = $_SESSION ['elo'] [$modus];
						$_SESSION ['range'] [$modus] ['untere_grenze'] = $_SESSION ['elo'] [$modus];
					}
				}
				$retTest .= "RANGE wurde gesetzt " . print_r ( $_SESSION ['range'], 1 ) . "\n\n";
			}
			$retTest .= p ( $_SESSION ['queue'], 1 );
			// Sessions wurden bereits in vorherigen iterationen angelegt
			if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
				// kontrollieren ob durch andere User eine Queue bereits gefunden wurde
				// $sql = "SELECT DISTINCT MatchID
				// From `MatchTeams`
				// WHERE SteamID = ".secureNumber($steamID)."
				// LIMIT 1";
				$sql = "SELECT DISTINCT mt.MatchID
						From `MatchTeams` mt JOIN `Match` m ON m.MatchID = mt.MatchID
						JOIN `MatchDetails` md ON m.MatchID = md.MatchID AND mt.SteamID = md.SteamID
						WHERE mt.SteamID = " . secureNumber ( $steamID ) . "
								AND m.TeamWonID = -1 AND TimestampClosed = 0 AND Canceled = 0 AND ManuallyCheck = 0 AND mt.Ready = 0
								AND md.Submitted = 0 AND md.SubmissionFor = 0 AND md.SubmissionTimestamp = 0 AND EloChange = ''
								ORDER BY mt.MatchID DESC
								LIMIT 1";

				$matchID = $DB->select ( $sql );
				// p($matchID." - ");

				// pr�fen ob nicht schon fr�her best�tigt wurde
				$sql = "SELECT DISTINCT m.MatchID
						FROM `Match` m JOIN MatchDetails md ON m.MatchID = md.MatchID
						WHERE TeamWonID = -1 AND TimestampClosed = 0 AND Canceled = 0 AND ManuallyCheck = 0
						AND md.SteamID = " . secureNumber ( $steamID ) . " AND md.Submitted = 0 AND md.SubmissionFor = 0 AND md.SubmissionTimestamp = 0
								AND NOT EXISTS(SELECT mdcmv.SteamID FROM MatchDetailsCancelMatchVotes mdcmv
								WHERE mdcmv.SteamID = " . secureNumber ( $steamID ) . " AND mdcmv.MatchID = m.MatchID)
										ORDER BY m.MatchID DESC
										LIMIT 1
										";
				$retMatchID = $DB->select ( $sql );

				$matchID = ( int ) $matchID ['MatchID'];
				$matchID2 = ( int ) $retMatchID ['MatchID'];
				$retTest .= "##MATCHID##:" . $matchID;
				// p($count);
				// wenn match bereits gefunden durch andere User gefudnen

				if ($matchID > 0 or $matchID2 > 0) {

					$log = new KLogger ( "log.txt", KLogger::INFO );
					$log->LogInfo ( "Durch anderen Spieler gefunden! MatchID=" . ( int ) $matchID . "|" . ( int ) $matchID2 . "! ID der dieses Verfahren bekommt: " . $steamID ); // Prints to the log file

					$ret ['queue'] = $_SESSION ['queue'];
					$ret ['range'] = $_SESSION ['range'];
					$ret ['erweiterteSuche'] = $_SESSION ['erweiterteSuche'];
					unset ( $_SESSION ['queue'] );
					unset ( $_SESSION ['erweiterteSuche'] );
					unset ( $_SESSION ['range'] );
					$ret ['test'] = $retTest;

					if ($matchID >= $matchID2) {
						$ret ['matchID'] = $matchID;
						$log->LogInfo ( "MatchID=" . ( int ) $matchID . " weitergereicht - " . $steamID ); // Prints to the log file
					} else {
						$log->LogInfo ( "MatchID2=" . ( int ) $matchID2 . " weitergereicht - " . $steamID ); // Prints to the log file
						$ret ['matchID'] = $matchID2;
					}
					$ret ['status'] = "finished_through_other_player";
				} 				// nicht durch andere User eingetragen
				else {
					// spieler suchen für alle Spielmodi -> werden gleich in die Session geschrieben
					// if(count($_SESSION['erweiterteSuche']) == 0){
					// //p($_SESSION['range']);
					// $retSingle = $this->getPlayerCountsOfQueueForPlayerSingleRange($steamID, $spielmodi, $_SESSION['range'], $regions);
					// $retTest .= $retSingle['debug'];
					// $matchDetails .= $retSingle['matchDetails'];
					// }
					// else{
					// // erweiterte suche über mehrere Spielmodi
					// $erweiterteSuche = $_SESSION['erweiterteSuche'];
					// // Differenz der Arrays bestimmen -> damit der spielmodus der noch nicht ausgeweitet ist extra behandelt werden kann
					// $diff = array_diff($spielmodi, $erweiterteSuche);

					// // Multiple behandelt
					// $retMultiple = $this->getPlayerCountsOfQueueForPlayerMultipleModi($steamID, $erweiterteSuche, $_SESSION['range'], $regions);
					// $retTest .= $retMultiple['debug'];
					// $matchDetails .= $retMultiple['matchDetails'];

					// // die noch nciht ausgeweitet sind behandeln
					// $retSingle = $this->getPlayerCountsOfQueueForPlayerSingleRange($steamID, $diff, $_SESSION['range'], $regions);
					// $retTest .= $retSingle['debug'];
					// $matchDetails .= $retSingle['matchDetails'];
					// }

					$retSingle = $this->getPlayerCountsOfQueueForPlayerSingleRange2 ( $steamID, $spielmodi, $_SESSION ['range'], $regions, $forceSearch );
					$retTest .= $retSingle ['debug'];
					$matchDetails .= $retSingle ['matchDetails'];
					// für jeden Spielmodus kontrollieren ob 10 Spieler gefunden wurden
					if (is_array ( $_SESSION ['queue'] ) && count ( $_SESSION ['queue'] ) > 0) {
						$found_players = false;
						foreach ( $_SESSION ['queue'] as $kqueue => $vqueue ) {
							if ($vqueue == 10) {
								$found_players = true;
							}
						}
					}

					$retTest .= "##FOUND_Players##:" . print_r ( $_SESSION ['queue'], 1 ) . " " . $found_players;
					// 10 Spieler gefunden
					if ($found_players) {
						$log = new KLogger ( "log.txt", KLogger::INFO );
						$log->LogInfo ( "10 Spieler gefunden durch:" . $steamID ); // Prints to the log file
						$log->LogInfo ( "quickJoin:" . $quickJoin . "|" ); // Prints to the log file
						$log->LogInfo ( "regions:" . print_r ( $retSingle ['originalRegions'], 1 ) ); // Prints to the log file

						// die Queue leeren mit den zehn leuten
						$Queue = new Queue ();
						$retKick = $Queue->kickPlayersOutOfQueueIf10PlayersFound ( $retSingle ['foundPlayersData'] );

						// die 10 gefundenen spieler in MatchTeams eintragen
						$MatchTeams = new MatchTeams ();

						$retHandleFound = $this->handleFoundPlayersSingleQueue ( $steamID, $retSingle ['foundPlayersSQL'], $retMultiple ['foundPlayersSQL'], $retSingle ['foundPlayersData'] );
						$retTest .= $retHandleFound ['debug'];

						$ret ['queue'] = $_SESSION ['queue'];
						$ret ['range'] = $_SESSION ['range'];
						$ret ['erweiterteSuche'] = $_SESSION ['erweiterteSuche'];
						$ret ['matchID'] = $retHandleFound ['matchID'];
						unset ( $_SESSION ['queue'] );
						unset ( $_SESSION ['erweiterteSuche'] );
						unset ( $_SESSION ['range'] );
						$ret ['matchDetails'] = $matchDetails;
						$ret ['test'] = $retTest . "\n\n\n####################\n";
						if ($retHandleFound ['status'] == true) {
							$ret ['status'] = "finished";
						} else {
							$ret ['status'] = "searching";
						}
					} 					// keine 10 Spieler gefunden
					else {
						// range erweitern
						$retTest .= "wide Range und so \n";
						$retWideRange = $this->wideRangeOfSearch ( $spielmodi, $forceSearch );
						$retTest .= "END wide Range und so \n";
						// $retTest .= $retWideRange;
						$ret ['retWideRange'] = $retWideRange;
						$ret ['test'] = $retTest . "\n\n\n####################\n";
						$ret ['queue'] = $_SESSION ['queue'];
						$ret ['range'] = $_SESSION ['range'];
						$ret ['erweiterteSuche'] = $_SESSION ['erweiterteSuche'];
						$ret ['status'] = "searching";
						$ret ['matchDetails'] = $matchDetails . $retWideRange;
					}
				}
			}
		} else {
			$ret ['status'] = "not logged in";
		}
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function singleQueueSearch2($spielmodi, $regions, $forceSearch) {
		$retTest .= "- START singleQueueSearch - \n\n";
		$DB = new DB ();
		$con = $DB->conDB ();
		$UserStats = new UserStats ();
		$Queue = new Queue ();
		$ret = array ();
		$steamID = $_SESSION ['user'] ["steamID"];
		$matchType = 1;
		$retTest .= p ( $_SESSION, 1 );

		// Range Array erstellen - workaround
		if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
			$rangeArray = array ();
			foreach ( $spielmodi as $k => $modus ) {
				// Count von gefundenen Spielern auf 1 setzen
				$_SESSION ['queue'] [$modus] ['count'] = "1";
				$_SESSION ['queue'] [$modus] ['position'] = "1";

				// Anfangsrange setzen = aktueller Elo
				if ($_SESSION ['points'] >= Matchmaking::maxEloRange) {
					$rangeArray [$modus] ['obere_grenze'] = $_SESSION ['points'] [$modus] + Matchmaking::maxEloRange;
					$rangeArray [$modus] ['untere_grenze'] = $_SESSION ['points'] [$modus] - Matchmaking::maxEloRange;
				} else {
					$rangeArray [$modus] ['obere_grenze'] = $_SESSION ['points'] [$modus] + Matchmaking::maxEloRange;
					$rangeArray [$modus] ['untere_grenze'] = 0;
				}
			}
		}

		// CheckTimestamp setzen
		$this->setInQueueTime ( $steamID );

		// update ForceSearch
		$retQ = $Queue->updateForceSearch($forceSearch);

		// check ob duo partner noch in Queue
		$SingleQueueGroup = new SingleQueueGroup ();
		$retSQG = $SingleQueueGroup->checkIfAlreadyInQueueWithGroup ();
		$ret['inQueue'] = $retSQG['inQueue'];

		// checken ob bereits in MatchTeams
		$MatchTeams = new MatchTeams ();
		$retMT = $MatchTeams->checkIfPlayerAlreadyInMatchTeams();
		$ret['inMatchTeams'] = $retMT['status'];

		// UserLeague kontrollieren und in ne schublade packen
		// $UserLeague = new UserLeague();
		// $retU = $UserLeague->getLeagueOfUser($steamID);
		// $userLeague = $retU['data']['LeagueTypeID'];
		// if($userLeague >= 3){
		// $silverOrHigher = true;
		// }
		// else{
		// $silverOrHigher = false;
		// }

		$UserSkillBracket = new UserSkillBracket ();
		$retUSB = $UserSkillBracket->getSkillBracketOfUser ($steamID, $matchType);
		$userSkillBRacketTypeID = $retUSB ['data'] ['SkillBracketTypeID'];

		if ($forceSearch == "true" && $userSkillBRacketTypeID != "1") {
			$skillBracket = "Force";
		} else {
			switch ($userSkillBRacketTypeID) {
				case 1 :
				case 2 :
					$skillBracket = $retUSB ['data'] ['Name'];
					break;
				default :
					$skillBracket = "Amateur or higher";
					break;
			}
		}

		$ret ['skillBracket'] = $skillBracket;

		// Queue Stats
		$retSBQC = $this->getSkillBracketQueueCounts($matchType);
		$ret['queueCounts'] = $retSBQC['data'];

		$retSingle = $this->getPlayerCountsOfQueueSkillBracket ( $steamID, $spielmodi, $rangeArray, $regions, $userSkillBRacketTypeID, $forceSearch, $matchType );
		$retTest .= $retSingle ['debug'];
		$matchDetails .= $retSingle ['matchDetails'];


	
		
		// checken ob schon durch vronjob gefunden wurde
		$sql = "SELECT DISTINCT mt.MatchID
				From `MatchTeams` mt JOIN `Match` m ON m.MatchID = mt.MatchID
				JOIN `MatchDetails` md ON m.MatchID = md.MatchID AND mt.SteamID = md.SteamID
				WHERE mt.SteamID = " . secureNumber ( $steamID ) . "
						AND m.TeamWonID = -1 AND TimestampClosed = 0 AND Canceled = 0 AND ManuallyCheck = 0 AND mt.Ready = 0
						AND md.Submitted = 0 AND md.SubmissionFor = 0 AND md.SubmissionTimestamp = 0 AND EloChange = ''
						ORDER BY mt.MatchID DESC
						LIMIT 1";

		$matchID = $DB->select ( $sql );

		$matchID = ( int ) $matchID ['MatchID'];
		$matchID2 = ( int ) $retMatchID ['MatchID'];
		$retTest .= "##MATCHID##:" . $matchID . "|" . $matchID2;
		// p($count);
		// wenn match bereits gefunden durch andere User gefudnen

		if ($matchID > 0 || $matchID2 > 0) {

			$log = new KLogger ( "log.txt", KLogger::INFO );
			$log->LogInfo ( "Durch anderen Spieler gefunden! MatchID=" . ( int ) $matchID . "|" . ( int ) $matchID2 . "! ID der dieses Verfahren bekommt: " . $steamID ); // Prints to the log file

			$ret ['queue'] = $_SESSION ['queue'];
			$ret ['range'] = $_SESSION ['range'];
			$ret ['erweiterteSuche'] = $_SESSION ['erweiterteSuche'];
			unset ( $_SESSION ['queue'] );
			unset ( $_SESSION ['erweiterteSuche'] );
			unset ( $_SESSION ['range'] );
			$ret ['test'] = $retTest;

			if ($matchID >= $matchID2) {
				$ret ['matchID'] = $matchID;
				$log->LogInfo ( "MatchID=" . ( int ) $matchID . " weitergereicht - " . $steamID ); // Prints to the log file
			} else {
				$log->LogInfo ( "MatchID2=" . ( int ) $matchID2 . " weitergereicht - " . $steamID ); // Prints to the log file
				$ret ['matchID'] = $matchID2;
			}
			$ret ['status'] = "finished";
		} else {
			// checken ob User noch in Queue
			$inQueue = $Queue->inQueue();
			if(!$inQueue){
				$ret['status'] = "notInQueue";
			}
			else{
				$ret ['status'] = "searching";
			}
			
		}
		// $retTest .= $retWideRange;
		$ret ['retWideRange'] = $retWideRange;
		$ret ['test'] = $retTest . "\n\n\n####################\n";
		$ret ['queue'] = $_SESSION ['queue'];
		$ret ['range'] = $rangeArray;
		$ret ['erweiterteSuche'] = $_SESSION ['erweiterteSuche'];

		$ret ['matchDetails'] = $matchDetails . $retWideRange;

		// seconds till next matchmaking
		$cur_secs = date ( "s" );
		$left_sec = 60 - $cur_secs;
		$ret ['nextMatchmaking'] = ( int ) $left_sec;

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function oneVsOneQueueSearch($spielmodi, $regions, $forceSearch) {
		$retTest .= "- START oneVsOneQueueSearch - \n\n";
		$DB = new DB ();
		$con = $DB->conDB ();
		$UserStats = new UserStats ();
		$Queue = new Queue ();
		$ret = array ();
		$steamID = $_SESSION ['user'] ["steamID"];
		$matchType = 8;
		$retTest .= p ( "SESSION", 1 );
		$retTest .= p ( $_SESSION, 1 );

		// Range Array erstellen - workaround
		if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
			$rangeArray = array ();
			foreach ( $spielmodi as $k => $modus ) {
				// Count von gefundenen Spielern auf 1 setzen
				$_SESSION ['queue'] [$modus] ['count'] = "1";
				$_SESSION ['queue'] [$modus] ['position'] = "1";

				// Anfangsrange setzen = aktueller Elo
				if ($_SESSION ['points'] >= Matchmaking::maxEloRange) {
					$rangeArray [$modus] ['obere_grenze'] = $_SESSION ['points'] [$modus] + Matchmaking::maxEloRange;
					$rangeArray [$modus] ['untere_grenze'] = $_SESSION ['points'] [$modus] - Matchmaking::maxEloRange;
				} else {
					$rangeArray [$modus] ['obere_grenze'] = $_SESSION ['points'] [$modus] + Matchmaking::maxEloRange;
					$rangeArray [$modus] ['untere_grenze'] = 0;
				}
			}
		}
		
		// CheckTimestamp setzen
		$this->setInQueueTime ( $steamID );
		
		// update ForceSearch
		$retQ = $Queue->updateForceSearch($forceSearch);
		
		// check ob duo partner noch in Queue
		$SingleQueueGroup = new SingleQueueGroup ();
		$retSQG = $SingleQueueGroup->checkIfAlreadyInQueueWithGroup ();
		$ret['inQueue'] = $retSQG['inQueue'];
		
		// checken ob bereits in MatchTeams
		$MatchTeams = new MatchTeams ();
		$retMT = $MatchTeams->checkIfPlayerAlreadyInMatchTeams();
		$ret['inMatchTeams'] = $retMT['status'];
		
		// UserLeague kontrollieren und in ne schublade packen
		// $UserLeague = new UserLeague();
		// $retU = $UserLeague->getLeagueOfUser($steamID);
		// $userLeague = $retU['data']['LeagueTypeID'];
		// if($userLeague >= 3){
		// $silverOrHigher = true;
		// }
		// else{
		// $silverOrHigher = false;
		// }
		
		$UserSkillBracket = new UserSkillBracket ();
		$retUSB = $UserSkillBracket->getSkillBracketOfUser ( $steamID, $matchType);
		$userSkillBRacketTypeID = $retUSB ['data'] ['SkillBracketTypeID'];
		
		if ($forceSearch == "true") {
			$skillBracket = "Force";
		} else {
			switch ($userSkillBRacketTypeID) {
				case 1 :
				case 2 :
					$skillBracket = $retUSB ['data'] ['Name'];
					break;
				default :
					$skillBracket = "Amateur or higher";
					break;
			}
		}
		
		$ret ['skillBracket'] = $skillBracket;
		
		// Queue Stats
		$retSBQC = $this->getSkillBracketQueueCounts($matchType);
		$ret['queueCounts'] = $retSBQC['data'];
		
		$retSingle = $this->getPlayerCountsOfQueueSkillBracket ( $steamID, $spielmodi, $rangeArray, $regions, $userSkillBRacketTypeID, $forceSearch, $matchType );
		$retTest .= $retSingle ['debug'];
		$matchDetails .= $retSingle ['matchDetails'];
		
		
		// checken ob schon durch vronjob gefunden wurde
		// kontrollieren ob durch andere User eine Queue bereits gefunden wurde
		// $sql = "SELECT DISTINCT MatchID
		// From `MatchTeams`
		// WHERE SteamID = ".secureNumber($steamID)."
		// LIMIT 1";
		$sql = "SELECT DISTINCT mt.MatchID
				From `MatchTeams` mt JOIN `Match` m ON m.MatchID = mt.MatchID
				JOIN `MatchDetails` md ON m.MatchID = md.MatchID AND mt.SteamID = md.SteamID
				WHERE mt.SteamID = " . secureNumber ( $steamID ) . "
						AND m.TeamWonID = -1 AND TimestampClosed = 0 AND Canceled = 0 AND ManuallyCheck = 0 AND mt.Ready = 0
						AND md.Submitted = 0 AND md.SubmissionFor = 0 AND md.SubmissionTimestamp = 0 AND EloChange = ''
						ORDER BY mt.MatchID DESC
						LIMIT 1";

		$matchID = $DB->select ( $sql );
		// p($matchID." - ");

		// pr�fen ob nicht schon fr�her best�tigt wurde
		// $sql = "SELECT DISTINCT m.MatchID
		// FROM `Match` m JOIN MatchDetails md ON m.MatchID = md.MatchID
		// WHERE TeamWonID = -1 AND TimestampClosed = 0 AND Canceled = 0 AND ManuallyCheck = 0
		// AND md.SteamID = ".secureNumber($steamID)." AND md.Submitted = 0 AND md.SubmissionFor = 0 AND md.SubmissionTimestamp = 0
		// AND NOT EXISTS(SELECT mdcmv.SteamID FROM MatchDetailsCancelMatchVotes mdcmv
		// WHERE mdcmv.SteamID = ".secureNumber($steamID)." AND mdcmv.MatchID = m.MatchID)
		// ORDER BY m.MatchID DESC
		// LIMIT 1
		// ";
		// $retMatchID = $DB->select($sql);


		
		$matchID = ( int ) $matchID ['MatchID'];
		$matchID2 = ( int ) $retMatchID ['MatchID'];
		$retTest .= "##MATCHID##:" . $matchID . "|" . $matchID2;
		// p($count);
		// wenn match bereits gefunden durch andere User gefudnen
		$ret['MatchID'] = $matchID;
		if ($matchID > 0 || $matchID2 > 0) {

			$log = new KLogger ( "log.txt", KLogger::INFO );
			$log->LogInfo ( "Durch anderen Spieler gefunden! MatchID=" . ( int ) $matchID . "|" . ( int ) $matchID2 . "! ID der dieses Verfahren bekommt: " . $steamID ); // Prints to the log file

			$ret ['queue'] = $_SESSION ['queue'];
			$ret ['range'] = $_SESSION ['range'];
			$ret ['erweiterteSuche'] = $_SESSION ['erweiterteSuche'];
			
			unset ( $_SESSION ['queue'] );
			unset ( $_SESSION ['erweiterteSuche'] );
			unset ( $_SESSION ['range'] );
			$ret ['test'] = $retTest;

			if ($matchID >= $matchID2) {
				$ret ['matchID'] = $matchID;
				$log->LogInfo ( "MatchID=" . ( int ) $matchID . " weitergereicht - " . $steamID ); // Prints to the log file
			} else {
				$log->LogInfo ( "MatchID2=" . ( int ) $matchID2 . " weitergereicht - " . $steamID ); // Prints to the log file
				$ret ['matchID'] = $matchID2;
			}
			$ret ['status'] = "finished";
		} else {
			// checken ob User noch in Queue
			$inQueue = $Queue->inQueue();
			if(!$inQueue){
				$ret['status'] = "notInQueue";
			}
			else{
				$ret ['status'] = "searching";
			}
			
		}
		// $retTest .= $retWideRange;
		$ret ['retWideRange'] = $retWideRange;
		$ret ['test'] = $retTest . "\n\n\n####################\n";
		$ret ['queue'] = $_SESSION ['queue'];
		$ret ['range'] = $rangeArray;
		$ret ['erweiterteSuche'] = $_SESSION ['erweiterteSuche'];

		$ret ['matchDetails'] = $matchDetails . $retWideRange;
		
		// seconds till next matchmaking
		$cur_secs = date ( "s" );
		$left_sec = 60 - $cur_secs;
		$ret ['nextMatchmaking'] = ( int ) $left_sec;

		
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function threeVsThreeQueueSearch($spielmodi, $regions, $forceSearch) {
		$retTest .= "- START oneVsOneQueueSearch - \n\n";
		$DB = new DB ();
		$con = $DB->conDB ();
		$UserStats = new UserStats ();
		$Queue = new Queue ();
		$ret = array ();
		$steamID = $_SESSION ['user'] ["steamID"];
		$matchType = ThreeVsThreeQueue::matchType;
		$retTest .= p ( "SESSION", 1 );
		$retTest .= p ( $_SESSION, 1 );

		// Range Array erstellen - workaround
		if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
			$rangeArray = array ();
			foreach ( $spielmodi as $k => $modus ) {
				// Count von gefundenen Spielern auf 1 setzen
				$_SESSION ['queue'] [$modus] = "1";

				// Anfangsrange setzen = aktueller Elo
				if ($_SESSION ['points'] >= Matchmaking::maxEloRange) {
					$rangeArray [$modus] ['obere_grenze'] = $_SESSION ['points'] [$modus] + Matchmaking::maxEloRange;
					$rangeArray [$modus] ['untere_grenze'] = $_SESSION ['points'] [$modus] - Matchmaking::maxEloRange;
				} else {
					$rangeArray [$modus] ['obere_grenze'] = $_SESSION ['points'] [$modus] + Matchmaking::maxEloRange;
					$rangeArray [$modus] ['untere_grenze'] = 0;
				}
			}
		}
		$retSingle = $this->getPlayerCountsOfQueueForPlayerSingleRange2 ( $steamID, $spielmodi, $rangeArray, $regions, $forceSearch, $matchType );
		$retTest .= $retSingle ['debug'];
		$matchDetails .= $retSingle ['matchDetails'];

		// checken ob schon durch vronjob gefunden wurde
		// kontrollieren ob durch andere User eine Queue bereits gefunden wurde
		// $sql = "SELECT DISTINCT MatchID
		// From `MatchTeams`
		// WHERE SteamID = ".secureNumber($steamID)."
		// LIMIT 1";
		$sql = "SELECT DISTINCT mt.MatchID
				From `MatchTeams` mt JOIN `Match` m ON m.MatchID = mt.MatchID
				JOIN `MatchDetails` md ON m.MatchID = md.MatchID AND mt.SteamID = md.SteamID
				WHERE mt.SteamID = " . secureNumber ( $steamID ) . "
						AND m.TeamWonID = -1 AND TimestampClosed = 0 AND Canceled = 0 AND ManuallyCheck = 0 AND mt.Ready = 0
						AND md.Submitted = 0 AND md.SubmissionFor = 0 AND md.SubmissionTimestamp = 0 AND EloChange = ''
						ORDER BY mt.MatchID DESC
						LIMIT 1";

		$matchID = $DB->select ( $sql );
		// p($matchID." - ");

		// pr�fen ob nicht schon fr�her best�tigt wurde
		// $sql = "SELECT DISTINCT m.MatchID
		// FROM `Match` m JOIN MatchDetails md ON m.MatchID = md.MatchID
		// WHERE TeamWonID = -1 AND TimestampClosed = 0 AND Canceled = 0 AND ManuallyCheck = 0
		// AND md.SteamID = ".secureNumber($steamID)." AND md.Submitted = 0 AND md.SubmissionFor = 0 AND md.SubmissionTimestamp = 0
		// AND NOT EXISTS(SELECT mdcmv.SteamID FROM MatchDetailsCancelMatchVotes mdcmv
		// WHERE mdcmv.SteamID = ".secureNumber($steamID)." AND mdcmv.MatchID = m.MatchID)
		// ORDER BY m.MatchID DESC
		// LIMIT 1
		// ";
		// $retMatchID = $DB->select($sql);

		$matchID = ( int ) $matchID ['MatchID'];
		$matchID2 = ( int ) $retMatchID ['MatchID'];
		$retTest .= "##MATCHID##:" . $matchID . "|" . $matchID2;
		// p($count);
		// wenn match bereits gefunden durch andere User gefudnen

		if ($matchID > 0 or $matchID2 > 0) {

			$log = new KLogger ( "log.txt", KLogger::INFO );
			$log->LogInfo ( "Durch anderen Spieler gefunden! MatchID=" . ( int ) $matchID . "|" . ( int ) $matchID2 . "! ID der dieses Verfahren bekommt: " . $steamID ); // Prints to the log file

			$ret ['queue'] = $_SESSION ['queue'];
			$ret ['range'] = $_SESSION ['range'];
			$ret ['erweiterteSuche'] = $_SESSION ['erweiterteSuche'];
			unset ( $_SESSION ['queue'] );
			unset ( $_SESSION ['erweiterteSuche'] );
			unset ( $_SESSION ['range'] );
			$ret ['test'] = $retTest;

			if ($matchID >= $matchID2) {
				$ret ['matchID'] = $matchID;
				$log->LogInfo ( "MatchID=" . ( int ) $matchID . " weitergereicht - " . $steamID ); // Prints to the log file
			} else {
				$log->LogInfo ( "MatchID2=" . ( int ) $matchID2 . " weitergereicht - " . $steamID ); // Prints to the log file
				$ret ['matchID'] = $matchID2;
			}
			$ret ['status'] = "finished";
		} else {
			$ret ['status'] = "searching";
		}
		// $retTest .= $retWideRange;
		$ret ['retWideRange'] = $retWideRange;
		$ret ['test'] = $retTest . "\n\n\n####################\n";
		$ret ['queue'] = $_SESSION ['queue'];
		$ret ['range'] = $rangeArray;
		$ret ['erweiterteSuche'] = $_SESSION ['erweiterteSuche'];

		$ret ['matchDetails'] = $matchDetails . $retWideRange;

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getPlayerCountsOfQueueForPlayerSingleRange($steamID, $spielmodi, $rangeArray, $regions, $forceSearch) {
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "- Start getPlayerCountsOfQueueForPlayerSingleRange - \n\n";
		$ret ['foundPlayersSQL'] = array ();
		$ret ['foundPlayersData'] = array ();

		// $_SESSION['erweiterteSuche'] = array("6");
		// $ret .= "rangeArray: ".print_r($rangeArray,1)." \n\n";
		if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
			if (is_array ( $regions ) && count ( $regions ) > 0) {
				// region SQL Statement generieren lassen
				$regionsSQL = $this->getSQLForRegion ( $regions );
				$ret ['debug'] .= print_r ( $regionsSQL, 1 );
				$regionsSQL = $regionsSQL ['regionSQL'];

				foreach ( $spielmodi as $k => $modus ) {
					$countArray = array ();

					foreach ( $regions as $k => $region ) {

						$sql = "SELECT DISTINCT SteamID, Elo, MatchModeID, Region, MatchTypeID
								FROM `Queue`
								WHERE SteamID = " . secureNumber ( $steamID ) . " AND MatchTypeID = 1 AND MatchModeID = " . ( int ) $modus . " AND Region = " . ( int ) $region . "
										";
						$ret ['debug'] .= p ( $sql, 1 );
						$dataPlayer = $DB->select ( $sql );
						$countPlayerInMatchMode = count ( $dataPlayer );
						// grkcen ob in einer Gruppe drin ist
						$sql = "SELECT DISTINCT qgm.SteamID, Elo, qgm.MatchModeID, Region, qgm.MatchTypeID, qgm.GroupID
								FROM QueueGroup qg JOIN QueueGroupMembers qgm ON qg.GroupID = qgm.groupID
								WHERE (qgm.SteamID = " . secureNumber ( $steamID ) . " AND qgm.MatchTypeID = 1 AND qgm.MatchModeID = " . ( int ) $modus . " AND qg.Region = " . ( int ) $region . ")
										";
						$ret ['debug'] .= p ( $sql, 1 );
						$dataPlayer2 = $DB->select ( $sql );
						$countPlayerInMatchMode2 = count ( $dataPlayer2 );

						// Solo Mensch
						if ($countPlayerInMatchMode > 0) {
							$data = array ();
							// Wenn force Search, dann auch nur force Search Leute suchen
							if ($forceSearch == "true") {
								$rangeUnten = ( int ) $_SESSION ['elo'] [$modus] - Matchmaking::maxEloRange;
								$rangeOben = ( int ) $_SESSION ['elo'] [$modus] + Matchmaking::maxEloRange;
								$forceSQL = " AND ForceSearch = 1";
							} else {
								$forceSQL = "AND ForceSearch = 0";
							}

							$untere_grenze = ( int ) $rangeArray [$modus] ['untere_grenze'];
							$obere_grenze = ( int ) $rangeArray [$modus] ['obere_grenze'];

							$ret ['debug'] .= "MODUS: " . $modus . " \n\n";

							$ret ['debug'] .= " UG:" . $untere_grenze . " OG:" . $obere_grenze . " \n\n";

							$count = 0;

							$sql = "SELECT DISTINCT SteamID, Elo, MatchModeID, Region, MatchTypeID
									FROM Queue
									WHERE (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
											AND Region = " . ( int ) $region . "
													" . $forceSQL . ")
															AND SteamID != " . secureNumber ( $steamID ) . ")
																	AND MatchModeID = " . ( int ) $modus . "
																			AND MatchTypeID = 1
																			ORDER BY Timestamp ASC
																			LIMIT 9
																			";
							$data = $DB->multiSelect ( $sql );
							$ret ['debug'] .= p ( $sql, 1 );

							$count = count ( $data );

							switch ($count) {
								case 0 :
									$limit = 8;
									break;
								case 1 :
									$limit = 8;
									break;
								case 2 :
									$limit = 6;
									break;
								case 3 :
									$limit = 6;
									break;
								case 4 :
									$limit = 4;
									break;
								case 5 :
									$limit = 4;
									break;
								case 6 :
									$limit = 2;
									break;
								case 7 :
									$limit = 2;
									break;
								case 8 :
									$limit = 2;
									break;
								case 9 :
									$limit = 0;
									break;
							}
							$data2 = array ();
							if ($limit > 0) {
								// Nach Gruppen suchen
								$sql2 = "SELECT DISTINCT SteamID, Elo, qg.MatchModeID, Region, qg.MatchTypeID, qg.GroupID
										FROM QueueGroup qg JOIN QueueGroupMembers qgm
										ON qg.GroupID = qgm.GroupID AND
										qg.MatchTypeID = qgm.MatchTypeID AND
										qg.MatchModeID = qgm.MatchModeID
										WHERE (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
												AND Region = " . ( int ) $region . "
														" . $forceSQL . ")
																AND SteamID != " . secureNumber ( $steamID ) . ")
																		AND qg.MatchModeID =  " . ( int ) $modus . "
																				AND qg.MatchTypeID = 1
																				ORDER BY Timestamp ASC
																				LIMIT " . $limit . "
																						";
								$ret ['debug'] .= p ( $sql2, 1 );
								$data2 = $DB->multiSelect ( $sql2 );

								$count = $count + count ( $data2 );
							}

							$count = $count + 1;
							if ($count == 0) {
								// $count = 1; // workAround für division by zero für wideRange udn so
							}

							$countArray [] = $count;

							// matchDetails
							$ret ['matchDetails'] .= "<p>" . date ( 'H:i:s' ) . " - " . "Searching in MatchMode: " . $modus . " witch following ranges: " . $untere_grenze . " - " . $obere_grenze . " Players found: " . $count . "</p>";

							if ($count == 10) {
								if (is_array ( $dataPlayer ) && count ( $dataPlayer ) > 0) {
									$data [] = $dataPlayer;
								} else {
									// sich selber noch ins
									$data [] = $dataPlayer2;
								}

								$ret ['foundPlayersSQL'] [$modus] = $sql . $sql2;
								$ret ['foundPlayersData'] [$modus] = array_merge ( $data, $data2 );
								$ret ['debug'] .= p ( array_merge ( $data, $data2 ), 1 );
							} else {
							}
						}
						// Ich bin Gruppenmensch
						if ($countPlayerInMatchMode2 > 0) {
							$data = array ();
							// Wenn force Search, dann auch nur force Search Leute suchen
							if ($forceSearch == "true") {
								$rangeUnten = ( int ) $_SESSION ['elo'] [$modus] - Matchmaking::maxEloRange;
								$rangeOben = ( int ) $_SESSION ['elo'] [$modus] + Matchmaking::maxEloRange;
								$forceSQL = " AND ForceSearch = 1";
							} else {
								$forceSQL = "AND ForceSearch = 0";
							}

							$untere_grenze = ( int ) $rangeArray [$modus] ['untere_grenze'];
							$obere_grenze = ( int ) $rangeArray [$modus] ['obere_grenze'];

							$ret ['debug'] .= "MODUS: " . $modus . " \n\n";

							$ret ['debug'] .= " UG:" . $untere_grenze . " OG:" . $obere_grenze . " \n\n";

							$count = 0;

							$sql = "SELECT DISTINCT SteamID, Elo, MatchModeID, Region, MatchTypeID
									FROM Queue
									WHERE (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
											AND Region = " . ( int ) $region . "
													" . $forceSQL . ")
															AND SteamID != " . secureNumber ( $steamID ) . ")
																	AND MatchModeID = " . ( int ) $modus . "
																			AND MatchTypeID = 1
																			ORDER BY Timestamp ASC
																			LIMIT 8
																			";
							$data = $DB->multiSelect ( $sql );
							$ret ['debug'] .= p ( $sql, 1 );

							$count = count ( $data );

							switch ($count) {
								case 0 :
									$limit = 8;
									break;
								case 1 :
									$limit = 8;
									break;
								case 2 :
									$limit = 8;
									break;
								case 3 :
									$limit = 6;
									break;
								case 4 :
									$limit = 6;
									break;
								case 5 :
									$limit = 4;
									break;
								case 6 :
									$limit = 4;
									break;
								case 7 :
									$limit = 2;
									break;
								case 8 :
									$limit = 2;
									break;
							}
							$data2 = array ();
							if ($limit > 0) {
								// Nach Gruppen suchen
								$sql2 = "SELECT DISTINCT SteamID, Elo, qg.MatchModeID, Region, qg.MatchTypeID, qg.GroupID
										FROM QueueGroup qg JOIN QueueGroupMembers qgm
										ON qg.GroupID = qgm.GroupID AND
										qg.MatchTypeID = qgm.MatchTypeID AND
										qg.MatchModeID = qgm.MatchModeID
										WHERE (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
												AND Region = " . ( int ) $region . "
														" . $forceSQL . ")
																AND SteamID != " . secureNumber ( $steamID ) . ")
																		AND qg.MatchModeID =  " . ( int ) $modus . "
																				AND qg.MatchTypeID = 1
																				ORDER BY Timestamp ASC
																				LIMIT " . $limit . "
																						";
								$ret ['debug'] .= p ( $sql2, 1 );
								$data2 = $DB->multiSelect ( $sql2 );

								$count = $count + count ( $data2 );
							}

							$count = $count + 1;
							if ($count == 0) {
								// $count = 1; // workAround für division by zero für wideRange udn so
							}

							$countArray [] = $count;

							// matchDetails
							$ret ['matchDetails'] .= "<p>" . date ( 'H:i:s' ) . " - " . "Searching in MatchMode: " . $modus . " witch following ranges: " . $untere_grenze . " - " . $obere_grenze . " Players found: " . $count . "</p>";

							if ($count == 10) {
								if (is_array ( $dataPlayer ) && count ( $dataPlayer ) > 0) {
									$data [] = $dataPlayer;
								} else {
									// sich selber noch ins
									$data [] = $dataPlayer2;
								}

								$ret ['foundPlayersSQL'] [$modus] = $sql . $sql2;
								$ret ['foundPlayersData'] [$modus] = array_merge ( $data, $data2 );
								$ret ['debug'] .= p ( array_merge ( $data, $data2 ), 1 );
							} else {
							}
						}
						// herausbekommen was der größte Wert zurzeit ist
						$max = max ( $countArray );

						$_SESSION ['queue'] [$modus] = $max;
					}

					// $sql = "SELECT DISTINCT SteamID
					// FROM `Queue`
					// WHERE SteamID = ".secureNumber($steamID)." AND MatchModeID = ".(int)$modus."
					// ";
					// $countPlayerInMatchMode = $DB->countRows($sql);
					// $ret['debug'] .= p($sql,1);

					// if($countPlayerInMatchMode > 0){
					// // Wenn force Search, dann auch nur force Search Leute suchen
					// if($forceSearch == "true"){
					// $rangeUnten = (int) $_SESSION['elo'][$modus] - Matchmaking::maxEloRange;
					// $rangeOben = (int) $_SESSION['elo'][$modus] + Matchmaking::maxEloRange;
					// $forceSQL = " AND ForceSearch = 1";

					// }
					// else{
					// $forceSQL = "AND ForceSearch = 0";
					// }

					// $untere_grenze = (int)$rangeArray[$modus]['untere_grenze'];
					// $obere_grenze = (int)$rangeArray[$modus]['obere_grenze'];

					// $ret['debug'] .= "MODUS: ".$modus." \n\n";

					// $ret['debug'] .= " UG:".$untere_grenze." OG:".$obere_grenze." \n\n";

					// $where = "WHERE (((Elo >= ".$untere_grenze." AND Elo <= ".$obere_grenze.") ".$regionsSQL." ".$forceSQL.") AND SteamID != ".$steamID.")
					// AND (MatchModeID = ".(int)$modus." AND MatchTypeID = 1)";
					// $sql = "SELECT DISTINCT SteamID, Elo
					// FROM Queue
					// ".$where."
					// ORDER BY Timestamp ASC
					// LIMIT 9
					// ";
					// $ret['debug'] .= $sql." \n\n";
					// $count = $DB->countRows($sql);
					// $count = $count+1;
					// if($count == 0){
					// $count = 1; // workAround für division by zero für wideRange udn so
					// }

					// $_SESSION['queue'][$modus] = $count;

					// // matchDetails
					// $ret['matchDetails'] .= "<p>".date('H:i:s')." - "."Searching in MatchMode: ".$modus." witch following ranges: ".$untere_grenze." - ".$obere_grenze." Players found: ".$count."</p>";

					// if($count == 10){

					// $ret['foundPlayersSQL'][$modus] = $sql;
					// }
					// }
				}
			}
		}
		$ret ['debug'] .= "- END getPlayerCountsOfQueueForPlayerSingleRange - \n\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getPlayerCountsOfQueueForPlayerSingleRange2($steamID, $spielmodi, $rangeArray, $regions, $forceSearch, $matchTypeID = 1) {
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "- Start getPlayerCountsOfQueueForPlayerSingleRange - \n\n";
		$ret ['foundPlayersSQL'] = array ();
		$ret ['foundPlayersData'] = array ();

		// $_SESSION['erweiterteSuche'] = array("6");
		// $ret .= "rangeArray: ".print_r($rangeArray,1)." \n\n";
		if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
			if (is_array ( $regions ) && count ( $regions ) > 0) {
				// region SQL Statement generieren lassen
				$regionsSQL = $this->getSQLForRegion ( $regions );
				$ret ['debug'] .= print_r ( $regionsSQL, 1 );
				$regionsSQL = $regionsSQL ['regionSQL'];

				foreach ( $spielmodi as $k => $modus ) {
					$countArray = array ();

					foreach ( $regions as $k => $region ) {

						$sql = "SELECT DISTINCT SteamID, Elo, MatchModeID, Region, MatchTypeID
								FROM `Queue`
								WHERE SteamID = " . secureNumber ( $steamID ) . " AND MatchTypeID = " . ( int ) $matchTypeID . " AND MatchModeID = " . ( int ) $modus . " AND Region = " . ( int ) $region . "
										";
						$ret ['debug'] .= p ( $sql, 1 );
						$dataPlayer = $DB->select ( $sql );
						$countPlayerInMatchMode = count ( $dataPlayer );
						// grkcen ob in einer Gruppe drin ist
						$sql = "SELECT DISTINCT qgm.SteamID, Elo, qgm.MatchModeID, Region, qgm.MatchTypeID, qgm.GroupID
								FROM QueueGroup qg JOIN QueueGroupMembers qgm ON qg.GroupID = qgm.groupID
								WHERE (qgm.SteamID = " . secureNumber ( $steamID ) . " AND qgm.MatchTypeID = " . ( int ) $matchTypeID . " AND qgm.MatchModeID = " . ( int ) $modus . " AND qg.Region = " . ( int ) $region . ")
										";
						$ret ['debug'] .= p ( $sql, 1 );
						$dataPlayer2 = $DB->select ( $sql );
						$countPlayerInMatchMode2 = count ( $dataPlayer2 );
						$ret ['debug'] .= "================ TEST SOLO MENSCH:" . p ( $dataPlayer, 1 ) . " COUNT:" . $countPlayerInMatchMode;
						$ret ['debug'] .= "================ TEST GRUPPEN MENSCH:" . p ( $dataPlayer2, 1 ) . " COUNT:" . $countPlayerInMatchMode2; // Solo Mensch
						if ($countPlayerInMatchMode > 2) {
							$ret ['debug'] .= "ICH BIN EIN SOLO MENSCH \n\n";
							$data = array ();
							// Wenn force Search, dann auch nur force Search Leute suchen
							if ($forceSearch == "true") {
								$rangeUnten = ( int ) $_SESSION ['elo'] [$modus] - Matchmaking::maxEloRange;
								$rangeOben = ( int ) $_SESSION ['elo'] [$modus] + Matchmaking::maxEloRange;
								$forceSQL = " AND ForceSearch = 1";
							} else {
								$forceSQL = "AND ForceSearch = 0";
							}

							$untere_grenze = ( int ) $rangeArray [$modus] ['untere_grenze'];
							$obere_grenze = ( int ) $rangeArray [$modus] ['obere_grenze'];

							$ret ['debug'] .= "MODUS: " . $modus . " \n\n";

							$ret ['debug'] .= " UG:" . $untere_grenze . " OG:" . $obere_grenze . " \n\n";

							$count = 0;

							$sql = "SELECT DISTINCT SteamID, Elo, MatchModeID, Region, MatchTypeID
									FROM Queue
									WHERE (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
											AND
											Region = " . ( int ) $region . "
													" . $forceSQL . " ))
															AND MatchModeID = " . ( int ) $modus . "
																	AND MatchTypeID = " . ( int ) $matchTypeID . "
																			ORDER BY Timestamp ASC
																			LIMIT 10
																			";
							// $sql = "SELECT DISTINCT SteamID, Elo, MatchModeID, Region, MatchTypeID
							// FROM Queue
							// WHERE
							// Region = ".(int)$region."
							// ".$forceSQL."
							// AND MatchModeID = ".(int) $modus."
							// AND MatchTypeID = ".(int) $matchTypeID."
							// ORDER BY Timestamp ASC
							// LIMIT 10
							// ";
							$data = $DB->multiSelect ( $sql );
							$ret ['debug'] .= p ( $sql, 1 );

							$count = count ( $data );

							switch ($count) {
								case 0 :
									$limit = 8;
									break;
								case 1 :
									$limit = 8;
									break;
								case 2 :
									$limit = 6;
									break;
								case 3 :
									$limit = 6;
									break;
								case 4 :
									$limit = 4;
									break;
								case 5 :
									$limit = 4;
									break;
								case 6 :
									$limit = 2;
									break;
								case 7 :
									$limit = 2;
									break;
								case 8 :
									$limit = 2;
									break;
								case 9 :
									$limit = 0;
									break;
								case 10 :
									$limit = 0;
									break;
							}
							$data2 = array ();
							if ($limit > 0) {
								// Nach Gruppen suchen
								$sql2 = "SELECT DISTINCT SteamID, Elo, qg.MatchModeID, Region, qg.MatchTypeID, qg.GroupID
										FROM QueueGroup qg JOIN QueueGroupMembers qgm
										ON qg.GroupID = qgm.GroupID AND
										qg.MatchTypeID = qgm.MatchTypeID AND
										qg.MatchModeID = qgm.MatchModeID
										WHERE
										-- (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
												-- AND
												Region = " . ( int ) $region . "
														" . $forceSQL . " -- ))
																AND qg.MatchModeID =  " . ( int ) $modus . "
																		AND qg.MatchTypeID = " . ( int ) $matchTypeID . "
																				ORDER BY Timestamp ASC
																				LIMIT " . $limit . "
																						";
								// $sql2 = "SELECT DISTINCT SteamID, Elo, qg.MatchModeID, Region, qg.MatchTypeID, qg.GroupID
								// FROM QueueGroup qg JOIN QueueGroupMembers qgm
								// ON qg.GroupID = qgm.GroupID AND
								// qg.MatchTypeID = qgm.MatchTypeID AND
								// qg.MatchModeID = qgm.MatchModeID
								// WHERE
								// Region = ".(int)$region."
								// ".$forceSQL."
								// AND qg.MatchModeID = ".(int) $modus."
								// AND qg.MatchTypeID = ".(int) $matchTypeID."
								// ORDER BY Timestamp ASC
								// LIMIT ".$limit."
								// ";
								$ret ['debug'] .= p ( $sql2, 1 );
								$data2 = $DB->multiSelect ( $sql2 );

								$count = $count + count ( $data2 );
							}

							if ($count == 0) {
								// $count = 1; // workAround für division by zero für wideRange udn so
							}

							$countArray [] = $count;

							// matchDetails
							$ret ['matchDetails'] .= "<p>" . date ( 'H:i:s' ) . " - " . "Searching in MatchMode: " . $modus . " witch following ranges: " . $untere_grenze . " - " . $obere_grenze . " Players found: " . $count . "</p>";
						}

						// Ich bin Gruppenmensch
						if ($countPlayerInMatchMode2 > 2) {
							$ret ['debug'] .= "ICH BIN EIN GRUPPENMENSCH \n\n";
							$data = array ();
							// Wenn force Search, dann auch nur force Search Leute suchen
							if ($forceSearch == "true") {
								$rangeUnten = ( int ) $_SESSION ['elo'] [$modus] - Matchmaking::maxEloRange;
								$rangeOben = ( int ) $_SESSION ['elo'] [$modus] + Matchmaking::maxEloRange;
								$forceSQL = " AND ForceSearch = 1";
							} else {
								$forceSQL = "AND ForceSearch = 0";
							}

							$untere_grenze = ( int ) $rangeArray [$modus] ['untere_grenze'];
							$obere_grenze = ( int ) $rangeArray [$modus] ['obere_grenze'];

							$ret ['debug'] .= "MODUS: " . $modus . " \n\n";

							$ret ['debug'] .= " UG:" . $untere_grenze . " OG:" . $obere_grenze . " \n\n";

							$count = 0;

							$sql = "SELECT DISTINCT SteamID, Elo, MatchModeID, Region, MatchTypeID
									FROM Queue
									WHERE (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
											AND Region = " . ( int ) $region . "
													" . $forceSQL . "))
															AND MatchModeID = " . ( int ) $modus . "
																	AND MatchTypeID = " . ( int ) $matchTypeID . "
																			ORDER BY Timestamp ASC
																			LIMIT 8
																			";
							$data = $DB->multiSelect ( $sql );
							$ret ['debug'] .= p ( $sql, 1 );

							$count = count ( $data );

							switch ($count) {
								case 0 :
									$limit = 8;
									break;
								case 1 :
									$limit = 8;
									break;
								case 2 :
									$limit = 8;
									break;
								case 3 :
									$limit = 6;
									break;
								case 4 :
									$limit = 6;
									break;
								case 5 :
									$limit = 4;
									break;
								case 6 :
									$limit = 4;
									break;
								case 7 :
									$limit = 2;
									break;
								case 8 :
									$limit = 2;
									break;
							}
							$data2 = array ();
							if ($limit > 0) {
								// Nach Gruppen suchen
								$sql2 = "SELECT DISTINCT SteamID, Elo, qg.MatchModeID, Region, qg.MatchTypeID, qg.GroupID
										FROM QueueGroup qg JOIN QueueGroupMembers qgm
										ON qg.GroupID = qgm.GroupID AND
										qg.MatchTypeID = qgm.MatchTypeID AND
										qg.MatchModeID = qgm.MatchModeID
										WHERE (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
												AND Region = " . ( int ) $region . "
														" . $forceSQL . "))
																AND qg.MatchModeID =  " . ( int ) $modus . "
																		AND qg.MatchTypeID = " . ( int ) $matchTypeID . "
																				ORDER BY Timestamp ASC
																				LIMIT " . $limit . "
																						";
								$ret ['debug'] .= p ( $sql2, 1 );
								$data2 = $DB->multiSelect ( $sql2 );

								$count = $count + count ( $data2 );
							}

							if ($count == 0) {
								// $count = 1; // workAround für division by zero für wideRange udn so
							}

							$countArray [] = $count;

							// matchDetails
							$ret ['matchDetails'] .= "<p>" . date ( 'H:i:s' ) . " - " . "Searching in MatchMode: " . $modus . " witch following ranges: " . $untere_grenze . " - " . $obere_grenze . " Players found: " . $count . "</p>";
						}
						$ret ['debug'] .= "+++++++++++++++++ COUNT:" . p ( $count, 1 );
						// kontrolle ob ich in den 10 drinne bin
						// wenn ja dann gefunden else nicht
						$inData = "";
						if (is_array ( $data ) && count ( $data ) > 0) {
							$inData = recursive_array_search ( $steamID, $data );
						}
						$inData2 = "";
						if (is_array ( $data2 ) && count ( $data2 ) > 0) {
							$inData2 = recursive_array_search ( $steamID, $data2 );
						}

						if ($count == 10) {
							$ret ['debug'] .= "#########++++++++++++++++######## TEST recusive Suche nach MIR: ID1:" . p ( $inData, 1 ) . " ID2:" . p ( $inData2, 1 );

							if (is_numeric ( $inData ) or is_numeric ( $inData2 )) {
								$ret ['foundPlayersSQL'] [$modus] = $sql . $sql2;
								$ret ['foundPlayersData'] [$modus] = array_merge ( $data, $data2 );
								$ret ['debug'] .= p ( array_merge ( $data, $data2 ), 1 );
							} else {
								$ret ['foundPlayersSQL'] [$modus] = "";
								$ret ['foundPlayersData'] [$modus] = array ();
								$ret ['debug'] .= "war nicht in den 10 drinne";
							}
						} else {
						}
						// herausbekommen was der größte Wert zurzeit ist
						if (is_array ( $countArray ) && count ( $countArray ) > 0) {
							$max = max ( $countArray );
							if (is_numeric ( $inData ) or is_numeric ( $inData2 )) {
								$_SESSION ['queue'] [$modus] = $max;
							} else {
								$_SESSION ['queue'] [$modus] = ($max - 1);
							}
						} else {
							$max = 1;
							if (is_numeric ( $inData ) or is_numeric ( $inData2 )) {
								$_SESSION ['queue'] [$modus] = $max;
							} else {
								$_SESSION ['queue'] [$modus] = ($max - 1);
							}
						}
					}
				}
			}
		}
		$ret ['debug'] .= "- END getPlayerCountsOfQueueForPlayerSingleRange - \n\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function wideRangeOfSearch($spielmodi, $forceSearch) {
		$ret = "- Start wideRangeOfSearch - \n\n";

		if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
			foreach ( $spielmodi as $k => $modus ) {
				// p($_SESSION['queue']);
				if ($_SESSION ['queue'] [$modus] > 0) {
					$delta = ( int ) round ( Matchmaking::eloChangePerIteration / ($_SESSION ['queue'] [$modus] / 2), 0 ); // �nderungswert
					$ret .= "handle Forch Search \n";
					$ret .= $this->handleForceSearch ( $modus, $delta, $forceSearch );
					$ret .= "END handle Forch Search \n";
					$ret .= "UG_S:" . $_SESSION ['range'] [$modus] ['untere_grenze'] . " OG_S:" . $_SESSION ['range'] [$modus] ['obere_grenze'] . " \n\n";
				}

				// if($_SESSION['range'][$modus]['obere_grenze'] - $_SESSION['elo'][$modus] >= Matchmaking::maxEloUnterschied){
				// $ret .= "\n maxEloUnterschied überschritten \n";
				// if($_SESSION['erweiterteSuche'] == null){
				// $ret .= "\n erweiterteSuche das erste mal \n";
				// $_SESSION['erweiterteSuche'] = array();
				// array_push($_SESSION['erweiterteSuche'], $modus);
				// // range zurücksetzen
				// $_SESSION['range'][$modus]['untere_grenze'] = $_SESSION['elo'][$modus];
				// $_SESSION['range'][$modus]['obere_grenze'] = $_SESSION['elo'][$modus];
				// }
				// else{
				// if(!in_array($modus, $_SESSION['erweiterteSuche'])){

				// array_push($_SESSION['erweiterteSuche'], $modus);
				// $ret .= "\nERWEITERTE SUCHE ".print_r($_SESSION['erweiterteSuche'],1)."\n\n\n";
				// // range zurücksetzen
				// $_SESSION['range'][$modus]['untere_grenze'] = $_SESSION['elo'][$modus];
				// $_SESSION['range'][$modus]['obere_grenze'] = $_SESSION['elo'][$modus];
				// }
				// }
				// }
			}
		}
		$ret .= "- END wideRangeOfSearch - \n\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getPlayerCountsOfQueueForPlayerSingleRange3($steamID, $spielmodi, $rangeArray, $regions, $silverOrHigher, $forceSearch, $matchTypeID = 1) {
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "- Start getPlayerCountsOfQueueForPlayerSingleRange3 - \n\n";
		$ret ['foundPlayersSQL'] = array ();
		$ret ['foundPlayersData'] = array ();

		// $_SESSION['erweiterteSuche'] = array("6");
		// $ret .= "rangeArray: ".print_r($rangeArray,1)." \n\n";
		if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
			if (is_array ( $regions ) && count ( $regions ) > 0) {
				// region SQL Statement generieren lassen
				$regionsSQL = $this->getSQLForRegion ( $regions );
				$ret ['debug'] .= print_r ( $regionsSQL, 1 );
				$regionsSQL = $regionsSQL ['regionSQL'];

				foreach ( $spielmodi as $k => $modus ) {
					$countArray = array ();
					$posiArray = array ();

					foreach ( $regions as $k => $region ) {

						$data = array ();
						// Wenn force Search, dann auch nur force Search Leute suchen
						if ($forceSearch == "true") {
							$rangeUnten = ( int ) $_SESSION ['elo'] [$modus] - Matchmaking::maxEloRange;
							$rangeOben = ( int ) $_SESSION ['elo'] [$modus] + Matchmaking::maxEloRange;
							$forceSQL = " AND ForceSearch = 1";
						} else {
							$forceSQL = "AND ForceSearch = 0";
						}

						$untere_grenze = ( int ) $rangeArray [$modus] ['untere_grenze'];
						$obere_grenze = ( int ) $rangeArray [$modus] ['obere_grenze'];

						$ret ['debug'] .= "MODUS: " . $modus . " \n\n";

						$ret ['debug'] .= " UG:" . $untere_grenze . " OG:" . $obere_grenze . " \n\n";

						$count = 0;
						if (POINTSMATCHMAKING) {
							$sql = "SELECT DISTINCT SteamID, Elo, MatchModeID, Region, MatchTypeID, Timestamp
									FROM Queue
									WHERE (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
											AND
											Region = " . ( int ) $region . "
													" . $forceSQL . " ))
															AND MatchModeID = " . ( int ) $modus . "
																	AND MatchTypeID = " . ( int ) $matchTypeID . "
																			ORDER BY Timestamp ASC
																			LIMIT 10
																			";
						} else {
							$ret ['debug'] .= "FORCESEARCH:" . $forceSearch;
							if ($forceSearch == "false") {
								if ($silverOrHigher) {
									$sql = "SELECT DISTINCT q.SteamID, Elo, MatchModeID, Region, MatchTypeID, Timestamp
											FROM Queue q LEFT JOIN UserLeague ul ON ul.SteamID = q.SteamID
											WHERE
											Region = " . ( int ) $region . "
													" . $forceSQL . "
															AND MatchModeID = " . ( int ) $modus . "
																	AND MatchTypeID = " . ( int ) $matchTypeID . "
																			AND ul.LeagueTypeID >= 3
																			ORDER BY Timestamp ASC
																			";
								} else {
									$sql = "SELECT DISTINCT q.SteamID, Elo, MatchModeID, Region, MatchTypeID, Timestamp
											FROM Queue q LEFT JOIN UserLeague ul ON ul.SteamID = q.SteamID
											WHERE
											Region = " . ( int ) $region . "
													" . $forceSQL . "
															AND MatchModeID = " . ( int ) $modus . "
																	AND MatchTypeID = " . ( int ) $matchTypeID . "
																			AND ul.LeagueTypeID < 3
																			ORDER BY Timestamp ASC
																			";
								}
							} else {
								$sql = "SELECT DISTINCT q.SteamID, Elo, MatchModeID, Region, MatchTypeID, Timestamp
										FROM Queue q LEFT JOIN UserLeague ul ON ul.SteamID = q.SteamID
										WHERE
										Region = " . ( int ) $region . "
												" . $forceSQL . "
														AND MatchModeID = " . ( int ) $modus . "
																AND MatchTypeID = " . ( int ) $matchTypeID . "

																		ORDER BY Timestamp ASC
																		";
							}
						}

						$data = $DB->multiSelect ( $sql );
						$ret ['debug'] .= p ( $sql, 1 );

						$count = count ( $data );

						if (POINTSMATCHMAKING) {
							// Nach Gruppen suchen
							$sql2 = "SELECT DISTINCT SteamID, Elo, qg.MatchModeID, Region, qg.MatchTypeID, qg.GroupID, Timestamp
									FROM QueueGroup qg JOIN QueueGroupMembers qgm
									ON qg.GroupID = qgm.GroupID AND
									qg.MatchTypeID = qgm.MatchTypeID AND
									qg.MatchModeID = qgm.MatchModeID
									WHERE
									-- (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
											-- AND
											Region = " . ( int ) $region . "
													" . $forceSQL . " -- ))
															AND qg.MatchModeID =  " . ( int ) $modus . "
																	AND qg.MatchTypeID = " . ( int ) $matchTypeID . "
																			ORDER BY Timestamp ASC
																			LIMIT " . $limit . "
																					";
						} else {
							if ($silverOrHigher) {
								$sql2 = "SELECT DISTINCT qgm.SteamID, Elo, qg.MatchModeID, Region, qg.MatchTypeID, qg.GroupID, Timestamp
										FROM QueueGroup qg JOIN QueueGroupMembers qgm
										ON qg.GroupID = qgm.GroupID AND
										qg.MatchTypeID = qgm.MatchTypeID AND
										qg.MatchModeID = qgm.MatchModeID
										LEFT JOIN UserLeague ul ON qgm.SteamID = ul.SteamID
										WHERE
										Region = " . ( int ) $region . "
												" . $forceSQL . "
														AND qg.MatchModeID =  " . ( int ) $modus . "
																AND qg.MatchTypeID = " . ( int ) $matchTypeID . "
																	 AND ul.LeagueTypeID >= 3
																		ORDER BY Timestamp ASC
																		";
							} else {
								$sql2 = "SELECT DISTINCT qgm.SteamID, Elo, qg.MatchModeID, Region, qg.MatchTypeID, qg.GroupID, Timestamp
										FROM QueueGroup qg JOIN QueueGroupMembers qgm
										ON qg.GroupID = qgm.GroupID AND
										qg.MatchTypeID = qgm.MatchTypeID AND
										qg.MatchModeID = qgm.MatchModeID
										LEFT JOIN UserLeague ul ON qgm.SteamID = ul.SteamID
										WHERE
										Region = " . ( int ) $region . "
												" . $forceSQL . "
														AND qg.MatchModeID =  " . ( int ) $modus . "
																AND qg.MatchTypeID = " . ( int ) $matchTypeID . "
																	 AND ul.LeagueTypeID < 3
																		ORDER BY Timestamp ASC
																		";
							}
						}

						$ret ['debug'] .= p ( $sql2, 1 );
						$data2 = $DB->multiSelect ( $sql2 );

						$count = $count + count ( $data2 );

						// arrays Mergen
						$globalArray = array ();
						if ((is_array ( $data2 ) && count ( $data2 ) > 0) && (is_array ( $data ) && count ( $data ) > 0)) {
							$globalArray = array_merge ( $data, $data2 );
						} else if (is_array ( $data ) && count ( $data ) > 0) {
							$globalArray = $data;
						} else if (is_array ( $data2 ) && count ( $data2 ) > 0) {
							$globalArray = $data2;
						}

						$ret ['debug'] .= "+++++++++++++++++ GLOBAL ARRAY:" . p ( $globalArray, 1 );

						$posi = "";
						if (is_array ( $globalArray ) && count ( $globalArray ) > 0) {
							// nach Timstamp sortieren und position hausfinden
							$globalArray = orderArrayBy ( $globalArray, 'Timestamp', SORT_ASC );
							$ret ['debug'] .= "+++++++++++++++++ GLOBAL ARRAY:" . p ( $globalArray, 1 );
							$posi = recursive_array_search ( $steamID, $globalArray );
							$posiArray [] = ( int ) $posi + 1;
						}

						$countArray [] = $count;

						// matchDetails
						$ret ['matchDetails'] .= "<p>" . date ( 'H:i:s' ) . " - " . "Searching in MatchMode: " . $modus . " witch following ranges: " . $untere_grenze . " - " . $obere_grenze . " Players found: " . $count . "</p>";
					} // END REGION FOREACH

					$ret ['debug'] .= "+++++++++++++++++ COUNT:" . p ( $countArray, 1 );
					$ret ['debug'] .= "+++++++++++++++++ PosiArray:" . p ( $posiArray, 1 );

					// herausbekommen was der größte Wert zurzeit ist
					if (is_array ( $countArray ) && count ( $countArray ) > 0 && is_array ( $posiArray ) && count ( $posiArray ) > 0) {
						$max = max ( $countArray );
						$maxPosi = max ( $posiArray );
						$ret ['debug'] .= p ( "WIRD IN SESSION EINGETRAGEN: :" . "C:" . $max . " P:" . $maxPosi, 1 );

						$_SESSION ['queue'] [$modus] ['count'] = ( int ) $max;
						$_SESSION ['queue'] [$modus] ['position'] = ( int ) $maxPosi;
					} else {
						$ret ['debug'] .= p ( "irgend ein Array ist NULL", 1 );
						$_SESSION ['queue'] [$modus] ['count'] = ( int ) 1;
						$_SESSION ['queue'] [$modus] ['position'] = ( int ) 1;
					}
				} // END MODUS FOREACH
			}
		}
		$ret ['debug'] .= p ( $_SESSION, 1 );
		$ret ['debug'] .= "- END getPlayerCountsOfQueueForPlayerSingleRange3 - \n\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getPlayerCountsOfQueueForPlayerMultipleModi($steamID, $spielmodi, $rangeArray, $regions) {
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] = "- START getPlayerCountsOfQueueForPlayerMultipleModi - \n\n\n";
		$ret ['foundPlayersSQL'] = array ();
		if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
			$where = "";
			$regionsSQL = $this->getSQLForRegion ( $regions );
			$ret ['debug'] .= print_r ( $regionsSQL, 1 );
			$regionsSQL = $regionsSQL ['regionSQL'];

			$matchDetailsMatchModi = "";
			foreach ( $spielmodi as $k => $modus ) {
				if ($where != "") {
					$where .= " OR ";
				}
				$untere_grenze = ( int ) $rangeArray [$modus] ['untere_grenze'];
				$obere_grenze = ( int ) $rangeArray [$modus] ['obere_grenze'];

				$ret ['debug'] .= "UG:" . $untere_grenze . " OG:" . $obere_grenze . " \n\n";

				$where .= "MatchModeID = " . ( int ) $modus;

				$matchDetailsMatchModi .= $modus . " ";
			}
			$where = "(" . $where . ")";
			$where .= " AND (Elo >= " . $untere_grenze . " AND Elo <= " . $obere_grenze . ")";
			$finalWhere = "WHERE (MatchTypeID = " . ( int ) $matchTypeID . " AND " . $where . " " . $regionsSQL . ") AND SteamID != " . secureNumber ( $steamID ) . "";
			$sql = "SELECT DISTINCT SteamID, Elo
					FROM Queue
					" . $finalWhere . "
							ORDER BY Timestamp ASC
							LIMIT 9
							";

			$count = $DB->countRows ( $sql );
			$ret ['debug'] .= "COUNT: " . $count . " SQL: " . $sql . " \n\n";
			$ret ['matchDetails'] .= "<p>" . date ( 'H:i:s' ) . " - " . "Searching in multiple MatchModes: " . $matchDetailsMatchModi . " with ranges: " . $untere_grenze . " - " . $obere_grenze . " Players found: " . $count . "</p>";
			$foundPlayers = array ();
			foreach ( $spielmodi as $k => $modus ) {

				$_SESSION ['queue'] [$modus] = $count;

				if ($count == 9) {
					$foundPlayers [$modus] = $sql;
				}
			}
			$ret ['foundPlayersSQL'] = $foundPlayers;
		}
		$ret ['debug'] .= "- END getPlayerCountsOfQueueForPlayerMultipleModi - \n\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function getSQLForRegion($regions) {
		$ret = array ();
		$sql = "";

		if (is_array ( $regions ) && count ( $regions ) > 0) {

			foreach ( $regions as $k => $region ) {
				if ($sql != "") {
					$sql .= " OR ";
				}
				$sql .= "Region = " . ( int ) $region;
			}

			$sql = " AND (" . $sql . ")";
		}

		$ret ['regionSQL'] = $sql;

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function handleFoundPlayersSingleQueue($steamID, $retSingle, $retMultiple, $retData) {
		$DB = new DB ();
		$Queue = new Queue ();
		$con = $DB->conDB ();
		$ret = array ();
		$MatchTeams = new MatchTeams ();
		$SingleQueue = new SingleQueue ();
		// checken ob bereits eingetragen

		$checkAlreadyIn = $MatchTeams->checkIfPlayerAlreadyInMatchTeams ( $steamID );
		$ret ['debug'] .= $checkAlreadyIn ['debug'];

		// Random Sql-Anweisung raussuchen, da mehrere gleichzeitig gefunden werden k�nnten
		// $mergedArray = array();
		$ret ['debug'] .= "########## retSingle: " . print_r ( $retSingle, 1 ) . "\n\n ";

		if (! $checkAlreadyIn ['status']) {

			if (is_array ( $retSingle )) {
				$randSQLKey = array_rand ( $retSingle, 1 );
				$randSQL = $retSingle [$randSQLKey];
				$randData = $retData [$randSQLKey];
				shuffle ( $randData );
				// sortieren nach Elo absteigend
				$randData = orderArrayBy ( $randData, 'Elo', SORT_DESC );
			}
			$ret ['debug'] .= "randSQL: " . $randSQL;
			$ret ['debug'] .= "randSQLDATA: " . p ( $randData, 1 ) . " ###ENDEEEE##";

			// wenn nicht bereits eingetragen, neues Match und MatchTeams eintragenF
			$retMatchTeams = $MatchTeams->insertPlayersIntoMatchTeamsAfterFoundMatchmaking ( $randSQL, $randData );
			$ret ['debug'] .= $retMatchTeams ['debug'];

			$ret ['matchID'] = $retMatchTeams ['matchID'];
			$ret ['status'] = true;
		} else {

			// Anscheinend doch einer schneller gewesen
			$log = new KLogger ( "log.txt", KLogger::INFO );
			$log->LogInfo ( "Player already in MatchTeams:" . $steamID . " MatchID raussuchen..." ); // Prints to the log file

			$sql = "SELECT DISTINCT m.MatchID
					FROM `Match` m JOIN MatchDetails md ON m.MatchID = md.MatchID
					WHERE TeamWonID = -1 AND TimestampClosed = 0 AND Canceled = 0 AND ManuallyCheck = 0
					AND md.SteamID = " . secureNumber ( $steamID ) . " -- AND md.Submitted = 0 AND md.SubmissionFor = 0 AND md.SubmissionTimestamp = 0
							-- AND NOT EXISTS(SELECT mdcmv.SteamID FROM MatchDetailsCancelMatchVotes mdcmv
							-- WHERE mdcmv.SteamID = " . secureNumber ( $steamID ) . " AND mdcmv.MatchID = m.MatchID)
									ORDER BY m.MatchID DESC
									LIMIT 1
									";
			$retMatchID = $DB->select ( $sql );
			$matchID = ( int ) $retMatchID ['MatchID'];
			if ($matchID > 0) {
				$ret ['status'] = true;
				$ret ['matchID'] = $matchID;
				$log->LogInfo ( "Match gesucht und gefunden:" . $steamID . " MatchID:" . $matchID ); // Prints to the log file
			} else {
				$ret ['status'] = false;
				$log->LogInfo ( "Match gesucht aber gescheitert:" . $steamID . "in MatchDetails/Match kein Datensatz drin" ); // Prints to the log file
			}
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function handleFoundPlayersSingleQueue2($retData) {
		$DB = new DB ();
		$Queue = new Queue ();
		$con = $DB->conDB ();
		$ret = array ();
		$MatchTeams = new MatchTeams ();

		// wenn nicht bereits eingetragen, neues Match und MatchTeams eintragenF
		$retMatchTeams = $MatchTeams->insertPlayersIntoMatchTeamsAfterFoundMatchmaking ( $randSQL, $randData );
		$ret ['debug'] .= $retMatchTeams ['debug'];

		$ret ['matchID'] = $retMatchTeams ['matchID'];
		$ret ['status'] = true;

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function handleForceSearch($modus, $delta, $forceSearch) {
		$ret = "";
		// $ret .= "Modus: ".$modus."Force:".$forceSearch." delta:".$delta." shcranke:".($_SESSION['range'][$modus]['obere_grenze']+$delta - $_SESSION['elo'][$modus])." max:".Matchmaking::maxEloRange."<br>";
		// pr�fen ob weitere
		$wideRange = false;
		$range = ( int ) (($_SESSION ['range'] [$modus] ['obere_grenze'] + $delta) - $_SESSION ['elo'] [$modus]);
		$ret .= "RANGE: " . $range . " \n";
		$ret .= "Force: " . $forceSearch . " \n";

		if ($range <= Matchmaking::maxEloRange) {
			$wideRange = true;
			$ret .= "<p>###### elo unter maxRange #######</p>";
		} else {
			if ($forceSearch == "true") {
				$wideRange = true;
				$ret .= "<p>###### elo ueber maxRange und FORCE #######</p>";
			} else {
				$wideRange = false;
				$_SESSION ['range'] [$modus] ['untere_grenze'] = $_SESSION ['elo'] [$modus] - Matchmaking::maxEloRange;
				$_SESSION ['range'] [$modus] ['obere_grenze'] = $_SESSION ['elo'] [$modus] + Matchmaking::maxEloRange;
				$ret .= "<p>###### elo ueber maxRange und nicht force #######</p>";
			}
		}

		if ($wideRange) {
			$ret .= "<p>###### range erhoehen/verniedrigen #######</p>";
			// untere Grenze nicht unter 0 fallen lassen
			if ($_SESSION ['range'] [$modus] ['untere_grenze'] - $delta > 0) {
				$_SESSION ['range'] [$modus] ['untere_grenze'] -= $delta;
			} else {
				$_SESSION ['range'] [$modus] ['untere_grenze'] = 0;
			}
			$_SESSION ['range'] [$modus] ['obere_grenze'] += $delta;
			$_SESSION ['range'] [$modus] ['maxSearchRange'] = false;
		} else {
			$_SESSION ['range'] [$modus] ['maxSearchRange'] = true;
			$ret .= "+++++ ALLES BLEIBT SO WIE ES IST! :P +++\n\n";
		}

		// $ret .= "<p>###### Fertig #######</p>";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function checkDurationInQueue($steamID) {
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function seperate10PlayersInto2BalancedTeams($data) {
		$ret = array ();
		$ret ['debug'] .= "Start seperate10PLayersInto2BalancedTeams <br>\n";
		if (is_array ( $data ) && count ( $data ) > 0) {
			$sortedArray = orderArrayBy ( $data, 'Elo', SORT_DESC );
			// p($sortedArray);
			$team = array ();
			$i = 0;
			while ( count ( $sortedArray ) !== 0 ) {

				// oberesten und untersten nehmen und in die gruppe packen
				$firstElem = first ( $sortedArray );
				$firstElemKey = array_search ( $firstElem, $sortedArray );
				$lastElem = last ( $sortedArray );
				$lastElemKey = array_search ( $lastElem, $sortedArray );

				// p($firstElemKey);
				// p($lastElemKey);

				// wenn eine runde rum dann erneut durchschnitte bereichnen
				if ($i % 2 === 0) {
					$ret ['debug'] .= p ( "modulo = 0", 1 );
					// in gruppe packen und aus array entfernen
					$minAveTeamNumberRet = getMinAveEloOfTeam ( $team [1], $team [2] );
					$ret ['debug'] .= p ( $minAveTeamNumberRet, 1 );
					$minAveTeamNumber = $minAveTeamNumberRet ['data'];

					// wenn es die letzten 2 sind
					if (count ( $sortedArray ) == 2 && count ( $data ) == 10) {
						$ret ['debug'] .= p ( "nur noch 2", 1 );

						$team [$minAveTeamNumber] [] = $firstElem;
						if ($minAveTeamNumber === 1) {
							$team [2] [] = $lastElem;
							$ret ['debug'] .= p ( "put into: 2", 1 );
						} else {
							$team [1] [] = $lastElem;
							$ret ['debug'] .= p ( "put into: 1", 1 );
						}
					} 					// sonst beide in selbe gruppe packen
					else {
						$ret ['debug'] .= p ( "put into: " . $minAveTeamNumber, 1 );
						// in gruppe packen und aus array entfernen
						$team [$minAveTeamNumber] [] = $firstElem;
						$team [$minAveTeamNumber] [] = $lastElem;
					}
				} 				// sonst in die gefundenen in die andere gruppe packen
				else {
					$ret ['debug'] .= p ( "modulo else", 1 );
					// $ret['debug'] .= p($sortedArray,1);

					// wenn es die letzten 2 sind
					if (count ( $sortedArray ) == 2 && count ( $data ) == 10) {
						$ret ['debug'] .= p ( "nur noch 2", 1 );
						// in gruppe packen und aus array entfernen
						if ($lastChoosedTeam === 1) {
							$team [2] [] = $lastElem;
							$ret ['debug'] .= p ( "put into: 2", 1 );
						} else {
							$team [1] [] = $lastElem;
							$ret ['debug'] .= p ( "put into: 1", 1 );
						}
					} 					// sonst beide in selbe gruppe packen
					else {
						// in gruppe packen und aus array entfernen
						if ($lastChoosedTeam === 1) {
							$team [2] [] = $firstElem;
							$team [2] [] = $lastElem;
							$ret ['debug'] .= p ( "put into: 2", 1 );
						} else {
							$team [1] [] = $firstElem;
							$team [1] [] = $lastElem;
							$ret ['debug'] .= p ( "put into: 1", 1 );
						}
					}
				}

				unset ( $sortedArray [$firstElemKey] );
				unset ( $sortedArray [$lastElemKey] );

				$lastChoosedTeam = $minAveTeamNumber;
				$i ++;
			}

			$ret ['data'] = $team;
			$ret ['status'] = true;
		} else {
			$ret ['status'] = "Eingabe Array leer";
		}

		$ret ['debug'] .= "End seperate10PlayersInto2BalancedTeams <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function seperate20PlayersInto4BalancedTeams($data) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start seperate10PLayersInto2BalancedTeams <br>\n";
		if (is_array ( $data ) && count ( $data ) > 0) {
			// in 10ner teams aufteilen
			$seperatedData = $this->seperate10PlayersInto2BalancedTeams ( $data );
			$ret ['debug'] .= p ( $seperatedData, 1 );
			$ret ['debug'] .= p ( getMinAveEloOfTeam ( $seperatedData ['data'] [1], $seperatedData ['data'] [2] ), 1 );
			$seperatedArrays = $seperatedData ['data'];
			if (is_array ( $seperatedArrays ) && count ( $seperatedArrays ) > 0) {
				$i = 0;
				$teams = array ();
				foreach ( $seperatedArrays as $k => $v ) {
					$doubleSeperatedData = array ();
					$doubleSeperatedData = $this->seperate10PlayersInto2BalancedTeams ( $v );
					$doubleSeperatedArray = $doubleSeperatedData ['data'];

					// wenn erste iteration -> dann in team1 und 2 aufteilen
					if ($i === 0) {
						$teams [1] = $doubleSeperatedArray [1];
						$teams [2] = $doubleSeperatedArray [2];
					} 					// sonst team3 und 4
					else {
						$teams [3] = $doubleSeperatedArray [1];
						$teams [4] = $doubleSeperatedArray [2];
					}
					$i ++;
				}
				$ret ['data'] = $teams;
			}
		} else {
			$ret ['status'] = "Eingabe Array leer";
		}

		$ret ['debug'] .= "End seperate10PlayersInto2BalancedTeams <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-01-01
	*/
	function setInQueueTime($steamID = 0) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start setInQueueTime <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if ($steamID > 0) {
			$SingleQueueGroup = new SingleQueueGroup ();
			$retSQG = $SingleQueueGroup->checkIfAlreadyInQueueWithGroup ( $steamID );
			if ($retSQG ['inQueue'] == true) {
				$sql = "UPDATE `QueueGroupMembers`
						SET CheckTimestamp = " . time () . "
								WHERE SteamID = " . secureNumber ( $steamID ) . " AND GroupID = " . $retSQG ['GroupID'] . "
										";
				$ret ['debug'] .= p ( $sql, 1 );
				$data = $DB->update ( $sql );
				$ret ['status'] = $data;
			} else {
				$sql = "UPDATE `Queue`
						SET CheckTimestamp = " . time () . "
								WHERE SteamID = " . secureNumber ( $steamID ) . "
										";
				$ret ['debug'] .= p ( $sql, 1 );
				$data = $DB->update ( $sql );
				$ret ['status'] = $data;
			}
		} else {
			$ret ['status'] = "steamID = 0";
		}

		$ret ['debug'] .= "End setInQueueTime <br>\n";

		return $ret;
	}
	function getQueueCountsSkillBracket($modus, $matchTypeID, $region, $forceSearch, $userSkillBracketTypeID) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getQueueCountsSkillBracket <br>\n";

		$data = array ();
		// Wenn force Search, dann auch nur force Search Leute suchen
		if ($forceSearch == "true") {
			$rangeUnten = ( int ) $_SESSION ['elo'] [$modus] - Matchmaking::maxEloRange;
			$rangeOben = ( int ) $_SESSION ['elo'] [$modus] + Matchmaking::maxEloRange;
			$forceSQL = " AND ForceSearch = 1";
		} else {
			$forceSQL = "AND ForceSearch = 0";
		}

		$untere_grenze = ( int ) $rangeArray [$modus] ['untere_grenze'];
		$obere_grenze = ( int ) $rangeArray [$modus] ['obere_grenze'];

		$ret ['debug'] .= "MODUS: " . $modus . " \n\n";

		$ret ['debug'] .= " UG:" . $untere_grenze . " OG:" . $obere_grenze . " \n\n";
		$skillBracketSQL = "";
		if($forceSearch != "true"){
			switch ($userSkillBracketTypeID) {
				case 1 :
					$skillBracketSQL = " AND usb.SkillBracketTypeID = 1";
					break;
				case 2 :
					$skillBracketSQL = " AND usb.SkillBracketTypeID = 2";
					break;
				default :
					$skillBracketSQL = " AND usb.SkillBracketTypeID >= 3";
			}
		}
		else{
			$skillBracketSQL = "";
		}


		switch($matchTypeID){
			case "8":
				$sql = "SELECT DISTINCT q.SteamID, q.Elo, q.MatchModeID, q.Region, q.MatchTypeID, q.Timestamp
					FROM Queue q LEFT JOIN UserSkillBracket usb ON usb.SteamID = q.SteamID AND usb.MatchTypeID = ".(int) $matchTypeID."
					WHERE
					q.Region = " . ( int ) $region . "
							" . $forceSQL . "
									AND q.MatchModeID = " . ( int ) $modus . "
											AND q.MatchTypeID = " . ( int ) $matchTypeID . "
													" . $skillBracketSQL . "
															ORDER BY q.Timestamp ASC
															
															";
				$data = $DB->multiSelect ( $sql );
				$ret ['debug'] .= p ( $sql, 1 );
				
				$count = count ( $data );
				
				break;
			default:
				$count = 0;
				if (POINTSMATCHMAKING) {
					$sql = "SELECT DISTINCT q.SteamID, q.Elo, q.MatchModeID, q.Region, q.MatchTypeID, q.Timestamp
					FROM Queue q
					WHERE (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
							AND
							q.Region = " . ( int ) $region . "
									" . $forceSQL . " ))
											AND q.MatchModeID = " . ( int ) $modus . "
													AND q.MatchTypeID = " . ( int ) $matchTypeID . "
															ORDER BY q.Timestamp ASC
															LIMIT 10
															";
				} else {
					$sql = "SELECT DISTINCT q.SteamID, q.Elo, q.MatchModeID, q.Region, q.MatchTypeID, q.Timestamp
					FROM Queue q LEFT JOIN UserSkillBracket usb ON usb.SteamID = q.SteamID AND usb.MatchTypeID = ".(int) $matchTypeID."
					WHERE
					q.Region = " . ( int ) $region . "
							" . $forceSQL . "
									AND q.MatchModeID = " . ( int ) $modus . "
											AND q.MatchTypeID = " . ( int ) $matchTypeID . "
													" . $skillBracketSQL . "
															ORDER BY q.Timestamp ASC
															";
				}
				
				$data = $DB->multiSelect ( $sql );
				$ret ['debug'] .= p ( $sql, 1 );
				
				$count = count ( $data );
				
				if (POINTSMATCHMAKING) {
					// Nach Gruppen suchen
					$sql2 = "SELECT DISTINCT SteamID, Elo, qg.MatchModeID, qg.Region, qg.MatchTypeID, qg.GroupID, Timestamp
					FROM QueueGroup qg JOIN QueueGroupMembers qgm
					ON qg.GroupID = qgm.GroupID AND
					qg.MatchTypeID = qgm.MatchTypeID AND
					qg.MatchModeID = qgm.MatchModeID
					WHERE
					-- (((Elo >= " . ( int ) $untere_grenze . " AND Elo <= " . ( int ) $obere_grenze . ")
							-- AND
							qg.Region = " . ( int ) $region . "
									" . $forceSQL . " -- ))
											AND qg.MatchModeID =  " . ( int ) $modus . "
													AND qg.MatchTypeID = " . ( int ) $matchTypeID . "
															ORDER BY qg.Timestamp ASC
															LIMIT " . $limit . "
																	";
				} else {
				
					$sql2 = "SELECT DISTINCT qgm.SteamID, Elo, qg.MatchModeID, qg.Region, qg.MatchTypeID, qg.GroupID, Timestamp
					FROM QueueGroup qg JOIN QueueGroupMembers qgm
					ON qg.GroupID = qgm.GroupID AND
					qg.MatchTypeID = qgm.MatchTypeID AND
					qg.MatchModeID = qgm.MatchModeID
					LEFT JOIN UserSkillBracket usb ON qgm.SteamID = usb.SteamID AND usb.MatchTypeID = ".(int) $matchTypeID."
					WHERE
					qg.Region = " . ( int ) $region . "
							" . $forceSQL . "
									AND qg.MatchModeID =  " . ( int ) $modus . "
											AND qg.MatchTypeID = " . ( int ) $matchTypeID . "
													" . $skillBracketSQL . "
															ORDER BY qg.Timestamp ASC
															";
				}
				
				$ret ['debug'] .= p ( $sql2, 1 );
				$data2 = $DB->multiSelect ( $sql2 );
				
				$count = $count + count ( $data2 );
		}
		

		$ret ['count'] = $count;
		$ret ['data'] = $data;
		$ret ['data2'] = $data2;
		$ret ['debug'] .= "End getQueueCountsSkillBracket <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-09-05
	*/
	function getPlayerCountsOfQueueSkillBracket($steamID, $spielmodi, $rangeArray, $regions, $userSkillBracketTypeID, $forceSearch, $matchTypeID = 1) {
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "- Start getPlayerCountsOfQueueSkillBracket - \n\n";
		$ret ['foundPlayersSQL'] = array ();
		$ret ['foundPlayersData'] = array ();

		// $_SESSION['erweiterteSuche'] = array("6");
		// $ret .= "rangeArray: ".print_r($rangeArray,1)." \n\n";
		if (is_array ( $spielmodi ) && count ( $spielmodi ) > 0) {
			if (is_array ( $regions ) && count ( $regions ) > 0) {
				// region SQL Statement generieren lassen
				$regionsSQL = $this->getSQLForRegion ( $regions );
				$ret ['debug'] .= print_r ( $regionsSQL, 1 );
				$regionsSQL = $regionsSQL ['regionSQL'];

				foreach ( $spielmodi as $k => $modus ) {
					$countArray = array ();
					$posiArray = array ();

					foreach ( $regions as $k => $region ) {

						$retC = $this->getQueueCountsSkillBracket ( $modus, $matchTypeID, $region, $forceSearch, $userSkillBracketTypeID );
						$ret ['debug'] .= p ( $retC, 1 );
						$count = $retC ['count'];
						$data = $retC ['data'];
						$data2 = $retC ['data2'];
						// arrays Mergen
						$globalArray = array ();
						if ((is_array ( $data2 ) && count ( $data2 ) > 0) && (is_array ( $data ) && count ( $data ) > 0)) {
							$globalArray = array_merge ( $data, $data2 );
						} else if (is_array ( $data ) && count ( $data ) > 0) {
							$globalArray = $data;
						} else if (is_array ( $data2 ) && count ( $data2 ) > 0) {
							$globalArray = $data2;
						}

						$ret ['debug'] .= "+++++++++++++++++ GLOBAL ARRAY:" . p ( $globalArray, 1 );

						$posi = "";
						if (is_array ( $globalArray ) && count ( $globalArray ) > 0) {
							// nach Timstamp sortieren und position hausfinden
							$globalArray = orderArrayBy ( $globalArray, 'Timestamp', SORT_ASC );
							$ret ['debug'] .= "+++++++++++++++++ GLOBAL ARRAY:" . p ( $globalArray, 1 );
							$posi = recursive_array_search ( $steamID, $globalArray );
							$posiArray [] = ( int ) $posi + 1;
						}

						$countArray [] = $count;

						// matchDetails
						$ret ['matchDetails'] .= "<p>" . date ( 'H:i:s' ) . " - " . "Searching in MatchMode: " . $modus . " witch following ranges: " . $untere_grenze . " - " . $obere_grenze . " Players found: " . $count . "</p>";
					} // END REGION FOREACH

					$ret ['debug'] .= "+++++++++++++++++ COUNT:" . p ( $countArray, 1 );
					$ret ['debug'] .= "+++++++++++++++++ PosiArray:" . p ( $posiArray, 1 );

					// herausbekommen was der größte Wert zurzeit ist
					if (is_array ( $countArray ) && count ( $countArray ) > 0 && is_array ( $posiArray ) && count ( $posiArray ) > 0) {
						$max = max ( $countArray );
						$maxPosi = max ( $posiArray );
						$ret ['debug'] .= p ( "WIRD IN SESSION EINGETRAGEN: :" . "C:" . $max . " P:" . $maxPosi, 1 );

						$_SESSION ['queue'] [$modus] ['count'] = ( int ) $max;
						$_SESSION ['queue'] [$modus] ['position'] = ( int ) $maxPosi;
					} else {
						$ret ['debug'] .= p ( "irgend ein Array ist NULL", 1 );
						$_SESSION ['queue'] [$modus] ['count'] = ( int ) 1;
						$_SESSION ['queue'] [$modus] ['position'] = ( int ) 1;
					}
				} // END MODUS FOREACH
			}
		}
		$ret ['debug'] .= p ( $_SESSION, 1 );
		$ret ['debug'] .= "- END getPlayerCountsOfQueueSkillBracket - \n\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-09-05
	*/
	function getSkillBracketQueueCounts($matchTypeID=1) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start getSkillBracketQueueCounts <br>\n";
		// Skill Bracket Count
		$sql = "SELECT q.SteamID, usb.SkillBracketTypeID
				FROM `Queue` q LEFT JOIN UserSkillBracket usb ON usb.SteamID = q.SteamID AND usb.MatchTypeID = ".(int) $matchTypeID."
				WHERE q.ForceSearch = 0 AND q.MatchTypeID = ".(int) $matchTypeID."
				GROUP BY q.SteamID
				";
		$data = $DB->multiSelect ( $sql );
		$ret ['debug'] .= p ( $sql, 1 );
		$retData = array (
				-1 => 0,
				1 => 0,
				2 => 0,
				3 => 0
		);
		if (is_array ( $data ) && count ( $data ) > 0) {
			foreach ( $data as $k => $v ) {
				$skillBRacket = $v ['SkillBracketTypeID'];
				if($skillBRacket >= 3){
					$skillBRacket = 3;
				}
				$retData [$skillBRacket] ++;
			}
		}
		// Skill Bracket Group Count
		$sql = "SELECT qgm.SteamID, SkillBracketTypeID
				FROM `QueueGroupMembers` qgm LEFT JOIN UserSkillBracket usb ON usb.SteamID = qgm.SteamID AND usb.MatchTypeID = ".(int) $matchTypeID."
				JOIN QueueGroup qg ON qg.GroupID = qgm.GroupID
				WHERE qg.ForceSearch = 0 AND qg.MatchTypeID = ".(int) $matchTypeID."
				GROUP BY qgm.SteamID
				";
		$data2 = $DB->multiSelect ( $sql );
		$ret ['debug'] .= p ( $sql, 1 );
		if (is_array ( $data2 ) && count ( $data2 ) > 0) {
			foreach ( $data2 as $k => $v ) {
				$skillBRacket = $v ['SkillBracketTypeID'];
				if($skillBRacket >= 3){
					$skillBRacket = 3;
				}
				$retData [$skillBRacket] ++;
			}
		}

		// Force Search Count
		$sql = "SELECT q.SteamID
				FROM `Queue` q
				WHERE ForceSearch = 1 AND q.MatchTypeID = ".(int) $matchTypeID."
				GROUP BY q.SteamID
				";
		$data3 = $DB->multiSelect ( $sql );
		$ret ['debug'] .= p ( $sql, 1 );
		if (is_array ( $data3 ) && count ( $data3 ) > 0) {
			foreach ( $data3 as $k => $v ) {
				$retData [-1] ++;
			}
		}

		// Force Search Group Count
		$sql = "SELECT  DISTINCT GroupID
				FROM `QueueGroup` qg
				WHERE ForceSearch = 1 AND qg.MatchTypeID = ".(int) $matchTypeID."
				";
		$data4 = $DB->multiSelect ( $sql );
		$ret ['debug'] .= p ( $sql, 1 );
		if (is_array ( $data4 ) && count ( $data4 ) > 0) {
			foreach ( $data4 as $k => $v ) {
				$retData [-1] ++;
				$retData [-1] ++;
			}
		}

		$ret ['data'] = $retData;
		$ret ['status'] = true;

		$ret ['debug'] .= "End getSkillBracketQueueCounts <br>\n";
		return $ret;
	}

	function cleanEverything($matchID, $groupID, $reason){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start cleanEverything <br>\n";

		// clean DBMM
		$retMM = $this->cleanDBMatchmaking($matchID);
		$ret['debug'] .= p($retMM,1);
		
		// clean MatchDetails
		$MatchDetails = new MatchDetails ();
		$retMD = $MatchDetails->deleteMatchDetails ($matchID);
		$ret['debug'] .= p($retMD,1);
		
		// clean created Match
		$Match = new Match ();
		$retM = $Match->deleteCreatedMatch ( $matchID );
		$ret['debug'] .= p($retM,1);
		
		// clean Queue
		$Queue = new Queue ();
		$retKAPOFQ = $Queue->kickAllPlayersOutOfQueue ( $matchID, $reason);
		$ret['debug'] .= p($retKAPOFQ,1);
		
		// clean QueueGroup
		$SingleQueueGroup = new SingleQueueGroup ();
		$retCQG = $SingleQueueGroup->cleanQueueGroup ( $groupID );
		$ret['debug'] .= p($retCQG,1);
		
		// clean Host of Match
		$MatchDetailsHostLobby = new MatchDetailsHostLobby ();
		$retHFM = $MatchDetailsHostLobby->cleanHostForMatch ($matchID);
		$ret['debug'] .= p($retHFM,1);
		
		// prio Queue resetten für bestimmte faelle
		switch($reason){
			case "declined":
			case "autoKick":
				$_SESSION['user']['joinTimestamp'] = 0;
				break;
		}
		//$ret['session'] = p($_SESSION,1);
		$ret['status'] = true;

		$ret['debug'] .= "End cleanEverything <br>\n";
		return $ret;
	}

}

?>