<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
p("Start END EVENT HANDLING");
$DB = new DB();
$con = $DB->conDB();

/**
 * Auslesen ob alle CreatedEvents schon irgendwie beendet wurden
 * Wenn alle beendet dann Event beenden
*/

$sql = "SELECT ce.EventID, ce.CreatedEventID, ce.TeamWonID, ce.Canceled
		FROM `Events` e JOIN CreatedEvents ce ON ce.EventID = e.EventID
		WHERE e.EndTimestamp = 0 AND e.Canceled = 0
		";
$data = $DB->multiSelect($sql);
p($data);
$countCreatedEvents = count($data);

if(is_array($data) && count($data)>0){
	// kontrollieren ob auch alle geschlossen wurden
	$countBeendet = 0;
	foreach($data as $k => $v){
		$teamWonID = $v['TeamWonID'];
		$canceled = $v['Canceled'];
		
		if($teamWonID > 0 OR $teamWonID == "-1" OR $canceled == "1"){
			$countBeendet++;
		}
	}
	
	// wenn alle beendet dann acuh event beenden
	if($countBeendet == $countCreatedEvents){
		$eventID = $data[0]['EventID'];
		
		$Event = new Event();
		$retUpdate = $Event->closeEvent($eventID);
		p($retUpdate);
	}
	else{
		p("Noch nciht alle CreatedEvents beendet!");
	}
	
		
		
	
}

p("End END EVENT HANDLING");
?>