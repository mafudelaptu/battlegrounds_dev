<?php

class EventModel extends Eloquent {
	protected $guarded = array();

	public static $rules = array();
	protected $table = 'events';

	public static function insertNewEvent($eventtype_id){
		if($eventtype_id>0){
			$etData = Eventtype::getData($eventtype_id)->first();

			$timestamp = Eventtype::calculateNextStartTimestamp($etData->start_time, $etData->start_day, $etData->region_id);
			$created_at = new DateTime;
			$created_at->setTimestamp($timestamp);

			$startSub = $timestamp-GlobalSetting::getEventStartSubmissionBorder();
			$startSubAt = new DateTime;
			$startSubAt->setTimestamp($startSub);

			$endSub = $timestamp-GlobalSetting::getEventEndSubmissionBorder();
			$endSubAt = new DateTime;
			$endSubAt->setTimestamp($endSub);

			$insertArray = array(
				"eventtype_id"=>$eventtype_id,
				"start_at"=>$created_at,
				"start_submission_at"=>$startSubAt,
				"end_submission_at"=>$endSubAt,
				"region_id"=>$etData->region_id,
				"created_at"=>new DateTime,
				);
			try{

			EventModel::insert($insertArray);
			}
			catch(Exception $e){
				return false;
			}
		}
	}

	public static function getStartingEvent(){
		return EventModel::join("eventtypes", "eventtypes.id", "=", "events.eventtype_id")
		->leftJoin("created_events", "created_events.event_id", "=", "events.id")
		->join("matchtypes", "matchtypes.id", "=", "eventtypes.matchtype_id")
		->where("created_events.id", null)
		->where("events.ended_at", "0000-00-00 00:00:00")
		->where("events.start_at", "<=", new DateTime);
	}

	public static function getCheckInEvents(){
		return EventModel::join("eventtypes", "eventtypes.id", "=", "events.eventtype_id")
		->leftJoin("created_events", "created_events.event_id", "=", "events.id")
		->join("matchtypes", "matchtypes.id", "=", "eventtypes.matchtype_id")
		->where("created_events.id", null)
		->where("events.ended_at", "0000-00-00 00:00:00")
		->where("events.end_submission_at", "<=", new DateTime)
		->where("events.start_at", ">", new DateTime);
	}

