<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
$DB = new DB();
$con = $DB->conDB();

p("START UpdateVoteCounts");

// aktuelles Datum auslesen und ob heute wieder der 1 Wochentag ist
$datum = date("N");

// Wenn es Monatg ist
//$datum = 1;
if($datum == 1){
	$UserVoteCounts = new UserVoteCounts();
	$retLast = $UserVoteCounts->getLastUpdate();
	$lastUpdate = $retLast['data'];
	$timestamp = strtotime(date("m.d.y"));
	
	p("Timestamp vergleich: ".$timestamp." ".$lastUpdate);
	
	if($lastUpdate != $timestamp){
		// für alle DB updaten
		$sql = "UPDATE `UserVoteCounts`
			SET Upvotes = ".(int)UserVoteCounts::WeeklyUpvoteCount.",
				Downvotes = ".(int)UserVoteCounts::WeeklyDownvoteCount.",
				Updated = ".(int)$timestamp."
			";
		p($sql);
		$DB->update($sql);
		
	}
}
else{
	p("not Monday!");
}


// wenn 5 spiele am Tag gespielt dann +1 UP- und DOWNVOTE
$aktuelleUhrzeit = date("H:i");
p($aktuelleUhrzeit);
//$aktuelleUhrzeit = "00:00";
if($aktuelleUhrzeit == "00:00"){
	$Match = new Match();
	$retM = $Match->getMatchesPlayedCountYesterdayOfAllPlayers();
	p($retM);
	$data = $retM['data'];
	if(is_array($data) && count($data) > 0){
		$i = 1;
		foreach ($data as $k => $v) {
			$steamID = $v['SteamID'];
			$UserVoteCounts = new UserVoteCounts();
			$retUVC = $UserVoteCounts->updateVotesOfUserByHand($steamID);
			//p($retUVC);
			if($retUVC['status']){
				$i++;
			}
		}
		p($i." User updated UserVoteCounts");
	}
}
else{
	p("es ist nicht neuer Tag!");
}

p("END UpdateVoteCounts");

?>