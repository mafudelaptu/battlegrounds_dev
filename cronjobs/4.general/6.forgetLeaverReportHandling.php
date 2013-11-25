<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
$DB = new DB();
$con = $DB->conDB();

p("START forgetLeaverReportHandling");
// $startTS = 1377042014;
// //$startTS = 0;

// $sqlCancelLeaverVotes = "SELECT COUNT(*)
// 		FROM MatchDetailsCancelMatchVotes mdclmv
// 		WHERE mdclmv.MatchID = m.MatchID";
// $sqlCancelLeaverVoter = "SELECT GROUP_CONCAT(DISTINCT mdclmv.SteamID SEPARATOR ', ')
// 		FROM MatchDetailsCancelMatchVotes mdclmv
// 		WHERE mdclmv.MatchID = m.MatchID";
// $sqlCancelVotesForLeaver = "SELECT GROUP_CONCAT(DISTINCT mdclmv.VoteForPlayer SEPARATOR ', ')
// 		FROM MatchDetailsCancelMatchVotes mdclmv
// 		WHERE mdclmv.MatchID = m.MatchID";
// $sqlLeaverVotes = "SELECT COUNT(*)
// 		FROM MatchDetailsLeaverVotes mdlv
// 		WHERE mdlv.MatchID = m.MatchID";
// $sqlLeaverVoter = "SELECT GROUP_CONCAT(DISTINCT mdlv.SteamID SEPARATOR ', ')
// 		FROM MatchDetailsLeaverVotes mdlv
// 		WHERE mdlv.MatchID = m.MatchID";
// $sqlVotesForLeaver = "SELECT GROUP_CONCAT(DISTINCT mdlv.VoteForPlayer SEPARATOR ', ')
// 		FROM MatchDetailsLeaverVotes mdlv
// 		WHERE mdlv.MatchID = m.MatchID";
// $sql = "SELECT m.MatchID, Count(*) as Count,  GROUP_CONCAT(md.SteamID SEPARATOR ', ') as PlayersInMatch,
// 		(".$sqlLeaverVotes.") as LeaverCount,
				
// 						(".$sqlVotesForLeaver.") as VotesForLeaver,
// 						(".$sqlCancelLeaverVotes.") as CancelLeaverCount,
								
// 										(".$sqlCancelVotesForLeaver.") as CancelVotesForLeaver
// 										FROM `Match` m LEFT JOIN `MatchDetails` md ON md.MatchID = m.MatchID
// 										WHERE TimestampClosed >= ".$startTS." AND md.Submitted = 1 AND NOT EXISTS(SELECT NULL
// 												FROM ForgotMarkLeaverMatchesChecked fmlmc
// 												WHERE fmlmc.MatchID = m.MatchID)
// 												GROUP BY m.MatchID
// 												HAVING Count = 10 AND (CancelLeaverCount >= 3 OR LeaverCount >= 3) 
// 												";
// $data = $DB->multiSelect($sql);
// p("testset");
// p($sql);
// p("blabla");
// if(is_array($data) && count($data) > 0){
// 	$Match = new Match();
// 	foreach ($data as $k => $v) {
// 		$matchID = $v['MatchID'];
// 		$playersInMatch = $v['PlayersInMatch'];
// 		$leaverCount = $v['LeaverCount'];
// 		$leaverVoter = $v['LeaverVoter'];
// 		$votesForLeaver = $v['VotesForLeaver'];
		
// 		$cancelLeaverCount = $v['CancelLeaverCount'];
// 		$cancelLeaverVoter = $v['CancelLeaverVoter'];
// 		$cancelVotesForLeaver = $v['CancelVotesForLeaver'];
		
// 		$playerInMatchArr = explode(", ", $playersInMatch);
			
