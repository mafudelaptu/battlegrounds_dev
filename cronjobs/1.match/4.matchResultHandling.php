<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
p("START MatchResultHandling");
$DB = new DB();
$con = $DB->conDB();

// alle offenen Matches auslesen
$sql = "SELECT MatchID, MatchModeID, MatchTypeID , (SELECT Count(*) FROM `MatchDetails` md WHERE MatchID = m.MatchID AND Submitted = 1) as Count
		FROM `Match` m
		WHERE TeamWonID = -1 AND TimestampClosed = 0 AND ManuallyCheck = 0 AND Canceled = 0
			AND EXISTS (SELECT md.MatchID FROM MatchDetails md WHERE md.MatchID = m.MatchID )
		ORDER BY MatchID
		LIMIT 100
		";
$openMatches = $DB->multiSelect($sql);
p($sql);
// f�r jedes Match auslesen ob alle submitted haben, dann ergebnis eintragen
if(is_array($openMatches) && count($openMatches) > 0){
	foreach($openMatches as $k =>$v){
		$matchID = $v["MatchID"];
		$matchModeID = $v["MatchModeID"];
		$matchTypeID = $v["MatchTypeID"];

		$sql = "SELECT *
				FROM `MatchDetails`
				WHERE MatchID = ".(int) $matchID." AND Submitted = 1 AND SubmissionFor != 0 AND SubmissionTimeStamp != 0
						";
		$countSubmitted = $DB->countRows($sql);
		$matchDetails = $DB->multiSelect($sql);
		//p($sql);
		$ret["debug"] .= p($matchDetails,1);
		switch ($matchTypeID){
			case "8":
				// wenn mind 8 submitted haben
				if($countSubmitted == 2){
				$MatchDetails = new MatchDetails();
					$strangeSubmission = $MatchDetails->checkForStrangeSubmissions1vs1($matchDetails);
					//p($strangeSubmission);
					// Wenn gegenstimmen kleiner als 3, dann automatisch behandeln
					if($strangeSubmission['status'] == true){

						$teamWonID = $MatchDetails->getTeamWon1vs1($matchDetails);
						//p($teamWonID);
						$teamWonID = $teamWonID['teamWonID'];

						$sql = "UPDATE `Match`
								SET TeamWonID = ".(int) $teamWonID.", TimestampClosed = ".time()."
										WHERE MatchID = ".(int) $matchID."
												";
						$DB->update($sql);
						//p($sql);

						// EloChanges eintragen
						$UserPoints = new UserPoints();
						$retUP = $UserPoints->insertPointChanges($matchID, $teamWonID);
						
						$ret['debug'] .= p($retUP,1);
						$ret['debug'] .= "MatchID: ".$matchID." -> teamWonID: ".$teamWonID;

						p("Matches erfolgreich aktualisert!<br>\n".$ret['debug']);
					}
					// manuell kontrollieren anhand von Screenshots
					else{
						$sql = "UPDATE `Match`
								SET ManuallyCheck = 1
								WHERE MatchID = ".(int) $matchID."
										";
						//p($sql);
						$DB->update($sql);
						p("Manueller Check!");
					}
				}
				else{
					echo "keine 2 MatchID:".$matchID;
				}
				break;
			case "9":
				// wenn mind 5 submitted haben
				if($countSubmitted >= 5){
				$MatchDetails = new MatchDetails();
					$strangeSubmission = $MatchDetails->checkForStrangeWinLoseSubmission($matchID, $matchDetails);
					p($strangeSubmission);
					// Wenn gegenstimmen kleiner als 3, dann automatisch behandeln
					if($strangeSubmission['result'] <= MatchDetails::screenshotGrenze){

						$teamWonID = $MatchDetails->getTeamWon($matchID);
						//p($teamWonID);
						$teamWonID = $teamWonID['teamWonID'];

						$sql = "UPDATE `Match`
								SET TeamWonID = ".(int) $teamWonID.", TimestampClosed = ".time()."
										WHERE MatchID = ".(int) $matchID."
												";
						$DB->update($sql);
						//p($sql);

						// EloChanges eintragen
						$UserPoints = new UserPoints();
						$retUP = $UserPoints->insertPointChanges($matchID, $teamWonID);
						
						$ret['debug'] .= p($retUP,1);
						$ret['debug'] .= "MatchID: ".$matchID." -> teamWonID: ".$teamWonID;

						p("Matches erfolgreich aktualisert!<br>\n".$ret['debug']);
					}
					// manuell kontrollieren anhand von Screenshots
					else{
						$sql = "UPDATE `Match`
								SET ManuallyCheck = 1
								WHERE MatchID = ".(int) $matchID."
										";
						//p($sql);
						$DB->update($sql);
						p("Manueller Check!");
					}
				}
				else{
					p("keine 6 MatchID:".$matchID);
				}
				break;
			default:
				// wenn mind 8 submitted haben
				if($countSubmitted >= (10-MatchDetails::screenshotGrenze)){
					$MatchDetails = new MatchDetails();
					$strangeSubmission = $MatchDetails->checkForStrangeWinLoseSubmission($matchID, $matchDetails);
					//p($strangeSubmission);
					// Wenn gegenstimmen kleiner als 3, dann automatisch behandeln
					if($strangeSubmission['result'] <= MatchDetails::screenshotGrenze){

						$teamWonID = $MatchDetails->getTeamWon($matchID);
						//p($teamWonID);
						$teamWonID = $teamWonID['teamWonID'];

						$sql = "UPDATE `Match`
								SET TeamWonID = ".(int) $teamWonID.", TimestampClosed = ".time()."
										WHERE MatchID = ".(int) $matchID."
												";
						$DB->update($sql);
						//p($sql);

						// EloChanges eintragen
						$UserPoints = new UserPoints();
						$retUP = $UserPoints->insertPointChanges($matchID, $teamWonID);
						
						//$ret['debug'] .= p($retUP,1);
						p("MatchID: ".$matchID." -> teamWonID: ".$teamWonID);

						p("Matches erfolgreich aktualisert!<br>\n".$ret['debug']);
					}
					// manuell kontrollieren anhand von Screenshots
					else{
						$sql = "UPDATE `Match`
								SET ManuallyCheck = 1
								WHERE MatchID = ".(int) $matchID."
										";
						//p($sql);
						$DB->update($sql);
						p("Manueller Check!");
					}
				}
				else{
					p("keine 10 MatchID:".$matchID);
				}
		}

	}
}
else {
	p("keine offenen Matches");
}

p("END MatchResultHandling");
?>