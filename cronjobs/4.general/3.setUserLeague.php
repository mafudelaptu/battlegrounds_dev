<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
p("NoLeagues: ".NOLEAGUES);


/* 
 * ###############
 * OBSOLET - in inc_general_fkt now 
 * ###############
*/
if(NOLEAGUES === false){
// 	$DB = new DB();
// 	$con = $DB->conDB();
// 	p("Start User League Handling");
// 	$UserLeague = new UserLeague();
// 	$User = new User();
// 	$UserPoints = new UserPoints();
	
// 	$retAllUser = $User->getAllUser();
// 	$allUser = $retAllUser['data'];
// 	if(is_array($allUser) && count($allUser) > 0){
// 		foreach($allUser as $k =>$v){
// 			$steamID = $v['SteamID'];
	
// 			// Points auslesen
// 			$retUP = $UserPoints->getGlobalPointsOfUser($steamID);
// 			$points = $retUP['data'];
	
// 			// LeagueDaten auslesen von User
// 			$retUL = $UserLeague->getLeagueOfUser($steamID);
// 			//p($retUL);
	
// 			$leagueData = $retUL['data'];
// 			$leagueTypeOfPlayer = $leagueData['LeagueTypeID'];
	
// 			$retUL = $UserLeague->getLeagueTypeByPoints($points);
// 			//p($retUL);
// 			$leagueByPoints = $retUL['data'];
// 			//p($leagueTypeOfPlayer." LBP:".$leagueByPoints);
// 			if($leagueTypeOfPlayer > 0){
// 				//p("League groesser 0");
// 				if($leagueTypeOfPlayer != $leagueByPoints){
// 					p("League änderung bei ".$steamID." (Points: ".$points.")!");
// 					$newLeague = 1;
// 					if($leagueTypeOfPlayer == 2 && $leagueByPoints == "1"){
// 						$newLeague = 2;
// 						p("Spieler: ".$steamID." würde in die Quali league fallen");
// 					}
// 					else{
// 						$newLeague = $leagueByPoints;
// 					}
						
// 					// league updaten
// 					$sql = "UPDATE `UserLeague`
// 					SET LeagueTypeID = ".(int) $newLeague.", LeagueTypeIDBefore = ".(int) $leagueTypeOfPlayer.", ChangeTimestamp = ".time()."
// 								WHERE SteamID = ".secureNumber($steamID)."
// 								";
// 					p($sql);
// 					$data = $DB->update($sql);
// 					p($data);
// 				}
// 			}
// 			else{
// 				$retUL = $UserLeague->insertFirstLeagueForUser($steamID);
// 				p($retUL);
// 			}
	
// 		}
// 	}

	
// 	$DB = new DB();
// 	$con = $DB->conDB();
// 	p("Start User SkillBracket Handling");
// 	$UserSkillBracket = new UserSkillBracket();
// 	$SkillBracket = new SkillBracket();
// 	$User = new User();
// 	$UserPoints = new UserPoints();
// 	$time = microtime();
// 	$sql = "SELECT u.Name, u.Avatar, u.SteamID, usb.SkillBracketTypeID
// 					FROM `User` u LEFT JOIN UserSkillBracket usb ON usb.SteamID = u.SteamID
// 	";
// 	$data = $DB->multiSelectUnbuffered($sql); 
// 	$ret['debug'] .= p($sql,1);
// 	//p($data);
// 	$duration = microtime()-$time;
// 	p("DURATION:".$duration);
// 	$allUser = $data;
// 	if(is_array($allUser) && count($allUser) > 0){
// 		foreach($allUser as $k =>$v){
// 			$steamID = $v['SteamID'];
// 			$skillBracketTypeIDOfPlayer = $v['SkillBracketTypeID'];
// 			// LeagueDaten auslesen von User
// // 			$retUSB = $UserSkillBracket->getSkillBracketOfUser($steamID);
// // 			//p($retUL);
	
// // 			$leagueData = $retUL['data'];
// // 			$skillBracketTypeIDOfPlayer = $retUSB['data']['SkillBracketTypeID'];
	
// 			$retSB = $SkillBracket->getSkillBracketOfUserByStats($steamID);
// 			//p($retUL);
// 			$skillBracketTypeIDByPoints = $retSB['data'];
// 			//p($leagueTypeOfPlayer." LBP:".$leagueByPoints);
// 			if($skillBracketTypeIDOfPlayer > 0){
// 				//p("League groesser 0");
// 				if($skillBracketTypeIDOfPlayer != $skillBracketTypeIDByPoints){
// 					p("SkillBracket änderung bei ".$steamID." (SB: ".$skillBracketTypeIDOfPlayer." SBCalc:".$skillBracketTypeIDByPoints.")!");
	
// 					$newLeague = $skillBracketTypeIDByPoints;
					
	
// 					// league updaten
// 					$sql = "UPDATE `UserSkillBracket`
// 					SET SkillBracketTypeID = ".(int) $newLeague.", SkillBracketTypeIDBefore = ".(int) $skillBracketTypeIDOfPlayer.", ChangeTimestamp = ".time()."
// 								WHERE SteamID = ".secureNumber($steamID)."
// 								";
// 					p($sql);
// 					$data = $DB->update($sql);
// 					p($data);
// 				}
// 			}
// 			else{
// 				$retUL = $UserSkillBracket->insertFirstSkillBracketForUser($steamID);
// 				p($retUL);
// 			}
	
// 		}
// 	}
	
// 	p("End User SkillBracket Handling");
}

?>