// 		// das Match als Checked eintragen
// 		$insertArray = array();
// 		$insertArray['MatchID'] = (int) $matchID;
// 		$retIns = $DB->insert("ForgotMarkLeaverMatchesChecked", $insertArray);
// 		$diffLeaver = array();
// 		p($leaverCount);
// 		if($leaverCount > 0 && $leaverCount != null){
// 			//$leaverVoterArr = explode(", ", $leaverVoter);
// 			$votesForLeaverArr = explode(", ", $votesForLeaver);
// 			p($votesForLeaverArr);
// 			if(is_array($votesForLeaverArr) && count($votesForLeaverArr) > 0){
// 				$Match = new Match();
// 			   foreach ($votesForLeaverArr as $kl => $vl) {
// 			   	p($vl." - ".$matchID);
// 					$retM = $Match->playerLeftTheMatch2($vl, $matchID, "leaver");
// 					p("Leaver:".$retM['left']);
// 					if($retM['left']){
// 						$sql = "SELECT GROUP_CONCAT(DISTINCT SteamID SEPARATOR ', ') as Voter
// 										FROM `MatchDetailsLeaverVotes` 
// 										WHERE VoteForPlayer = ".secureNumber($vl)." AND MatchID = ".(int) $matchID."
// 						";
// 						$data = $DB->select($sql);
// 						//p($sql);
// 						$voterArr = array();
// 						$voterArr = explode(", ", $data['Voter']);
// 						$diffLeaverTmp = array();
// 						$diffLeaverTmp = array_diff($playerInMatchArr, $voterArr);
// 						//p($diffLeaverTmp);
// 						$diffLeaver = array_merge($diffLeaver, $diffLeaverTmp);
// 						p($diffLeaver);
// 					}
// 			  	}
// 			}
// 		}
// 		$diffLeaver = array_unique($diffLeaver);
// 		p($diffLeaver);
// 		$diffCancelLeaver = array();
// 		if($cancelLeaverCount > 0 && $cancelLeaverCount != null){
// 		$cancelVotesForLeaverArr = explode(", ", $cancelVotesForLeaver);
// 			p($cancelVotesForLeaverArr);
// 			if(is_array($cancelVotesForLeaverArr) && count($cancelVotesForLeaverArr) > 0){
// 				$Match = new Match();
// 			   foreach ($cancelVotesForLeaverArr as $kl => $vl) {
// 			   	p($vl." - ".$matchID);
// 					$retM = $Match->playerLeftTheMatch2($vl, $matchID, "cancelLeaver");
// 					p("CancelLeaver:".$retM['left']);
// 					if($retM['left']){
// 						$sql = "SELECT GROUP_CONCAT(DISTINCT SteamID SEPARATOR ', ') as Voter
// 										FROM `MatchDetailsCancelMatchVotes` 
// 										WHERE VoteForPlayer = ".secureNumber($vl)." AND MatchID = ".(int) $matchID."
// 						";
// 						$data = $DB->select($sql);
// 						//p($sql);
// 						$voterArr = array();
// 						$voterArr = explode(", ", $data['Voter']);
// 						$diffLeaverTmp = array();
// 						//p($voterArr);
// 						//p($playerInMatchArr);
// 						$diffLeaverTmp = array();
// 						$diffLeaverTmp = array_diff($playerInMatchArr, $voterArr);
// 						p($diffLeaverTmp);
						
// 						$diffCancelLeaver = array_merge($diffCancelLeaver, $diffLeaverTmp);
// 						p($diffCancelLeaver);
// 					}
// 			  	}
// 			}
// 		}
// 		$diffCancelLeaver = array_unique($diffCancelLeaver);
// 		p($diffCancelLeaver);
// 		// für Leaver und Cancel leaver prinzipiell das gleiche machen
// 		for ($i=0; $i<2; $i++){
// 			if($i == 1){
// 				$diff = $diffCancelLeaver;
// 			}else {
// 				$diff = $diffLeaver;
// 			}
// 			// die die nicht gevoted haben bestrafen
// 			if(is_array($diff) && count($diff) > 0){
// 				$insertArray = array();
// 				foreach ($diff as $k => $v) {
// 					$steamID = $v;
// 					$tmp = array();
// 					$tmp['MatchID'] = (int) $matchID;
// 					$tmp['SteamID'] = secureNumber($steamID);
// 					$tmp['PointsTypeID'] = 9;
// 					$tmp['PointsChange'] = -50;
// 					$tmp['Timestamp'] = time();

// 					$insertArray[] = $tmp;
// 				}
// 				$retIns = $DB->multiInsert("UserPoints", $insertArray);
// 				p("punish:");
// 				p($insertArray);
				
// 			}
// 			else{
// 				p("all gevotet!");
// 			}
// 		}
// 	}
// }



p("END forgetLeaverReportHandling");
?>