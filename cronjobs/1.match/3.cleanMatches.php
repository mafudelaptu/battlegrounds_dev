<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
// $DB = new DB();
// $DB->conDB();

// $sql = "SELECT DISTINCT m.MatchID as MatchID
// 		FROM `Match` m LEFT JOIN `MatchDetails` md ON m.MatchID = md.MatchID
// 		WHERE ISNULL(md.MatchID)
// 		";
// $data = $DB->multiSelect($sql);
// $debug .= p($sql,1);
// $debug .= p($data,1);

// if(is_array($data) && count($data) > 0){
// 	foreach ($data as $k => $v) {
// 		$matchID = $v['MatchID'];
		 
// 		$Match = new Match();
// 		$MatchDetails = new MatchDetails();
// 		$MatchTeams = new MatchTeams();
		 
// 		// MATCH
// 		$retMatch = $Match->deleteCreatedMatch($matchID);
// 		$debug .= p($retMatch['debug'],1);
		
// 		// MATCHDETAILS
// 		$retMD = $MatchDetails->deleteMatchDetails($matchID);
// 		$debug .= p($retMD['debug'],1);
		
// 		// MACHTTEAMS
// 		$retMT = $MatchTeams->deleteMatchTeamsByMatchID($matchID);
// 		$debug .= p($retMT['debug'],1);
// 	}
// }

// echo $debug;

?>