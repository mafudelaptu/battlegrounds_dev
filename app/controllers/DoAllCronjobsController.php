<?php

class CronjobDoAllController extends BaseController {

	public function doAllCronjobs(){
		$ret = "";
		try {
			if(Redis::connect()){
				Redis::publish("notification", "event_start");
			}
		} catch (Exception $e) {
			$ret .= $e;
		}
		
		/**
		  * Match
		*/
		// cancel Matches
		$ret .= $this->cancelMatchHandling();
		
		// uservotes handling
		$ret .= $this->uservotesHandling();
		
		// (usercredits)ban handling
		$ret .= $this->bansHandling();
		// Match result handling
		$ret .= $this->matchResultHandling();
		
		/**
		*	Events
		*/
		// new Event Handling
		$ret .= $this->createNewEvent();

		// event checkin notifications
		$ret .= $this->notifyEventCheckIn();

		// start event
		$ret .= $this->startEvent();
		
		// event matches handling
		$ret .= $this->eventMatchesHandling();

		// round 2 handling
		$ret .= $this->eventRound2Handling();

		// round 2 result handling
		$ret .= $this->eventRound2ResultHandling();

		// end event handling
		$ret .= $this->eventEndHandling();

		/**
		*	General
		*/
		// weekly Votecounts reset
		$ret .= $this->updateVoteCounts();

		// permaban handling
		$ret .= $this->permabanActiveBansHandling();

		// active bans decay
		$ret .= $this->activeBansDecayHandling();

		// every monday update hero list
		$ret .= $this->getHeroList();
		//dd("test");

		return $ret;
	}

	public function matchResultHandling(){
		$ret = "=== Matches Result Handling === \n\r";

		$openMatches = Match::getAllOpenMatches()->get();
		if(!empty($openMatches)){
			foreach ($openMatches as $key => $match) {
				$match_id = $match->id;
				$matchmode_id = $match->matchmode_id;
				$matchtype_id = $match->matchtype_id;

				$submittedCount = Matchdetail::getSubmittedMatchdetails($match_id)->count();
				switch ($matchtype_id) {
					case 2: // 1vs1
					if($submittedCount >= 2){
						$matchdetailsData = Matchdetail::getMatchdetailData($match_id, false, false)->orderBy("matchdetails.points")
						->select("matchdetails.*")
						->get();

						$matchPlayersData = Match::getPlayersData($matchdetailsData, $matchtype_id);
						$check = $this->checkSubmissions($matchdetailsData, $matchtype_id);

						if($check['status'] == true){
							$teamWonData = Matchdetail::getTeamWon($matchdetailsData);
							$team_won_id = $teamWonData['team_won_id'];
						//dd($teamWonData);
							if($team_won_id > 0){
								Match::setTeamWon($match_id, $team_won_id);
									// set Point-changes
								$retUP = Userpoint::insertPointChanges($match_id, $team_won_id, $matchdetailsData, $matchPlayersData, $matchmode_id, $matchtype_id);

								$ret .= "Match: ". $match_id." entered result ".$retUP['status']." TeamWon: ".$team_won_id."\n\r";
							}
							else{
								Match::setMatchToManuallyCheck($match_id);
								$ret .= "Match: ". $match_id." - couldnt detect team won id! \n\r";
							}
						}
						else{
							Match::setMatchToManuallyCheck($match_id);
							$ret .= "Match: ". $match_id." - manually check! \n\r";
						}
					}
					else{
						$ret .= "Match: ". $match_id." - just ".$submittedCount. " submits yet! \n\r";
					}		
					break;
					
					default: // 5vs5
					if($submittedCount >= 8){
						$matchdetailsData = Matchdetail::getMatchdetailData($match_id, false, false)->orderBy("matchdetails.points")
						->select("matchdetails.*")
						->get();
						
						$matchPlayersData = Match::getPlayersData($matchdetailsData, $matchtype_id);

						$check = $this->checkSubmissions($matchdetailsData, $matchtype_id);

						if($check['wrongSubmissions'] <= 2){
							$teamWonData = Matchdetail::getTeamWon($matchdetailsData);
							$team_won_id = $teamWonData['team_won_id'];
							if($team_won_id > 0){
								// set Point-changes
								$retUP = Userpoint::insertPointChanges($match_id, $team_won_id, $matchdetailsData, $matchPlayersData, $matchmode_id, $matchtype_id);
								
								Match::setTeamWon($match_id, $team_won_id);
								
								$ret .= "Match: ". $match_id." entered result ".$retUP['status']." TeamWon: ".$team_won_id."\n\r";
							}
							else{
								$ret .= "Match: ". $match_id." - couldnt detect team won id! \n\r";
							}
						}
						else{
							Match::setMatchToManuallyCheck($match_id);
							$ret .= "Match: ". $match_id." - manually check! \n\r";
						}
					}
					else{
						$ret .= "Match: ". $match_id." - just ".$submittedCount. " submits yet! \n\r";
					}					
					break;
				}
			}
		}
		return $ret."\n\r";
	}

