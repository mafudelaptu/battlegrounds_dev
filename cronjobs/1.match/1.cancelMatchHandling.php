<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
$DB = new DB();
$con = $DB->conDB();

// // zuerst auslesen ob cancel Aufträge existieren gruppiert nach MatchID
// $sql = "SELECT COUNT(VoteForPlayer) as Count, VoteForPlayer
// 		FROM `MatchDetailsCancelMatchVotes`
// 		WHERE Edited = 0 AND Reason = 1
// 		GROUP BY MatchID, VoteForPlayer
// 		HAVING Count >= ".RELEVANTCANCELCOUNT."
				
// 				";
// $countLeaver = $DB->countRows($sql);
// $ret['debug'] .= p($sql,1);
// $ret['debug'] .= p($countLeaver,1);

// // Prüfen ob genügend votes eingegangen sind
// // Count berechnen anhand der potentiellen Leaver
// $tmpCount = (int) floor((10 - $countLeaver) / 100 * MATCHCANCELBORDER);
$ret['debug'] .= p($tmpCount,1);

$sql = "SELECT COUNT(VoteForPlayer) as Count, VoteForPlayer, mdcmv.MatchID, MatchModeID, MatchTypeID, Reason
		FROM `MatchDetailsCancelMatchVotes` mdcmv JOIN `Match` m ON m.MatchID = mdcmv.MatchID
		WHERE Edited = 0
		GROUP BY mdcmv.MatchID, VoteForPlayer
		HAVING Count >= ".RELEVANTCANCELCOUNT."
				";
$countCancelMatch = $DB->multiSelect($sql);

$ret['debug'] .= p($sql,1);
$ret['debug'] .= p(count($countCancelMatch),1);

// Wenn ein Datensatz gefunden dann darf das Match gecanceled werden
if(count($countCancelMatch) > 0 && is_array($countCancelMatch)){
	// Array einwenig umschreiben
	$cancelMatchArray = array();
	foreach ($countCancelMatch as $k => $v) {

		
		$ret['debug'] .= p($sql,1);
		$cancelMatchArray[$v['MatchID']]['count'] = $v['Count'];

		$cancelMatchArray[$v['MatchID']]['Leaver'][] = $v['VoteForPlayer'];

		$cancelMatchArray[$v['MatchID']]['MatchModeID'] = $v['MatchModeID'];
		$cancelMatchArray[$v['MatchID']]['MatchTypeID'] = $v['MatchTypeID'];
		$cancelMatchArray[$v['MatchID']]['Reason'] = $v['Reason'];
	}
	$ret['debug'] .= p($cancelMatchArray,1);
	// f�r jedes Match -> cancel
	if(is_array($cancelMatchArray) && count($cancelMatchArray) > 0){
		foreach($cancelMatchArray as $k =>$v){
			$matchID = $k;
				
			$matchModeID = $v['MatchModeID'];
			$matchTypeID = $v['MatchTypeID'];
			$leaverArray = $v['Leaver'];
			$reason = $v['Reason'];
				
			
			switch ($reason) {
				case 1: // Wegen Leaver dann folgendes tun: 
					// Match auf canceled setzen
					$sql = "UPDATE `Match`
							SET Canceled = 1, TimestampClosed = ".time()." 
							WHERE MatchID = ".(int) $matchID." 
									";
					$DB->update($sql);
						
					// Match auf bearbeitet setzen
					$sql = "UPDATE `MatchDetailsCancelMatchVotes`
							SET Edited = 1
							WHERE MatchID = ".(int) $matchID."
									";
					$DB->update($sql);
						
					// Leaver bestrafen
					$ret['debug'] .= p($leaverArray,1);
					if(is_array($leaverArray) && count($leaverArray) > 0){
						foreach($leaverArray as $k =>$v){
							$steamID = $v;
							$pointsChange = LEAVEMATCHPUNISHMENT*(-1);
							
							$insertArray = array();
							$insertArray['SteamID'] = secureNumber($steamID);
							$insertArray['MatchModeID'] = (int) $matchModeID;
							$insertArray['MatchTypeID'] = (int) $matchTypeID;
							$insertArray['MatchID'] = (int) $matchID;
							$insertArray['PointsTypeID'] = (int) 5;
							$insertArray['PointsChange'] = (int) $pointsChange;
							$insertArray['Timestamp'] = (int) time();
							
							$retINs = $DB->insert("UserPoints", $insertArray);
							$ret['status'] = $retINs;
							
							$ret['debug'] .= p($punishRet['debug'],1);
						}
					};
					$ret['debug'] .= p("Match:".$matchID." canceled",1);
					break;

				default:
					;
					break;
			}
				
				
				
				
				
				
		}
	}
}
else{
	echo "kein Match zum canceln gefunden";
}


