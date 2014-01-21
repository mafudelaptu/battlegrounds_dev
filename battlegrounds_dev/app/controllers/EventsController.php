<?php

class EventsController extends BaseController {
	protected $layout = "master";
	protected $title = "Events";
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->layout->title = $this->title;

		$nextEvent = EventModel::getNextEvent()->first();
		$isEventActive = false;
		$retER = array();

		if(!empty($nextEvent)){
			($nextEvent->start_at < new DateTime && $nextEvent->canceled == 0) ? $isEventActive = true : $isEventActive = false;
			$event_id = $nextEvent->event_id;
			$matchmode_id =  $nextEvent->matchmode_id;
			$matchtype_id =  $nextEvent->matchtype_id;
			
		}
		else{
			$event_id = 0;
			$matchmode_id = 0;
			$matchtype_id = 0;
		}

		$retER = Eventregistration::getAllRegistrations($event_id, true, $matchtype_id)->get();
		

		//dd($retER);
		$retER2 = Eventregistration::checkIfPlayerAlreadySignedIn($event_id, Auth::user()->id);
		//dd($retER2);		
		$retERCM = Eventregistration::getRegistrationOfUser($event_id, Auth::user()->id)->first();
		if(!empty($retERCM)){
			$isRdyForEvent = $retERCM->ready;
			$created_event_id = $retERCM->created_event_id;
		}
		else{
			$isRdyForEvent = false;
			$created_event_id = 0;
		}

		$retAllowed = EventModel::checkRequirementsOfPlayer(Auth::user()->id, $event_id, $nextEvent);
//dd($retAllowed);
		// EventType
		$allEventTypesRet = Eventtype::getAllActiveEventtypes(true)->get();

		// EventStats
		$eventStatsRet = EventModel::getGlobalEventStats();

		// Last Event Winners
		$eventWinnersRet = EventModel::getLastEventWinners();

		// Most Event Wins
		$mostEventWinsRet = EventModel::getUserWithMostEventWins();

		// get Last Events
		$lastEvents = EventModel::getLastEvents("5");
		//dd($lastEvents);
		// Next-Event State
		$nextEventState = EventModel::getEventState($event_id, $retAllowed['status'], $retER2['status'], $created_event_id, $isRdyForEvent, $nextEvent);
		// dd($nextEventState);
		// 

		$contentData = array(
			"heading" => $this->title,
			"nextEvent" => $nextEvent,
			"eventSubData" => $retER,
			"eventSubsCount" => count($retER),
			"userAlreadyInEvent" => $retER2['status'],
			"allEventTypes" => $allEventTypesRet,
			"allowedToJoin" => $retAllowed['status'],
			"eventStats" => $eventStatsRet['data'],
			"lastEventWinner" => $eventWinnersRet['data'],
			"mostEventWins" => $mostEventWinsRet['data'],
			"lastEvents" => $lastEvents['data'],
			"isRdyForEvent" => $isRdyForEvent,
			"created_event_id" => $created_event_id,
			"nextEventState" => $nextEventState['status'],
			);
		//dd($nextEvent);
		$this->layout->nest("content", 'events.index', $contentData);
	}

	public function joinEvent(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$event_id = Input::get("event_id");
				$user_id = Auth::user()->id;

				$nextEvent = EventModel::getNextEvent()->first();
				$retAllowed = EventModel::checkRequirementsOfPlayer($user_id, $event_id, $nextEvent);
				//dd($retAllowed);
				if($retAllowed['status']){
					Eventregistration::insertRegistration($event_id, $user_id);
					$ret['status'] = true;
				}
				else{
					$ret['status'] = false;
				}
			}
		}
		return $ret;
	} 

	public function signOut(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$event_id = Input::get("event_id");
				$user_id = Auth::user()->id;
				Eventregistration::signOut($event_id, $user_id);
				$ret['status'] = true;
			}
		}
		return $ret;
	} 


	public function showEvent($event_id, $created_event_id){
		$title = "Event #".$event_id."[".$created_event_id."]";
		$this->layout->title = $title;

		$user_id = Auth::user()->id;

		$eventData = EventModel::getEventData($event_id, true)->first();
		//dd($eventData);
		$retStatus = EventModel::getPlayerStatus($event_id, $created_event_id, $user_id);

		$retTeam = Eventteam::getLastTeamOfUser($event_id, $created_event_id, $user_id)->first();
		$retT1 = Eventteam::getTeamMembers($event_id, $created_event_id, 1)->get();
		$retT2 = Eventteam::getTeamMembers($event_id, $created_event_id, 2)->get();
		$retT3 = Eventteam::getTeamMembers($event_id, $created_event_id, 3)->get();
		$retT4 = Eventteam::getTeamMembers($event_id, $created_event_id, 4)->get();
		$retT5 = Eventteam::getTeamMembers($event_id, $created_event_id, 5)->get();
		$retT6 = Eventteam::getTeamMembers($event_id, $created_event_id, 6)->get();
		
		$retMatches = Eventmatch::getEventMatchesStatus($event_id, $created_event_id);
		
		$retCData = Created_event::getCreatedEventData($created_event_id)->first();

		$retBracket = EventModel::getBracketJSON($retMatches['data']);
		//dd($retBracket);



		$contentData = array(
			"eventData" => $eventData,
			"playerStatus" => $retStatus['data'],
			"team1Data" => $retT1,
			"team2Data" => $retT2,
			"team3Data" => $retT3,
			"team4Data" => $retT4,
			"team5Data" => $retT5,
			"team6Data" => $retT6,
			"eventMatchesData" => $retMatches['data'],
			"createdEventData" => $retCData,
			"event_id" => $event_id,
			"created_event_id" => $created_event_id,
			"bracketJson" => $retBracket['data'],
			);

		$winnerTeam = array();
		if(!empty($retCData) && count($retCData) > 0){
			if($retCData->team_won_id > 0){
				$winnerTeam = $contentData['team'.$retCData->team_won_id."Data"];
			}
		}
		$contentData['winnerTeam'] = $winnerTeam;

		$teamOfPlayer = array();
		if(!empty($retTeam) && count($retTeam) > 0){
			$teamOfPlayer = $contentData['team'.$retTeam->eventteam_id."Data"];
		}
		$contentData['teamOfPlayer'] = $teamOfPlayer;

		$this->layout->nest("content", 'event.index', $contentData);
	}
}