	public function checkSubmissions($matchdetails, $matchtype_id){
		$ret = array();

		if(!empty($matchdetails)){
			switch($matchtype_id){
				case 2: // 1vs1
				$countWins = 0;
				foreach ($matchdetails as $k => $v) {
					if($v->submissionFor == 1){
						$countWins++;
					}
				}

				if($countWins > 1 || $countWins == 0){
					$ret['status'] = false;
				}
				else{
					$ret['status'] = true;
				}

				break;
				default: // 5vs5 single etc
				$countWin = array("1"=>0, "2"=>0);
				foreach ($matchdetails as $k => $v){
					$team_id = $v->team_id;
						// WIN
					if($v->submissionFor == 1){
						$countWin[$team_id]++;
					}
				}

					// wenn von beiden seiten auf win getippt wurde -> haben n problem
				$falscherWert = 0;
				if($countWin[1] > 0  && $countWin[2] > 0){

					$min = min($countWin[1],$countWin[2] );

						// �ber 2 haben dagegen gestimmt -> nun haben wir wirklich n problem
					$falscherWert = $min;
				}
				$ret['wrongSubmissions'] = $falscherWert;
				$ret['status'] = true;
				break;
			}
		}
		else{
			$ret['status'] = "matchdetails empty";
		}

		return $ret;
	}

	public function cancelMatchHandling(){
		$ret = "=== Cancel Matches === \n\r";
		$matchtypes = Matchtype::getAllActiveMatchtypes()->get();
		if(!empty($matchtypes)){
			foreach ($matchtypes as $key => $mt) {
				$matchtype_id = $mt->id;
				$cancelBorder = GlobalSetting::getCancelBorderForMatchtype($matchtype_id);

				$openMatches = Match::getAllOpenMatches()->join("matchvotes", "matchvotes.match_id", "=", "matches.id")
				->where("matchvotes.matchvotetype_id", 1)
				->groupBy("matchvotes.match_id")
				->select(
					"matches.*",
					DB::raw("COUNT(matchvotes.user_id) as cancelCount")
					)
				->having("cancelCount", ">=", $cancelBorder);

				$openMatchesData = $openMatches->get();
				//dd($openMatchesData);
				if(!empty($openMatchesData)){
					foreach ($openMatchesData as $key => $m) {
					// schauen ob auch mit leaver
						$leaverVotes = Matchvote::getAllLeaverVotesCountsForMatch($m->id)->get();
						$leaverArray = array();

						if(!empty($leaverVotes)){
							$leaverRet = "";
							foreach ($leaverVotes as $key => $l) {
								//dd($l);
								if($l->leaverVotes >= 6){
									$leaverArray[] = $l->vote_for_user;
								}
								else{
									$leaverRet .= "   just ".$l->leaverVotes." vote for ".$l->vote_for_user." \n\r";
								}
							}
						}

				// match canceln
						$updateArray = array(
							"canceled" => 1,
							"closed" => new DateTime,
							);
						Match::where("id", $m->id)->update($updateArray);

						$ret .= "Match: ".$m->id." canceled \n\r";
						$ret .= $leaverRet;
				// wenn leaver drin  dann auch noch bestrafen
						if(is_array($leaverArray) && count($leaverArray) > 0){
							foreach($leaverArray as $k =>$v){
								$insertArray = array(
									"user_id" => $v,
									"matchmode_id" => $m->matchmode_id,
									"matchtype_id" => $m->matchtype_id,
									"match_id" => $m->id,
									"pointtype_id" => 5,
									"matchmode_id" => $m->matchmode_id,
									"pointschange" => GlobalSetting::getMatchLeaverPunishment(),
									"created_at" => new DateTime,
									);
								try{

									Userpoint::insert($insertArray);
								}
								catch(Exception $e){
									
								}
								$ret .= "       Leaver punished: ".$v."\n\r";
							}
						}

						$updateArray = array(
							"updated_at" => new DateTime
							);
						Matchvote::where("match_id", $m->id)->update($updateArray);
					}
				}
				else{
					$ret .= "no open Matches... \n\r";
				}
			}
		}
		else{
			$ret .= "no active matchtypes";
		}
		return $ret."\n\r";
	}

	public function uservotesHandling(){
		$ret = "=== Uservotes Handling === \n\r";

		$uservotesData = Uservote::getVotesOfAllMatches()
		->join("votetypes", "votetypes.id", "=", "uservotes.votetype_id")
		->get();
		if(!empty($uservotesData)){
			// rewrite Array for later
			$matchArray = array();
			foreach ($uservotesData as $key => $uv) {
				$matchArray[$uv->match_id][] = $uv;
			}
			if(is_array($matchArray) && count($matchArray) > 0){
				foreach($matchArray as $k =>$v){
					$match_id = $k;
					foreach ($v as $key => $vote) {
						
						Usercredit::insertCredit($vote->user_id, $vote->vote_of_user, $vote->weight, $match_id);
						$ret .= " inserted '".$vote->weight."' Credits for ".$vote->user_id." of ".$vote->vote_of_user." (match_id:".$match_id.") \n\r";
						// set on edited
						$updateArray = array(
							"updated_at"=>new DateTime,
							);
						Uservote::where("user_id", $vote->user_id)
						->where("vote_of_user", $vote->vote_of_user)
						->where("match_id", $match_id)
						->update($updateArray);
					}
					
				}
			}
			else{
				$ret .= "matchArray is empty \n\r";
			}
		}
		else{

		}

		return $ret."\n\r";
	}

