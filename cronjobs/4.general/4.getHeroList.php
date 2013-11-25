<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
// aktuelles Datum auslesen und ob heute wieder der 1 Wochentag ist
$datum = date("N");

// Wenn es Monatg ist
//$datum = 1;
if($datum == 1){
	$DB = new DB();
	$con = $DB->conDB();
	p("Start getting Hero-List");
	$url = "https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/?key=".STEAM_KEY."&language=en_us";
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$json = curl_exec($ch);
	curl_close($ch);
	$dataSteam = json_decode($json,true);
	$heroes = $dataSteam['result']['heroes'];
	
	if(is_array($heroes) && count($heroes) > 0){
		// vorherigen einträge löschen
		// if count ungleich count von ausgelesenem -> löschen und neu aufbauen
		$sql = "SELECT *
		FROM `ReplayHeroList`
		";
		$countInDb = $DB->countRows($sql);
		$ret['debug'] .= p($sql,1);
	
		$ret['debug'] .= p("C:".count($heroes)." CDB:".$countInDb,1);
	
		if(count($heroes) != $countInDb){
			$sql = "DELETE FROM ReplayHeroList
		";
			$data = $DB->delete($sql);
			$ret['debug'] .= p($sql,1);
			$insertArray = array();
			foreach ($heroes as $k => $v) {
				$tmp = array();
				$tmp['HeroID'] = $v['name'];
				$tmp['Name'] = secureStrings($v['localized_name']);
				$insertArray[] = $tmp;
			}
			$retInsP = $DB->multiInsert("ReplayHeroList", $insertArray,1);
			$retIns = $DB->multiInsert("ReplayHeroList", $insertArray);
			$ret['debug'] .= p($retInsP,1);
	
		}
	}
	p($ret);
	
	
	p("End getting Hero-List");
}

?>