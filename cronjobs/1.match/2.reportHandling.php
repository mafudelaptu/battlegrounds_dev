<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
$DB = new DB();
$con = $DB->conDB();

p("START ReportHandling");

// alle Matches auslesen die noch nicht 
$sql="SELECT uv.*, vt.Gewicht, vt.Type
		FROM UserVotes uv JOIN VoteType vt ON uv.VoteTypeID = vt.VoteTypeID
		WHERE EditedByCronjob = 0
		ORDER BY MatchID, vt.Gewicht DESC
		";
$data = $DB->multiSelect($sql);

p($sql);
// Array Umschreiben
if(is_array($data) && count($data) > 0){
	$matchArray = array();
	foreach($data as $k =>$v){
		$tmpArray['SteamID'] = $v['SteamID'];
		$tmpArray['VoteOfUser'] = $v['VoteOfUser'];
		$tmpArray['VoteTypeID'] = $v['VoteTypeID'];
		$tmpArray['Gewicht'] = $v['Gewicht'];
		$tmpArray['Type'] = $v['Type'];
		
		$matchArray[$v['MatchID']][] = $tmpArray;
	}
}
//p($matchArray);

$UserVotes = new UserVotes();
$UserCredits = new UserCredits();
// für jedes gefundene Match nun Votes bearbeiten
if(is_array($matchArray) && count($matchArray) > 0){
	foreach($matchArray as $k =>$v){
		$matchID = $k;

		$votesRet = $UserVotes->getRelevantVotes($matchID,$v);
		$votes = $votesRet['data'];
		
		//p($votes);
		$retCredits = $UserCredits->insertCredits($matchID, $votes);
		//p($retCredits);
		
		// auf bearbeitet setzen
		if($retCredits['status']){
			$retSet = $UserVotes->setUserVotesOfMatchToEditedByCronjob($matchID);
			//p($retSet);
		}
	}
}


/*
 * Ban Handling
 */

// alle Credits auslesen die eine Summe -15 haben
p("Start Ban Handling");
$sql = "SELECT SUM(Vote) as Summe, SteamID
		FROM UserCredits 
		GROUP BY SteamID
		HAVING Summe <= ".UserCredits::banBorder."
		";
$data = $DB->multiSelect($sql);

// Wenn ban-bare Spieler gefunden wurden dann bannen/verwarnen
$Banlist = new Banlist();
if(is_array($data) && count($data) > 0){
	foreach($data as $k =>$v){
		$steamID = $v['SteamID'];
		$retIns = $Banlist->insertBanViaCronjob($steamID,1,"",true);
		p($retIns);
		if($retIns['status']){
			$retReset = $UserCredits->resetUserCredits($steamID);
			p($retReset);
		}
	}
}

p("End Ban Handling");
p("END ReportHandling");
?>