	public function bansHandling(){
		$ret = "=== Bans Handling === \n\r";

		$userData = Usercredit::selectAllUserWithBanableCreditCount()->get();

		if(!empty($userData)){
			foreach($userData as $k =>$v){
				$user_id = $v->user_id;
				Banlist::insertBan($user_id, 1, "reached the credits bottom border (".GlobalSetting::getBanCreditBorder().")");
				Usercredit::resetUsercredits($user_id);
				$ret .= "banned user: ".$user_id." \n\r";

			}
		}
		else{
			$ret .= "usercreditsArray is empty \n\r";
		}

		return $ret."\n\r";
	}

	public function updateVoteCounts(){
		$ret = "=== Update VoteCounts === \n\r";

		// aktuelles Datum auslesen und ob heute wieder der 1 Wochentag ist
		$datum = date("N");
		//dd($datum);
		// Wenn es Monatg ist
		if($datum == GlobalSetting::getWeeklyVoteCountUpdateDay()){
			$lastUpdate = Uservotecount::getLastUpdate();
			
			$timestamp = strtotime(date("m.d.y"));
			$date = new DateTime;
			$date->setTimestamp($timestamp);
			
			if($lastUpdate != $date->format("Y-m-d H:i:s")){
				// für alle DB updaten
				Uservotecount::resetAllCounts();
				$ret .= "All UservoteCounts resetted to ".GlobalSetting::getWeeklyUpvoteCount()."-".GlobalSetting::getWeeklyDownvoteCount()."\n\r";
			}
			else{
				$ret .= "already updated \n\r";
			}
			
		}
		else{
			$ret .= "not getWeeklyVoteCountUpdateDay (".GlobalSetting::getWeeklyVoteCountUpdateDay().")\n\r";
		}

		return $ret."\n\r";
	}

	public function permabanActiveBansHandling(){
		$ret = "=== Permabans because of too many active bans handling === \n\r";
		$banData = Banlist::getAllUsersWhoHaveToMuchActiveBans()->get();
		// 	$queries = DB::getQueryLog();
		 // dd($queries);
		if(!empty($banData)){
			// if found -> permaban
			foreach ($banData as $key => $b) {
				$user_id = $b->user_id;

				$insertArray = array(
					"user_id" => $user_id,
					"banlistreason_id" => 3,
					"banned_at" => new DateTime,
					);
				Permaban::insert($insertArray);
				$ret .= "User: ".$user_id." got permabanned";
			}
		}
		return $ret."\n\r";
	}

	public function activeBansDecayHandling(){
		$ret = "=== Active Bans Decay handling === \n\r";
		$timeDecay = GlobalSetting::getBanDecayTime(); // 20 Days
		$banData = Banlist::getAllUsersThatHaveOldActiveBans($timeDecay)->get();
// $queries = DB::getQueryLog();
// dd($queries);
		if(!empty($banData)){
			// if found -> permaban
			foreach ($banData as $key => $b) {
				
				$id = $b->id;
				$user_id = $b->user_id;	

				$updateArray = array(
					"id" => $id,
					"display" => 0,
					"updated_at" => new DateTime,
					);
				Banlist::where("id", $id)->update($updateArray);
				$ret .= "Users ".$user_id." ban got deactivated";
			}
		}
		return $ret."\n\r";
	}

	public function createNewEvent(){
		$ret = "=== create new Event handling === \n\r";
		$eventtypes = Eventtype::getAllActiveEventtypes()->get();
// $queries = DB::getQueryLog();
	//dd($eventtypes);
		if(!empty($eventtypes)){
			// if found -> insert new event
			foreach ($eventtypes as $key => $et) {
				$eventtype_id = $et->id;

				$lastEvent = EventModel::getEventsByEventtype($eventtype_id)->orderBy("events.id", "desc")->first();

				//dd($et->ended_at);
				// first time or already ended
				if(!empty($lastEvent) && count($lastEvent) > 0){
					//dd($lastEvent->ended_at);
					if($lastEvent->ended_at != "0000-00-00 00:00:00"){
						EventModel::insertNewEvent($et->id);

						$ret .= "Created new Event \n\r";
					}
				}
				else{
					EventModel::insertNewEvent($eventtype_id);
					$ret .= "Created first Event \n\r";
				}
				
			}
		}
		return $ret."\n\r";
	}

