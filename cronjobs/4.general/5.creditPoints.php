<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
$DB = new DB();
$con = $DB->conDB();

p("START CreditPoints und so");

$aktuelleUhrzeit = date("H:i");
p($aktuelleUhrzeit);
//$aktuelleUhrzeit = "00:00";
$aktuellerTag = date("N");
//$aktuellerTag = "1";
if($aktuelleUhrzeit == "00:00" && $aktuellerTag == "1"){
	$startTS = strtotime("last monday");
	$endTS = time();
	$sql = "SELECT uc.SteamID, SUM(Vote) as Credits, (SELECT SUM(Vote) FROM UserCredits uc2 WHERE uc2.SteamID = uc.SteamID) as CreditsNow
			FROM UserCredits uc
			WHERE uc.Timestamp >= ".(int)$startTS." AND uc.Timestamp <= ".(int)$endTS."
			GROUP BY uc.SteamID
			HAVING Credits <= ".(int) DAILYCREDITSBORDERFORPUNISHMENT."
	";
	p($sql);
	$data = $DB->multiSelect($sql);

	if(is_array($data) && count($data) > 0){
		foreach ($data as $k => $v) {
			$credits = $v['Credits'];
			$creditsNow = $v['CreditsNow'];
			$steamID = $v['SteamID'];

			p("SteamID:".$steamiD." C:".$credits." CN:".$creditsNow);

			// Bestrafungen je nach status
			if($creditsNow < 0){
				$changeCredits = false;
			}
			else{
				$changeCredits = true;
			}
			if($changeCredits){
				$newCredits = (int) $creditsNow/2;
				$insertArray = array();
				$insertArray['SteamID'] = secureNumber($steamID);
				$insertArray['VotedOfPlayer'] = secureNumber($steamID);
				$insertArray['MatchID'] = 0;
				$insertArray['Vote'] = (int) $newCredits;
				$insertArray['Timestamp'] = time();

				$data = $DB->insert("UserCredits", $insertArray);
				$ret['debug'] .= p($sql,1);
				p($retUC);
			}
		}
	}

	$ret['debug'] .= p($sql,1);
}

$aktuelleUhrzeit = date("H:i");
p($aktuelleUhrzeit);
//$aktuelleUhrzeit = "00:00";
if($aktuelleUhrzeit == "00:00"){
	$startTS = strtotime("yesterday");
	$endTS = time();
	$sql = "SELECT uc.SteamID, SUM(Vote) as Credits, (SELECT SUM(Vote) FROM UserCredits uc2 WHERE uc2.SteamID = uc.SteamID) as CreditsNow
			FROM UserCredits uc 
			WHERE uc.Timestamp >= ".(int)$startTS." AND uc.Timestamp <= ".(int)$endTS."
			GROUP BY uc.SteamID
			HAVING Credits <= ".(int) DAILYCREDITSBORDERFORPUNISHMENT."
	";
	p($sql);
	$data = $DB->multiSelect($sql);
	
	if(is_array($data) && count($data) > 0){
	   foreach ($data as $k => $v) {
	   		$credits = $v['Credits'];
	   		$creditsNow = $v['CreditsNow'];
	   		$steamID = $v['SteamID'];
	   		
	   		p("SteamID:".$steamiD." C:".$credits." CN:".$creditsNow);
	   		
			// Bestrafungen je nach status
			if($creditsNow < 0){
				$resetCredits = false;
			}
			else{
				$resetCredits = true;
			}
			if($resetCredits){
				$UserCredits = new UserCredits();
				$retUC = $UserCredits->resetUserCredits($steamID);
				p($retUC);
			}
	  }
	}
	
	$ret['debug'] .= p($sql,1);
}




p("END CreditPoints");

?>