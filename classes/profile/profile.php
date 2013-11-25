<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Profile{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getNotifications($steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID > 0){
			$globalCount = 0;

			// Open Matches Notification
			$sql="Select m.MatchID, md.Submitted, (
					SELECT COUNT(mdcmv.SteamID) as CountCancelSubmitts
					FROM MatchDetailsCancelMatchVotes mdcmv
					WHERE SteamID = ".secureNumber($steamID)." AND mdcmv.MatchID = m.MatchID
							) as CountCancelSubmitts
							FROM `MatchDetails` md JOIN `Match` m ON md.MatchID = m.MatchID

							WHERE md.SteamID = ".secureNumber($steamID)." AND m.TeamWonID = -1 AND m.Canceled = 0
									";

			// 			$sql="Select m.MatchID, md.Submitted, COUNT(mdcmv.SteamID) as CountCancelSubmitts
			// 					FROM `MatchDetails` md JOIN `Match` m ON md.MatchID = m.MatchID
			// 					LEFT JOIN MatchDetailsCancelMatchVotes mdcmv ON mdcmv.SteamID = md.SteamID AND mdcmv.MatchID = m.MatchID
			// 					WHERE md.SteamID = ".secureNumber($steamID)." AND m.TeamWonID = -1 AND m.Canceled = 0
			// 					";
			$ret['debug'] .= $sql;
			$data = $DB->multiSelect($sql);
			if($data[0]['MatchID'] != ""){
				$count = count($data);
			}
			else{
				$count = 0;
			}
			if($count > 0){
				$tmpData['count'] = $count;
				$matchString = ($count > 1 ? 'Matches' : 'Match');
				$tmpData['message'] = "open ".$matchString;
				$tmpData['href'] = "openMatches.php";

				$retData[] = $tmpData;
				$globalCount++;
			}

			// Submissions fehlen
			$data = array();
			// Open Matches Notification
			$sql="Select m.MatchID, md.Submitted, (
					SELECT COUNT(mdcmv.SteamID) as CountCancelSubmitts
					FROM MatchDetailsCancelMatchVotes mdcmv
					WHERE SteamID = ".secureNumber($steamID)." AND mdcmv.MatchID = m.MatchID
							) as CountCancelSubmitts
							FROM `MatchDetails` md JOIN `Match` m ON md.MatchID = m.MatchID

							WHERE md.SteamID = ".secureNumber($steamID)." AND Submitted = 0
									";
			$ret['debug'] .= $sql;
			$data = $DB->multiSelect($sql);

			if(is_array($data) && count($data) > 0){
				$subCount=0;
				foreach($data as $k => $v){
					if($v['Submitted'] == 0 && $v['CountCancelSubmitts'] == 0){
						$subCount++;
					}
				}
				if($subCount>0){
					$tmpData['count'] = $subCount;
					$matchString = ($count > 1 ? 'Match-Results' : 'Match-Result');
					$is = ($subCount > 1 ? 'are' : 'is');
					$tmpData['message'] = $matchString." of you ".$is." missing!";
					$tmpData['href'] = "openMatches.php?openSubmissions=true";
					$retData[] = $tmpData;
					$globalCount++;
				}
			}
			/**
			 * Event-Notifications
			 */

			$data = array();
			$sql = "SELECT *
					FROM `Events` e JOIN EventSubmissions es ON es.EventID = e.EventID
					JOIN EventTypes et ON e.EventTypeID = et.EventTypeID
					WHERE es.SteamID = ".secureNumber($steamID)." AND e.EndTimestamp = 0
								
							LIMIT 1
							";
			$ret['debug'] .= p($sql,1);
				
			$data = $DB->select($sql);
				
			$sql = "SELECT *
					FROM `Events` e JOIN EventSubmissions es ON es.EventID = e.EventID
					WHERE es.SteamID = ".secureNumber($steamID)." AND e.EndTimestamp >= ".(time()-600)."
							LIMIT 1
							";
			$ret['debug'] .= p($sql,1);
			$data2 = $DB->select($sql);
			if(is_array($data) && count($data) > 0){
				$eventID = $data['EventID'];
				$minSubmissions = $data['MinSubmissions'];
				$createdEventID = $data['CreatedEventID'];
				// Wenn ein Event gefunden dann kontrollieren was f�r ein status es hat
				// Check: noch nicht gestartet -> dann anzeiigen man ist in einem Event angemeldet
				switch($createdEventID){
					// Event noch nciht gestartet
					case "0":
						$tmpData['count'] = 1;
						$tmpData['message'] = "You signed-in to an Event (#".$eventID."). Check-in starts at ".date("H:i",$data['EndSubmissionTimestamp']).", be sure to be online.";
						$tmpData['href'] = "index.php";
							
						$retData[] = $tmpData;
						$globalCount++;



						break;
						// spieler war zu sp�t / wurde nciht zufgelassen
					case "-1":
						if(time() <= $data['StartTimestamp']+600){
							$tmpData['count'] = 1;
							$tmpData['message'] = "Sorry, you signed-in too late to the Event(#".$eventID."). ".$minSubmissions." other Players were found before you signed-in.";
							$tmpData['href'] = "#";

							$retData[] = $tmpData;
							$globalCount++;
						}

						break;
							
						// spieler hat nicht rdy geklickt
					case "-2":
						if(time() <= $data['StartTimestamp']+600){
							$tmpData['count'] = 1;
							$tmpData['message'] = "Sorry, you don't confirmed that you are ready to play the Event(#".$eventID.") or declined it. Next time be prepared 10 minutes before Event start.";
							$tmpData['href'] = "#";

							$retData[] = $tmpData;
							$globalCount++;
						}

						break;

						// Event wurde ge�ffnet und spieler ist drin
					default:
						$tmpData['count'] = 1;
						$tmpData['message'] = "Event successfully created. You are in Sub-Event: ".$createdEventID.". Click on this notification to get redirected to Event-page.";
						$tmpData['href'] = "event.php?eventID=".$eventID."&cEID=".$createdEventID;
							
						$retData[] = $tmpData;
						$globalCount++;

							

						break;
				}

				// Event-Verloren MEssage
				$Event = new Event();
				$retLost = $Event->playerLostEvent($eventID, $createdEventID,true);
				$lostEvent = $retLost['status'];
				if($lostEvent === true){
					$tmpData['count'] = 1;
					$tmpData['message'] = "You Lost a Match in an Event (#".$eventID."-".$createdEventID."). Now you cant play any Matches in this Event anymore!";
					$tmpData['href'] = "";

					$retData[] = $tmpData;
					$globalCount++;
				}


			}
			// ob canceled pr�fen
			if(is_array($data2) && count($data2) > 0){

				$eventID = $data2['EventID'];
				$createdEventID = $data2['CreatedEventID'];

				// spieler war zu sp�t / wurde nciht zufgelassen
				if($createdEventID == -2){
					$tmpData['count'] = 1;
					$tmpData['message'] = "Sorry, Event #".$eventID." was canceled. There were not enough Players that signed-in for this Event.";
					$tmpData['href'] = "#";
						
					$retData[] = $tmpData;
					$globalCount++;
				}
			}

			/**
			 * Erinnerung das heute Event ist
			 */
			$date = date("Y-m-d");
			$Event = new Event();
			$retNExtEventData = $Event->getNextEvent();
			$eventData = $retNExtEventData['data'];
			$nextEventDate = date("Y-m-d", $eventData['StartTimestamp']);
				
			if($date == $nextEventDate){
				if($eventData['EndSubmissionTimestamp'] >= time()){
					$tmpData = array();
					$tmpData['count'] = 1;
					$tmpData['message'] = "Today is an Event at: <b>".date("H:i:s",$eventData['StartTimestamp'])."</b> - probably till ".date("H:i:s",$eventData['StartTimestamp']+7200)." (Sign-in at: ".date("H:i:s",$eventData['StartSubmissionTimestamp'])."). Dont forget to sign-in!";
					$tmpData['href'] = "index.php";
						
					$retData[] = $tmpData;
					$globalCount++;
				}

			}
				
			// Missing Replays
			$MatchDetailsReplay = new MatchDetailsReplay();
			$retMDR = $MatchDetailsReplay->getMatchesWithoutReplaysCountOfUser($steamID);
			$MissingReplayCount = $retMDR['data'];
			if($MissingReplayCount > 0){
				$tmpData = array();
				$tmpData['count'] = $MissingReplayCount;
				$tmpData['message'] = "Matches without uploaded replay";
				$tmpData['href'] = "matchesWithoutReplay.php";
				
				$retData[] = $tmpData;
				$globalCount++;
			}
			/**
			 * Supporting Notifications
			 */
			// Reddit link
			// 				$redditCookieName = "clickedOnRedditNotification";
			// 				if(!$_COOKIE[$redditCookieName]){
			// 					$redditLink = "http://www.reddit.com/r/DotA2/comments/1erf1e/weekly_tournaments_on_dota2leaguenet_everybody/";
			// 					$tmpData = array();
			// 					$tmpData['count'] = 1;
			// 					$tmpData['message'] = "For supporting dota2-league.net, please vote up the reddit post by clicking this link.";
			// 					$tmpData['href'] = "notificationRedirect.php?data=".$redditLink."&name=".$redditCookieName;
				
			// 					$retData[] = $tmpData;
			// 					$globalCount++;
			// 				}

			// Global Notifications
			$sql = "SELECT *
							FROM `GlobalNotifications` gn 
							WHERE Active = 1 AND 
									NOT EXISTS (SELECT SteamID FROM `CheckedGlobalNotification` WHERE SteamID = ".secureNumber($steamID)." AND GlobalNotificationID = gn.GlobalNotificationID)			
					";
			$data = $DB->multiSelect($sql); 
			$ret['debug'] .= p($sql,1);
			if(is_array($data) && count($data) > 0){
			   foreach ($data as $k => $v) {
			   	$redirectLink = $v['Href'];
			   	if($redirectLink != ""){
			   		$redirectURL = "&redirectLink=".$redirectLink;
			   	}
			   	else{
			   		$redirectURL = "&redirectLink=".$_SERVER['REQUEST_URI'];
			   	}
			   	$tmpData = array();
			   	$tmpData['count'] = 1;
			   	$tmpData['message'] = $v['Text'];
			   	$tmpData['href'] = "notificationRedirect.php?type=global&gnid=".$v['GlobalNotificationID'].$redirectURL;
			   	
			   	$retData[] = $tmpData;
			   	$globalCount++;
			  }
			}
			
			
			/**
			 * Race-Notifications
			 */
			// WInner Item Auswahl notification
			$sql = "SELECT *
					FROM `RaceWinner` rw
					WHERE SteamID = ".secureNumber($steamID)." AND RacePrizeID = 0
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			if(is_array($data) && count($data) > 0){
				// get count of prizes in last race
				$RacePrizes = new RacePrizes();
				$retRP = $RacePrizes->getPrizeCountOfRace($data['RaceID']);
				$count = $retRP['data'];
				
				$RaceCoinsPrizes = new RaceCoinPrizes();
				$retRCP = $RaceCoinsPrizes->getCoinPrizeByPlacement($data['Position']);
				$coins = $retRCP['data']['RewardCoins'];
				 
				$tmpData = array();
				$tmpData['count'] = 1;
				
				if($count >= $data['Position']){
					$tmpData['message'] = "
						Congratulations! You are a Winner of the Race #".$data['RaceID'].". By clicking on this link you can choose your Prize.
						";
					$tmpData['href'] = "chooseRacePrize.php?rID=".$data['RaceID'];
				}
				else{
					$tmpData['message'] = "
						Congratulations! You are a Winner of the Race #".$data['RaceID'].". You earned <b>".$coins."</b> N-GAGE.TV-Coins.
						";
					$tmpData['href'] = "notificationRedirect.php?type=raceNoPrize&rID=".$data['RaceID'];
				}
				
				$retData[] = $tmpData;
				$globalCount++;
			}

			// WInner Item Auswahl schon getroffen
			$sql = "SELECT *
					FROM `RaceWinner` rw JOIN RacePrizes rp ON rp.RacePrizeID = rw.RacePrizeID
					WHERE rw.SteamID = ".secureNumber($steamID)." AND rw.RacePrizeID > 0
							AND rp.Given = 0 AND rp.PrizeTypeID = 4
								
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			if(is_array($data) && count($data) > 0){

				$tmpData = array();
				$tmpData['count'] = 1;
				$tmpData['message'] = "
						You successfully choose your Prize. In near future you should get a friend-request of 'Dota2LeaguePrizes' and will trade your Prize!";
				$tmpData['href'] = "";

				$retData[] = $tmpData;
				$globalCount++;
			}

			/*
			 * GROUP NOTIFICATIONS
			 */
			$sql="SELECT *
					FROM GroupMembers
					WHERE SteamID = ".secureNumber($steamID)." AND Accepted = 0
									";
			$ret['debug'] .= $sql;
			$count = $DB->countRows($sql);
			if($count > 0){
				$tmpData = array();
				$tmpData['count'] = $count;
				$tmpData['message'] = "
							You got an invite-request for a Team. Check it out!
						";
				$tmpData['href'] = "profile.php?ID=".$steamID."#teams";
				
				$retData[] = $tmpData;
				$globalCount++;
			}
			
			// 				$tmpData['count'] = 1;
			// 				$tmpData['message'] = "Everybody who signs up the event should make sure to check this thread. Click to get redirected.
			// ";
			// 				$tmpData['href'] = "http://www.n-gage.tv/forum/showthread.php?tid=109";
				
			// 				$retData[] = $tmpData;
			// 				$globalCount++;

			$ret['data'] = $retData;
			$ret['count'] = $globalCount;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID 0";
		}

		return $ret;
	}

}

?>