	public function notifyEventCheckIn(){
		$ret = "=== CheckIn notification handling === \n\r";
		$events = EventModel::getCheckInEvents()->select("events.*")->get();
		// 	$queries = DB::getQueryLog();
		//dd($events);
		if(!empty($events)){
			foreach ($events as $key => $e) {
				//dd($e);
				$event_id = $e->id;
				$start_at = $e->start_at;
				// get All registered User in Event
				//dd($event_id);
				$users = Eventregistration::getAllRegistrations($event_id)->where("ready", 0)->get();
				//dd($users);
				if(!empty($users)){
					foreach ($users as $key => $u) {
						$user_id = $u->user_id;
						RedisModel::setEventCheckInData($user_id, $event_id, $start_at);
					}
				}
				try {
					Redis::publish("notification", "event_checkIn");
				} catch (Exception $e) {
					$ret .= $e;
				}
				
				$ret .= "send User notifications \n\r";
			}
		}
		return $ret."\n\r";
	}

	/**
	 * Wenn aktuelle Zeit > als Startzeit aktuelles Event
	 * dann kontrollieren ob min Submissions erreicht
	 * wenn ja dann in min submission teile aufteilen und kopien vom Event erzeugen
	 * alle bekommen eine notification mit entsprechendem inhalt( event open, sry leider zu spät signed-in, event canceled)
	*/
	public function startEvent(){
		$ret = "=== start Event handling === \n\r";
		$startingEvent = EventModel::getStartingEvent()
		->select("events.*", "eventtypes.min_submissions", "eventtypes.matchmode_id", "eventtypes.matchtype_id", "matchtypes.playercount")
		->first();
// $queries = DB::getQueryLog();
//dd($startingEvent);
		if(!empty($startingEvent)){
			//dd($startingEvent);
			$event_id = $startingEvent->id;
			$eventtype_id = $startingEvent->eventtype_id;
			$minSubmissions = $startingEvent->min_submissions;
			$matchmode_id = $startingEvent->matchmode_id;
			$matchtype_id = $startingEvent->matchtype_id;
			$region_id = $startingEvent->region_id;
			$team_size = (int)($startingEvent->playercount/2);

			// Alle Players rauswerfen die nicht rdy geklickt haben
			Eventregistration::throwOutPlayersOutOfEventWhoDontRdy($event_id);
//dd($minSubmissions);
			$retReached = EventModel::eventReachedMinSubmissions($event_id, $minSubmissions);
			//dd($retReached);
			// wenn minimum erreicht, dann Event kopien anfertigen
			if($retReached['status']){
				$retChunk = Eventregistration::chunkSingedInPlayersInEvent($event_id, $minSubmissions, $matchtype_id);
				
				$chunkedArray = $retChunk['data'];
				$stackCount = count($chunkedArray);

				for($i=0; $i<$stackCount; $i++){
					$playerData = $chunkedArray[$i];
					$countPlayer = count($playerData);

					// wenn genug spieler im Array
					if($countPlayer == $minSubmissions){
						$created_event_id = Created_event::insertNewCreatedEvent($event_id, $eventtype_id);
						//$created_event_id = 1;
						if($created_event_id > 0){
							$ret .= "New Event created: ".$created_event_id." \n\r";
							// Player zum erzeugten Event kopie zuweisen
							if(is_array($playerData) && count($playerData) > 0){
								foreach($playerData as $k =>$v){
									$user_id = $v->user_id;
									Eventregistration::updateCreatedEventValueOfPlayer($user_id, $event_id, $created_event_id);
									//$redis = Redis::connect();
									RedisModel::setEventStartData($user_id, $event_id, $created_event_id);
									// notification werden automatisch in profile.php getNotification erstellt.
								}

							}
							// die erfolgreich zugelosten spieler nun in teams aufteilen

							// if 1vs1 - teamsize = 1
							if($team_size == "1"){
								$countTeams = $minSubmissions;
								$sepTeams = $playerData;
							}
							// gesamtanzahl der spieler im pool durch {team_size} = Team anzahl
							else{
								$countTeams = $minSubmissions/$team_size;
								$retSepTeams = General::seperate20PlayersInto4BalancedTeams($playerData);

								$sepTeams = $retSepTeams['data'];
							}

							if(is_array($sepTeams) && count($sepTeams) > 0){
								$j = 1;
								foreach($sepTeams as $k =>$v){
									$team_id = $j;
									if(is_array($v) && count($v) > 0){
										foreach($v as $kPlayer =>$vPlayer){
											$user_id = $vPlayer->user_id;
											$points = $vPlayer['points'];
											// Spieler in das Team hinzufügen
											Eventteam::insertPlayerIntoTeam($event_id, $created_event_id, $team_id, $user_id, $points,1);
										}
									}
									$j++;
								}
							}
							// Nun die erzeugten Teams für Runde 1 auslesen und eintragen
							$retRoundOneData = Eventteam::getFirstRoundTeams($event_id, $created_event_id);
							$roundOneData = $retRoundOneData['data'];

							// für jeden eintrage ein Match erzeugen
							if(is_array($roundOneData) && count($roundOneData) > 0){
								$round = 1;
								//dd($roundOneData[2]);
								$match_number=1;
								foreach($roundOneData as $k =>$v){
									// erzeuge Matches für Runde 1   T1<>T2    und T3<>T4
									$match_id = Match::createNewMatch($matchtype_id, $matchmode_id, $region_id);
									$z = 0;
									foreach ($v as $kv => $team) {
										if($z > 0){
											$team2 = $kv;
										}
										else{
											$team1 = $kv;
										}
										//dd($team);
										$retSave = Matchdetail::addDetailsToMatch($match_id, $team, true);
										$z++;
									}

									Matchhost::setHost($match_id);

									// und schließlich EventMatch eintragen mit MatchID
									Eventmatch::insertEventMatchToEvent($event_id, $created_event_id, $match_id, $round, $team1, $team2, $match_number);
									$match_number++;
								}
							}
						}
					}
					else{
						// leider zu spät gekommen   -> notification schicken das too late und so
						// CreatedEvent auf -1 setzen um zu erkennen wer zu spät ist
						if(is_array($playerData) && count($playerData) > 0){
							foreach($playerData as $k =>$v){
								$user_id = $v->user_id;
								Eventregistration::updateCreatedEventValueOfPlayer($user_id, $event_id, -1);
								RedisModel::setEventStartData($user_id, $event_id, $created_event_id);

							}
						}
					}
				}
				
			}
			else{
				// Event auf canceled setzen
				EventModel::cancelEvent($event_id);
				$ret .= "Event canceled: ".$event_id." \n\r";

				// alle Signed-In user auslesen
				$retPlayer = Eventregistration::getAllRegistrations($event_id)->get();

				if(!empty($retPlayer)){
					foreach($retPlayer as $k =>$v){
						// Created Event auf -3 setzen -> Event Canceled
						$user_id = $v->user_id;
						Eventregistration::updateCreatedEventValueOfPlayer($user_id, $event_id, -3);
						RedisModel::setEventStartData($user_id, $event_id, -3);
					}
				}
			}
			try{
				Redis::publish("notification", "event_start");
			}
			catch(Exception $e){
				$ret .= $e;
			}
		}
		return $ret."\n\r";
	}