	public static function eventReachedMinSubmissions($event_id, $minSubmissions){
		$ret = array();
		if($event_id > 0){
			$count = Eventregistration::getAllRegistrations($event_id)
			->where("eventregistrations.ready", ">", 0)
			->count();
			if($count >= $minSubmissions){
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "event_id = 0";
		}
		return $ret;
	}

	public static function cancelEvent($event_id){
		$updateArray = array(
			"canceled" => 1,
			"ended_at" => new DateTime,
			"updated_at" => new DateTime,
			);
		EventModel::where("id", $event_id)->update($updateArray);
	}

	public static function getEventData($event_id, $data=false){
		if($data){
			return EventModel::where("events.id", $event_id)
			->join("eventtypes", "eventtypes.id", "=", "events.eventtype_id")
			->join("matchmodes", "matchmodes.id", "=", "eventtypes.matchmode_id")
			->join("regions", "regions.id", "=", "eventtypes.region_id")
			->join("tournamenttypes", "tournamenttypes.id", "=", "eventtypes.tournamenttype_id")
			->join("prizetypes", "prizetypes.id", "=", "eventtypes.prizetype_id")
			->select(
				"events.*",
				"eventtypes.*",
				"matchmodes.name as matchmode",
				"matchmodes.shortcut as mm_shortcut",
				"regions.name as region",
				"regions.shortcut as r_shortcut",
				"tournamenttypes.name as tournamenttype",
				"tournamenttypes.shortcut as tt_shortcut",
				"prizetypes.name as prize",
				"prizetypes.count as prizecount",
				"prizetypes.type as prizetype"
				);
		}
		else{	
			return EventModel::where("id", $event_id);
		}
	}

	public static function getAllMaybeClosableEvents(){
		return EventModel::join("created_events", "created_events.event_id", "=", "events.id")
		->where("events.ended_at", "0000-00-00 00:00:00")
		->where("events.canceled", 0);
	}

	public static function closeEvent($event_id){
		$updateArray = array(
			"ended_at"=> new DateTime,
			"updated_at"=> new DateTime,
			);
		EventModel::where("id", $event_id)->update($updateArray);
	}

	public static function getEventsByEventtype($eventtype_id){
		return EventModel::where("eventtype_id", $eventtype_id);
	}

	public static function getNextEvent(){
		return EventModel::join("eventtypes", "eventtypes.id", "=", "events.eventtype_id")
		->join("matchmodes", "matchmodes.id", "=", "eventtypes.matchmode_id")
		->join("regions", "regions.id", "=", "eventtypes.region_id")
		->join("tournamenttypes", "tournamenttypes.id", "=", "eventtypes.tournamenttype_id")
		->join("eventrequirements", "eventrequirements.id", "=", "eventtypes.eventrequirement_id")
		->join("prizetypes", "prizetypes.id", "=", "eventtypes.prizetype_id")
		->leftJoin("skillbrackettypes", "skillbrackettypes.id", "=", "eventrequirements.skillbracketborder")
		->where("events.ended_at", "0000-00-00 00:00:00")
		->where("events.region_id", Auth::user()->region_id)
		->orderBy("events.start_at", "asc")
		->select(
			"events.id as event_id",
			"events.*",
			"eventtypes.*",
			"matchmodes.name as matchmode",
			"matchmodes.shortcut as mm_shortcut",
			"regions.name as region",
			"regions.shortcut as r_shortcut",
			"tournamenttypes.name as tournamenttype",
			"tournamenttypes.shortcut as tt_shortcut",
			"eventrequirements.pointborder as pointReq",
			"skillbrackettypes.name as skillbracketReq",
			"prizetypes.name as prize",
			"prizetypes.count as prizecount",
			"prizetypes.type as prizetype"
			)
		;
	}

	public static function checkRequirementsOfPlayer($user_id, $event_id, $eventData=null){
		$ret = array();
		$ret['status'] = false;
		if(!empty($eventData) && count($eventData) > 0){
			$pointsReq = $eventData->pointsReq;
			$skillbracketReq = $eventData->skillbracketReq;
			$matchtype_id = $eventData->matchtype_id;
			$allowed = array();
			
			// points check
			if($pointsReq > 0){

				$points = Userpoint::getPoints($user_id, $matchtype_id);

				if($points >= $pointsReq){
					$allowed[] = true;
				}
				else{
					$allowed[] = false;
				}
			}

			// Skillbracket checken
			if($skillbracketReq > 0){

				$retUL = Userskillbracket::getSkillbracket($user_id, $matchtype_id)->first();
				$skillbracket_id = $retUL->skillbrackettype_id;

				if($skillbracket_id >= $leagueReq){
					$allowed[] = true;
				}
				else{
					$allowed[] = false;
				}
			}
			
			if(count($allowed) > 0 && in_array(false, $allowed)){
				$ret['status'] = false;
			}
			else{
				$ret['status'] = true;
			}
		}
		return $ret;
	}

	public static function getAllClosedEvents(){
		return EventModel::where("ended_at", "!=", "0000-00-00 00:00:00")->where("canceled",0);
	}

	public static function getGlobalEventStats(){
		$ret = array();

		$closedEvents = EventModel::getAllClosedEvents();

		// Events played
		$countPlayedEvents = $closedEvents->count();

		// Players signed-in count
		$playerSubCount = $closedEvents->join("eventregistrations", "eventregistrations.event_id", "=", "events.id")->count();

		// Players signed-in count
		$playerSubPlayedCount = $closedEvents->where("eventregistrations.created_event_id",">",0)->count();

		$data = array();
		$data['events_played'] = $countPlayedEvents;
		$data['players_signed_in'] = $playerSubCount;
		$data['players_played'] = $playerSubPlayedCount;

		$ret['data'] = $data;

		return $ret;
	}

	public static function getLastEvents($limit=5, $winners=false, $eventSubs=true, $createdEvents=true){
		$ret = array();
		$events = EventModel::join("eventtypes", "eventtypes.id", "=", "events.eventtype_id")
		->join("matchmodes", "matchmodes.id", "=", "eventtypes.matchmode_id")
		->join("regions", "regions.id", "=", "eventtypes.region_id")
		->join("tournamenttypes", "tournamenttypes.id", "=", "eventtypes.tournamenttype_id")
		->join("prizetypes", "prizetypes.id", "=", "eventtypes.prizetype_id")
		->select(
			"events.id as event_id",
			"events.*",
			"eventtypes.*",
			"matchmodes.name as matchmode",
			"matchmodes.shortcut as mm_shortcut",
			"regions.name as region",
			"regions.shortcut as r_shortcut",
			"tournamenttypes.name as tournamenttype",
			"tournamenttypes.shortcut as tt_shortcut",
			"prizetypes.name as prize",
			"prizetypes.count as prizecount",
			"prizetypes.type as prizetype"
			)
		->where("events.ended_at", ">", "0000-00-00 00:00:00")
		->where("events.canceled", 0)
		->where("events.region_id", Auth::user()->region_id)
		->orderBy("events.ended_at", "desc")
		->take($limit)->get();
		// $query = DB::getQueryLog();
		// die($query[31]['query']);
		// gewinner der Events auslesen
		if($winners){
			if(!empty($events)){
				foreach ($events as $k => $v) {
					$event_id = $v->event_id;
					$tmpData = EventModel::getWinnerOfEvent($event_id)->get();
					$events[$k]['winner'] = $tmpData;
				}
			}
		}

		// Createdevent Data
		if($createdEvents){
			if(!empty($events)){
				foreach ($events as $k => $v) {
					$event_id = $v->event_id;
					$createdEventStuff = Created_event::getAllCreatedEventsOfEvent($event_id)->get();
					$events[$k]['createdEventsData'] = $createdEventStuff;
				}
			}
		}

		// EventSubs hinzufügen
		if($eventSubs){
			if(!empty($events)){
				foreach ($events as $k => $v) {
					$event_id = $v->event_id;
					$matchmode_id = $v->matchmode_id;
					$matchtype_id = $v->matchtype_id;

					$stuff = Eventregistration::getPlayersSubmissionOfEvent($event_id, $matchtype_id);
					//dd(count($stuff['data']));
					$events[$k]['eventSubData'] = $stuff['data'];
					$events[$k]['eventSubsCount'] = $stuff['count'];
				}
			}
		}

		//dd($events[0]);

		$ret['data'] = $events;
		$ret['status'] = true;

		return $ret;
	}


	public static function getWinnerOfEvent($event_id){
		return User_won_event::getUsersWonEvent($event_id)->join("users", "users.id", "=", "user_won_events.user_id")
		->leftJoin("usercredits", "usercredits.user_id", "=", "user_won_events.user_id")
		->groupBy("user_won_events.user_id")
		->select(
			"user_won_events.*",
			"users.*",
			DB::raw("SUM(usercredits.vote) as credits")
			);
	}

	public static function getLastEventWinners(){
		$ret = array();
		$lastWinnersRet = EventModel::getLastEvents("1",true,false,false);
		if(!empty($lastWinnersRet['data']) && count($lastWinnersRet['data']) > 0){
			$ret['data'] = $lastWinnersRet['data'][0]['winner'];
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "no last Events";
			$ret['data'] = null;
		}
		
		return $ret;
	}

	public static function getUserWithMostEventWins($limit=5){
		$ret = array();
		$sql = "SELECT uwe.*, Count(uwe.user_id) as Wins
		FROM `user_won_events` uwe
		GROUP BY uwe.user_id
		ORDER BY Wins DESC
		LIMIT ".(int)$limit."
		";
		$data = DB::select(DB::raw($sql));
	//dd($data);
		if(!empty($data)){
			foreach($data as $k => $v){
				$user_id = $v->user_id;

				$userData = User::getUserData($user_id)->first();
				$data[$k]  = (object) array_merge((array) $data[$k], (array) $userData['attributes']);
				//$data[$k] = array_merge($data[$k], $userData);
				
				$userCreditsData = Usercredit::getCreditCount($user_id);

				$data[$k]= (object) array_merge((array) $data[$k], array("credits"=>$userCreditsData));
				
			}
		} 
		$ret['data'] = $data;
		$ret['status'] = true;
		return $ret;
	}

	public static function getEventState($event_id, $allowedToJoin, $userAlreadyInEvent, $created_event_id,$isRdyForEvent, $eventData=null){
		$ret = array();
		if(empty($eventData)){
			// hier daten auslesen
		}

		if($allowedToJoin){
			$now = time();
			$startSubmission = strtotime($eventData->start_submission_at);
			$endSubmission = strtotime($eventData->end_submission_at);
			$eventStart = strtotime($eventData->start_at);

			if(!$userAlreadyInEvent){
				//dd($startSubmission);
				if($userAlreadyInEvent || $now <= $startSubmission){
					$ret['status'] = "not_started_yet";
				}
				elseif($userAlreadyInEvent || ($now >= $endSubmission && $now < $eventStart)){
					//dd($eventData->end_submission_at);
					$ret['status'] = "not_started_but_ended_sign_in_time";
				}
				elseif($userAlreadyInEvent || $now >= $eventStart){
					$ret['status'] = "event_started_user_not_signed_in";
				}
				else{
					$ret['status'] = "sign_in_possible";
				}
			}
			elseif($now <= $endSubmission){
				$ret['status'] = "signed_in_in_signed_in_time";
			}
			elseif($now > $endSubmission && $now <= $eventStart){
				$ret['status'] = "signed_in_after_signintime_before_start";
			}
			elseif($now >= $eventStart){
				if($created_event_id>0){
					if($isRdyForEvent == "1"){
						$ret['status'] = "event_started_user_in_event";
					}
					else{
						$ret['status'] = "event_started_user_not_rdy";
					}
				}
				else{
					$ret['status'] = "event_should_start_but_not_created";
				}
			}
		}
		else{
			$ret['status'] = "user_not_allowed_to_join";
		}
		
		return $ret;
	}

	public static function getPlayerStatus($event_id, $created_event_id, $user_id){
		$ret = array();
		if(EventModel::isUserInEvent($user_id, $event_id, $created_event_id)){
			for($i=1; $i<=2; $i++){
				$retTeam = Eventteam::getTeamOfUser($event_id, $created_event_id, $i, $user_id)->first();
				if(!empty($retTeam) && count($retTeam)>0){
					$team_id = $retTeam->eventteam_id;

					$matchesData = Eventmatch::getMatchData($event_id, $created_event_id, $team_id)->first();

					$match_id = $matchesData->match_id;

				//opponent Team bestimmen
					if($team_id == $matchesData->team1){
						$opponentTeam = $matchesData->team2;
					}
					else{
						$opponentTeam = $matchesData->team1;
					}

					$mdData = Matchdetail::getMatchdetailDataOfPlayer($match_id, $user_id)
											->join("eventmatches", "eventmatches.match_id", "=", "matchdetails.match_id")
											->join("matches", "matches.id", "=", "matchdetails.match_id")->first();
					//dd($mdData);
					if(!empty($mdData) && count($mdData)>0){
						$data['round'.$i]['mdData'] = $mdData;
						//dd($mdData);
				// Match noch nciht gespielt
						if($mdData->team_won_id == "0" AND $mdData->canceled == "0" AND $mdData->submitted == "0"){
							$data['round'.$i]['status'] = "<div class='alert alert-danger'>You have to play Match: <a href='".URL::to("match/".$match_id)."'>".$match_id."</a></div><p><a class='btn btn-block btn-primary' href='".URL::to("match/".$match_id)."'>Go to Match</a></p>";
						}
						elseif($mdData->team_won_id == "0" AND $mdData->canceled == "0" AND $mdData->submitted == "1"){
							$data['round'.$i]['status'] = "<div class='alert alert-warning'>Waiting for other Players submit a result in Match: <a href='".URL::to("match/".$match_id)."'>".$match_id."</a></div>";
						}
						elseif($mdData->team_won_id == "0" AND $mdData->canceled == "1" AND $mdData->submitted == "1"){
							$data['round'.$i]['status'] = "<div class='alert alert-danger'>Match was canceled by Players</div>";
						}
						elseif($mdData->team_won_id == $mdData->team_id AND $mdData->canceled == "0" AND $mdData->submitted == "1"){
							$data['round'.$i]['status'] = "<div class='alert alert-success'>You won Match: <a href='".URL::to("match/".$match_id)."'>".$match_id."</a></div>";
						}
						elseif($mdData->team_won_id != $mdData->team_id AND $mdData->canceled == "0" AND $mdData->submitted == "1"){
							$data['round'.$i]['status'] = "<div class='alert alert-danger'>You lost Match: <a href='".URL::to("match/".$match_id)."'>".$match_id."</a></div>";
						}
						$data['round'.$i]['playerTeam'] = $team_id;
						$data['round'.$i]['matchesData'] = $matchesData;
						$data['round'.$i]['opponentTeam'] = $opponentTeam;
						//dd($data);
					}
					else{
						$ret['status'] = "MD = null";
					}
					
				}
				else{
					$ret['status'] = "Team_id = 0";
				}
			}
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['data'] = "";
			$ret['status'] = "User not in Event";
		}
		
		return $ret;
	}

	public static function isUserInEvent($user_id, $event_id, $created_event_id){
		$ret = false;
		$data = Eventteam::getTeamOfUser($event_id, $created_event_id, 1, $user_id)->first();
		if(!empty($data) && count($data) > 0){
			$ret = true;
		}

		return $ret;
	}

	public static function getBracketJSON($eventMatchesStatus){
		$ret = array();
		if(!empty($eventMatchesStatus) && count($eventMatchesStatus)>0){
			$json = array();
			$json['teams'] = array(0 => array("Team 1", "Team 2"), 1=> array("Team 3", "Team 4"));
			$json['results'] = array();
			foreach ($eventMatchesStatus as $round => $v) {
				foreach ($v as $key => $d) {

					if($d['team_won_id'] != 0){
						if($d['team_won_id'] === 1 || $d['team_won_id'] === 5 || $d['team_won_id'] === 3){
							$json['results'][($round-1)][] = array(1,0);
						}
						else{
							$json['results'][($round-1)][] = array(0,1);
						}	
					}
					else{
						$json['results'][($round-1)][] = array(0,0);
					}
				}
				
			}
			$ret['data'] = json_encode($json);
		}
		else{
			$ret['status'] = "status = null";
		}
		return $ret;
	}

	public static function getNextEvents($limit=3){
		return EventModel::join("eventtypes", "events.eventtype_id", "=", "eventtypes.id")
			->join("matchmodes", "matchmodes.id", "=", "eventtypes.matchmode_id")
		->join("regions", "regions.id", "=", "eventtypes.region_id")
		->join("tournamenttypes", "tournamenttypes.id", "=", "eventtypes.tournamenttype_id")
		->join("prizetypes", "prizetypes.id", "=", "eventtypes.prizetype_id")
		->select(
			"events.id as event_id",
			"events.*",
			"eventtypes.*",
			"matchmodes.name as matchmode",
			"matchmodes.shortcut as mm_shortcut",
			"regions.name as region",
			"regions.shortcut as r_shortcut",
			"tournamenttypes.name as tournamenttype",
			"tournamenttypes.shortcut as tt_shortcut",
			"prizetypes.name as prize",
			"prizetypes.count as prizecount",
			"prizetypes.type as prizetype"
			)
			->where("events.region_id", Auth::user()->region_id)
			->where("events.ended_at", "0000-00-00 00:00:00")
			->where("events.canceled", 0)
			->take($limit);
	}

	public static function setStatusOfEvents($events){
		if(!empty($events) && count($events) > 0){
			foreach($events as $k => $v){
				$start_at = new DateTime($v->start_at);
				$end_sub = new DateTime($v->end_submission_at);
				$start_sub = new DateTime($v->start_submission_at);
				$now = new DateTime;

				if($now < $start_sub){
					$events[$k]['status'] = "upcoming";
				}
				elseif($now >= $start_sub && $now < $end_sub){
					$events[$k]['status'] = "check-in";
				}
				elseif($now >= $end_sub && $now < $start_at){
					$events[$k]['status'] = "check-in-closed";
				}
				elseif($now >= $start_at){
					$events[$k]['status'] = "live";
				}
				else{
					$events[$k]['status'] = "undefined";
				}
			}
		}
		return $events; 
	}

	public static function getMaxRoundsOfEvent($minSubmissions, $playercount){
		// teams berechnen
		$teams_count = $minSubmissions/($playercount/2);

		$teams_count = $teams_count/2;
		$rounds = 0;
		for($i=$team_count; $i<1; $i/2){
			if($i >= 1){
				$rounds++;
			}
		}
		return $rounds;
	}
}