// Hard gecancelte Matches suchen und canceln
$MatchDetails = new MatchDetails();
$cancelValue = MatchDetails::chancelValue;
$cancelCount = MATCHCANCELBORDER/10;

$sql = "SELECT DISTINCT m.MatchID, COUNT(md.SteamID) as CancelVotes
		FROM `MatchDetails` md JOIN `Match` m ON m.MatchID = md.MatchID
		WHERE m.Canceled = 0 AND m.ManuallyCheck = 0 AND m.TeamWonID = -1
            AND md.SubmissionFor = ".$cancelValue." AND md.Submitted = 1
    	GROUP BY md.MatchID
        HAVING CancelVotes >= ".$cancelCount."
		";
$data = $DB->multiSelect($sql);
$ret['debug'] .= "HARD Match Cancel Data found:".p($data,1);

// f�r jedes gefundene cancelbare Match -> Match canceln 
if(is_array($data) && count($data) > 0){
	foreach($data as $k =>$v){
			$matchID = $v['MatchID'];
			$ret['debug'] .= "HARD CANCEL MATCH(".$matchID.")!";
			// Match canceln
			$sql="UPDATE `Match`
 			 		SET Canceled = 1, TimestampClosed = ".time()."
					WHERE MatchID = ".(int) $matchID."
					";
			$DB->update($sql);
			$ret['debug'] .= p($sql,1);
	}
}
else{
	echo "kein Match zum hard canceln gefunden";
}

// match canceling für 1vs1-Queue
$MatchDetails = new MatchDetails();
$cancelValue = MatchDetails::chancelValue;
$cancelCount = 2;

$sql = "SELECT DISTINCT m.MatchID, COUNT(md.SteamID) as CancelVotes
		FROM `MatchDetails` md JOIN `Match` m ON m.MatchID = md.MatchID
		WHERE m.Canceled = 0 AND m.ManuallyCheck = 0 AND m.TeamWonID = -1
            AND md.SubmissionFor = ".$cancelValue." AND md.Submitted = 1
            		AND MatchTypeID = 8
    	GROUP BY md.MatchID
        HAVING CancelVotes >= ".$cancelCount."
		";
$data = $DB->multiSelect($sql);
$ret['debug'] .= "HARD Match Cancel Data 1vs1 Queue found:".p($data,1);

// f�r jedes gefundene cancelbare Match -> Match canceln
if(is_array($data) && count($data) > 0){
	foreach($data as $k =>$v){
		$matchID = $v['MatchID'];
		$ret['debug'] .= "HARD CANCEL MATCH(".$matchID.")!";
		// Match canceln
		$sql="UPDATE `Match`
 			 		SET Canceled = 1, TimestampClosed = ".time()."
					WHERE MatchID = ".(int) $matchID."
					";
		$DB->update($sql);
		$ret['debug'] .= p($sql,1);
	}
}
else{
	echo "kein Match zum hard canceln gefunden (1vs1Queue)";
}

// match canceling für 3vs3-Queue
$MatchDetails = new MatchDetails();
$cancelValue = MatchDetails::chancelValue;
$cancelCount = 4;

$sql = "SELECT DISTINCT m.MatchID, COUNT(md.SteamID) as CancelVotes
		FROM `MatchDetails` md JOIN `Match` m ON m.MatchID = md.MatchID
		WHERE m.Canceled = 0 AND m.ManuallyCheck = 0 AND m.TeamWonID = -1
            AND md.SubmissionFor = ".$cancelValue." AND md.Submitted = 1
            		AND MatchTypeID = 9
    	GROUP BY md.MatchID
        HAVING CancelVotes >= ".$cancelCount."
		";
$data = $DB->multiSelect($sql);
$ret['debug'] .= "HARD Match Cancel Data 1vs1 Queue found:".p($data,1);

// f�r jedes gefundene cancelbare Match -> Match canceln
if(is_array($data) && count($data) > 0){
	foreach($data as $k =>$v){
		$matchID = $v['MatchID'];
		$ret['debug'] .= "HARD CANCEL MATCH(".$matchID.")!";
		// Match canceln
		$sql="UPDATE `Match`
 			 		SET Canceled = 1, TimestampClosed = ".time()."
					WHERE MatchID = ".(int) $matchID."
					";
		$DB->update($sql);
		$ret['debug'] .= p($sql,1);
	}
}
else{
	echo "kein Match zum hard canceln gefunden (1vs1Queue)";
}


echo $ret['debug'];
?>