	public function eventMatchesHandling(){
		$ret = "=== Event Matches handling === \n\r";
		$eventmatches = Eventmatch::getAllFinishedEventMatches()
		->select("matches.*", "eventmatches.event_id", "eventmatches.created_event_id", "eventmatches.round")
		->get();
// $queries = DB::getQueryLog();
// dd($eventmatches);
		if(!empty($eventmatches) && count($eventmatches)>0){
			foreach ($eventmatches as $key => $em) {
				// unterscheidung was tun bei normalen eintrag cancel und manually check
				$team_won_id = $em->team_won_id;
				$canceled = $em->canceled;
				$check = $em->check;
				$event_id = $em->event_id;
				$created_event_id = $em->created_event_id;
				$match_id = $em->id;
				$nextRound = (int)($em->round+1);

				if($team_won_id > 0){
					$status = "Win/Lose";
				}
				if($canceled == 1){
					$status = "Canceled";
				}
				if($check == 1){
					$status = "MCheck";
				}

				switch($status){
					case "Win/Lose":
					// bei normalen verlauf -> gewonnenes Team bestimmen, eintragen und in den Pool weiterreichen
					// erstma wer gegeneinander antrat bestimmen
					$matchData = Eventmatch::getMatchDataByMatchID($event_id, $created_event_id, $match_id)->first();

					// unterscheidung wer gewonne hat
					($team_won_id === 1) ? ($event_team_won_id=$matchData->team1) : ($event_team_won_id=$matchData->team2);
					
					Eventmatch::updateEventMatchesTeamWonID($event_id, $created_event_id, $match_id, $event_team_won_id);
					
					// typen die gewonnen haben auslesen und wieder in pool tun
					$teamData = Eventteam::getTeam($event_id, $created_event_id, $event_team_won_id)->get();
					
					Eventpool::insertWonTeamIntoPool($event_id, $created_event_id, $nextRound, $teamData, $event_team_won_id);
					
					$ret .= "EventMatch ".$match_id." win/lose ".$event_team_won_id." \n\r";
					break;
					case "Canceled":
					// keinen in Pool lassen und eventMatch auf canceled setzen
					Eventmatch::updateEventMatchesTeamWonID($event_id, $created_event_id, $match_id, -1);
					$team_won_id = -1;
					// typen die gewonnen haben auslesen und wieder in pool tun
					$teamData = Eventteam::getTeam($event_id, $created_event_id, $team_won_id)->get();
					
					Eventpool::insertWonTeamIntoPool($event_id, $created_event_id, $nextRound, $teamData, $team_won_id);
					
					$ret .= "EventMatch ".$match_id." canceled \n\r";
					break;
					case "MCheck":
				// erst kontrollieren und dann per hand ergebniss eintragen  dann durch Cronjob automatisch einrtagen lassen
					Eventmatch::updateEventMatchesTeamWonID($event_id, $created_event_id, $match_id, -2);
					$ret .= "EventMatch ".$match_id." set to check \n\r";
					break;
				}			
			}
		}
		return $ret."\n\r";
	}

	// 1vs1 Event round X handling
	public function eventEndRoundHandling(){
		$ret = "=== Event: round X handling === \n\r";
		$eventmatches = Eventmatch::getAllPlayedMatches()->get();

		if(!empty($eventmatches) && count($eventmatches) > 0){
			foreach ($eventmatches as $key => $em) {
				$event_id = $em->event_id;
				$created_event_id = $em->created_event_id;
				$playercount = $em->playercount;
				$min_submissions = $em->min_submissions;
				$team_won_id = $em->team_won_id;

				// max Rounds auslesen für dieses Event
				$maxRounds = EventModel::getMaxRoundsOfEvent($min_submissions, $playercount);

				// matches auslesen aus event
				$retMatchesStatus = Eventmatch::getEventMatchesStatus($event_id, $created_event_id);
				$matchesStatus = $retMatchesStatus['data'];

				// letzte Runde bereits eingetragen
				if(!empty($matchesStatus[$maxRounds]) && count($matchesStatus[$maxRounds])>0){

				}
				// noch nciht die letzte Runde eingetragen
				else{
					switch($team_won_id){
						case "-1":
							
							break;
						case "0":
							break;
						default:

							break;
					}
				}

			}
		}
		else{
			$ret .= "played matches empty \n\r";
		}
	}


	/**
	 * Wenn genug Player in Pool für runde 2 -> dann Teams 5 und 6 generieren
	 * vorher noch prüfen ob match nciht von beiden gecanceled -> dann created event cancelen
	 * bei nur einem Match canceled hat das team das Event gewonnen welches das eine Match gewonnen hat
	 */


	public function eventRound2Handling(){
		$ret = "=== Event: round 2 handling === \n\r";
		$eventmatches = Eventmatch::getAllPlayedMatches()->get();
// $queries = DB::getQueryLog();
// dd($eventmatches);
		if(!empty($eventmatches) && count($eventmatches) > 0){
			// if found -> insert new event
			foreach ($eventmatches as $key => $em) {
				$event_id = $em->event_id;
				$created_event_id = $em->created_event_id;
// matches auslesen aus event
				$retMatchesStatus = Eventmatch::getEventMatchesStatus($event_id, $created_event_id);
				/*
					Hier muss man was fancyges ausdenken wie man es unendlich lang jede runde ausliest
					und wenn nicht letzte runde dann in nächste runde eintragen
				*/

				$matchesStatus = $retMatchesStatus['data'];

		// für Round 1 gucken ob nicht beide gecanceled
				if(is_array($matchesStatus[1]) && count($matchesStatus[1])>0){
					$canceledCount = 0;
					$matchesEndedCount = 0;
					foreach($matchesStatus[1] as $k => $v){
						if($v['team_won_id'] != "0"){
							$matchesEndedCount++;
						}
						if($v['team_won_id'] == "-1"){
							$canceledCount++;
						}

					}

					if($canceledCount == 2 AND $matchesEndedCount == 2){
				// wenn beide Mathces gecanceled wurden -> dann CreatedEvent cancelen
						Created_event::cancelCreatedEvent($created_event_id);
						$ret .= "Canceled created Event: ".$created_event_id." \n\r";
					}
					elseif ($canceledCount == 1 AND $matchesEndedCount == 2){
				// wenn nur ein Match gecanceled wurde -> team raussuchen welches gewonnen hat und als gewinner erklären
						foreach($matchesStatus[1] as $k => $v){
							if($v['team_won_id'] > 0){
								$team_won_id = $v['team_won_id'];
								break;
							}
						}
				// created Match beenden und gewinner festlegen
						Created_event::setWinnerForCreatedEvent($created_event_id, $team_won_id);
						
				// user die gewonnen haben in UserWonEvents eintragen
						$teamData = Eventteam::getTeam($event_id, $created_event_id, $team_won_id)->get();
						
						if(!empty($teamData)){
					// zuerst Event daten auslesen
							$retEventData = EventModel::getEventData($event_id, true)->first();
							
							$matchmode_id = $retEventData->matchmode_id;
							$matchtype_id = $retEventData->matchtype_id;
							$prizetype_id = $retEventData->prizetype_id;
							$match_id = 0;

							foreach($teamData as $k => $v){
								$user_id = $v->user_id;
								$retIns = $UserWonEvents->insertUserWonEvent($user_id, $event_id, $created_event_id);

							// checken ob überhaupt der Preis ein Point-Boost ist
								if($prizetype_id == "1"){
							// belohnung +50 Points hinzufügen
									Userpoint::insertGeneralPointChange($user_id, $match_id, $event_id, 7, GlobalSetting::getEventWinBonus(), $matchtype_id, $matchmode_id);
								}
							}
							$ret .= "Set winner for created Event: ".$created_event_id." Winner:".$team_won_id." \n\r";
						}
					}
					elseif ($canceledCount === 0 AND $matchesEndedCount == 2){
				// vorher KOntrollieren ob nciht schon erzeugt das ganze
						$retCheck = Eventmatch::checkIfRound2AlreadyCreated($event_id, $created_event_id);

						if(!$retCheck['status']){
							$retEventData = EventModel::getEventData($event_id, true)->first();
							//dd($retEventData);
							$matchmode_id = $retEventData->matchmode_id;
							$matchtype_id = $retEventData->matchtype_id;
							$region_id = $retEventData->region_id;

					// wenn beide Matches gespielt -> dann kontrollieren ob genügend spieler in Pool für round 2
							$retEventPool = Eventpool::checkPoolEnoughPlayerForRound2($event_id, $created_event_id);
							$eventPoolData = $retEventPool['data'];
							$eventPoolCount = $retEventPool['count'];
									//	dd($matchtype_id." ".$matchmode_id);
							if($eventPoolCount == 10){
						// gesamtanzahl der spieler im pool durch 5 = Team anzahl
								$countTeams = 2;
								$retSepTeams = General::seperate10PlayersInto2BalancedTeams($eventPoolData);
								$sepTeams = $retSepTeams['data'];
								if(is_array($sepTeams) && count($sepTeams) > 0){
									$team_id = 5;
									$match_number = 1;
									foreach($sepTeams as $k =>$v){
										if(is_array($v) && count($v) > 0){
											foreach($v as $kPlayer =>$vPlayer){
												
												$user_id = $vPlayer['user_id'];
												$points = $vPlayer['points'];
										// Spieler in das Team hinzufügen
												Eventteam::insertPlayerIntoTeam($event_id, $created_event_id, $team_id, $user_id, $points, 2);
											}
										}
										$team_id++;
									}
							// Nun die erzeugten Teams für Runde 2 auslesen und eintragen
									$retRoundTwoData = Eventteam::getSecondRoundTeams($event_id, $created_event_id, 10);

									$roundTwoData = $retRoundTwoData['data'];

							// für jeden eintrage ein Match erzeugen
									if(is_array($roundTwoData) && count($roundTwoData) > 0){
										$round = 2;
										$team1 = 5;
										$team2 = 6;


								// erzeuge Matches für Runde 2   T5<>T6
										$match_id = Match::createNewMatch($matchtype_id, $matchmode_id, $region_id);

										$z = 0;
										foreach ($roundTwoData as $kv => $team) {
											if($z > 0){
												$team2 = $kv;
											}
											else{
												$team1 = $kv;
											}

											$retSave = Matchdetail::addDetailsToMatch($match_id, $team, true);
											$z++;
										}

										Matchhost::setHost($match_id);

									// und schließlich EventMatch eintragen mit MatchID
										Eventmatch::insertEventMatchToEvent($event_id, $created_event_id, $match_id, $round, $team1, $team2, $match_number);
										$match_number++;
									}
								}
								$ret .= "Created Matches for created Event: ".$created_event_id." \n\r";
							}
						}
						else{
							$ret .= "Round 2 Match schon eingetragen! \n\r";
						}

					}
					else{
						$ret .="POOL keine 10 PLayer \n\r";
					}
				}	
			}
		}
		return $ret."\n\r";
		
	}

	/**
	 * Nun kontrollieren ob schon Runde 2 zuende gespiel wurde
	 * wenn das spielgecanceled wird -> createdEvent cancel  und keinen gewinner
	 * wenn einer gewinnt, dann created event teamWonId eintragen, userWonEvent eintragen, Eloboost geben
	*/
	public function eventRound2ResultHandling(){
		$ret = "=== Event: round 2 result handling  === \n\r";
		$eventmatches = Eventmatch::getAllPlayedMatches()
		->where("eventmatches.team1", 5)
		->where("eventmatches.team2", 6)
		->where("eventmatches.team_won_id","!=", 0)
		->get();
// $queries = DB::getQueryLog();
// dd($eventtypes);
		if(!empty($eventmatches)){
			// if found -> insert new event
			foreach ($eventmatches as $key => $em) {
				// jenachdem wie das spiel ausgegangen ist  behandeln
				$team_won_id = $em->team_won_id;
				$created_event_id = $em->created_event_id;
				$event_id =  $em->event_id;

				switch($team_won_id){
					case "-1":// created Event canceln
					Created_event::cancelCreatedEvent($created_event_id);
					$ret .= "Canceled created Event: ".$created_event_id." \n\r";
					break;

					case "-2":// von Admin begutachten lassen, per hand Wert ändern, dann per cronjob normal eintragen
					$ret .= "Manually Check Match. E:".$event_id." CE:".$created_event_id."  \n\r";
					break;

					default:// ein normales ergebnis
					// created Match beenden und gewinner festlegen
					Created_event::setWinnerForCreatedEvent($created_event_id, $team_won_id);
					
					// use die gewonnen haben in UserWonEvents eintragen
					$teamData = Eventteam::getTeam($event_id, $created_event_id, $team_won_id)->get();

					if(!empty($teamData)){
					// zuerst Event daten auslesen
						$retEventData = EventModel::getEventData($event_id, true)->first();
						
						$matchmode_id = $retEventData->matchmode_id;
						$matchtype_id = $retEventData->matchtype_id;
						$prizetype_id = $retEventData->prizetype_id;
						$match_id = 0;

						foreach($teamData as $k => $v){
							$user_id = $v->user_id;
							User_won_event::insertUserWonEvent($user_id, $event_id, $created_event_id);
							
							
							// checken ob überhaupt der Preis ein Point-Boost ist
							if($prizetype_id == "1"){
							// belohnung +50 Points hinzufügen
								Userpoint::insertGeneralPointChange($user_id, $match_id, $event_id, 7, GlobalSetting::getEventWinBonus(), $matchtype_id, $matchmode_id);
							}
						}
					}
					$ret .= "Inserted Winner for created Match:".$created_event_id." \n\r";
					break;
				}

			}
		}
		return $ret."\n\r";
	}

	public function eventEndHandling(){
		$ret = "=== Event End handling === \n\r";

		$events = EventModel::getAllMaybeClosableEvents()
		->select("created_events.event_id", "created_events.id", "created_events.team_won_id", "created_events.canceled")
		->get();
		$countCreatedEvents = count($events);
		if(!empty($events) && $countCreatedEvents > 0){
			$countBeendet = 0;
			foreach($events as $k =>$e){
				$team_won_id = $e->team_won_id;
				$canceled = $e->canceled;

				if($team_won_id > 0 OR $team_won_id == "-1" OR $canceled == "1"){
					$countBeendet++;
				}

			}

			// wenn alle beendet dann acuh event beenden
			if($countBeendet == $countCreatedEvents){
				$event_id = $events[0]->event_id;

				EventModel::closeEvent($event_id);
				$ret .= "Event:".$event_id." closed \n\r";
			}
			else{
				$ret .= "Can't close event, because not all matches closed. \n\r";
			}

		}

		return $ret."\n\r";
	}

	public function getHeroList(){
		$ret = "=== Herolist update handling === \n\r";
		// aktuelles Datum auslesen und ob heute wieder der 1 Wochentag ist
		$datum = date("N");
// Wenn es Monatg ist
		//$datum = 1;		
		if($datum == GlobalSetting::getHeroListUpdateDay()){

			$url = "http://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/?key=".GlobalSetting::getSteamAPIKey()."&language=en_us";
			try{
				$json = $this->curl_get($url, array());
				$dataSteam = json_decode($json,true);
				$heroes = $dataSteam['result']['heroes'];

				if(is_array($heroes) && count($heroes) > 0){
		// vorherigen einträge löschen
		// if count ungleich count von ausgelesenem -> löschen und neu aufbauen
					$data = DB::table("replay_dota2_heroes");
					$countInDb = $data->count();
					if(count($heroes) != $countInDb){
						$data->delete();
						$insertArray = array();
						foreach ($heroes as $k => $v) {
							$tmp = array();
							$tmp['id'] = $v['name'];
							$tmp['name'] = $v['localized_name'];
							$tmp['updated_at'] = new DateTime;
							$insertArray[] = $tmp;
						}
						try{
							DB::table("replay_dota2_heroes")->insert($insertArray);
							$ret .= "herolist updated! \n\r";
						}
						catch(Exception $e){
							$ret .= "insert failed (duplicate key)! \n\r";
						}

					}
					else{
						$ret .= "herolist is up-to-date! \n\r";
					}
				}
				else{
					$ret .= "steam-web-api not available. data = null\n\r";
				}
			}
			catch(Exception $e){
				//throw $e;
			}

		}
		else{
			//$ret .= "wrong day \n\r";
		}
		return $ret."\n\r";
	}

		/** 
		 * Send a GET requst using cURL 
		 * @param string $url to request 
		 * @param array $get values to send 
		 * @param array $options for cURL 
		 * @return string 
		 */ 
		function curl_get($url, array $get = NULL, array $options = array()) 
		{    
			$defaults = array( 
				CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get), 
				CURLOPT_HEADER => 0, 
				CURLOPT_RETURNTRANSFER => TRUE, 
				CURLOPT_TIMEOUT => 4 
				); 

			$ch = curl_init(); 
			curl_setopt_array($ch, ($options + $defaults)); 
			if( ! $result = curl_exec($ch)) 
			{ 
				trigger_error(curl_error($ch)); 
			} 
			curl_close($ch); 
			return $result; 
		} 
	}
