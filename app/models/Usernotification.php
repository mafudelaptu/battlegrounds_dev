<?php

class Usernotification extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function getNotifications($user_id){
		$ret = array();
		$retData = array();
		$globalCount = 0;

		// Open Matches
		$data = Match::getAllOpenMatchesOfUser($user_id)->get();
		// $queries = DB::getQueryLog();
		// dd($queries);
		// dd($data);
		if(!empty($data)){
			if($data[0]->id != null){
				$tmp = array();
				$generalOpen = count($data);
				$subMissing = 0;
				foreach($data as $match){
					$submitted = $match->submitted;
					$cancelSubmits = (int) $match->cancelSubmits;
					if($submitted === 0 && $cancelSubmits === 0 ){
						$subMissing++;
					}
				}
				$tmp['count'] = $generalOpen;
				$matchString = ($generalOpen > 1 ? 'matches' : 'match');
				if($subMissing > 0){
					$matchString .= ' <span class="t text-danger" title="missing submits">('.$subMissing.')</span>';
				}
				$tmp['message'] = "open ".$matchString;
				$tmp['href'] = URL::to("openMatches");

				$retData[] = $tmp;
				$globalCount++;
			}
		}

		// get Event notifications
		$ret = Usernotification::getStartEventNotification($user_id);
		if(!empty($ret) && count($ret)>0){
			$retData[] = $ret;
			$globalCount++;
		}

		$ret = Usernotification::getCanceledEventNotification($user_id);
		if(!empty($ret) && count($ret)>0){
			$retData[] = $ret;
			$globalCount++;
		}

		$ret = Usernotification::getEventReminderNotification($user_id);
		if(!empty($ret) && count($ret)>0){
			$retData[] = $ret;
			$globalCount++;
		}
		$ret['data'] = $retData;
		$ret['count'] = $globalCount;
		$ret['status'] = true;
		//dd($ret);
		return $ret;
	}

	public static function getStartEventNotification($user_id){
		$data = EventModel::join("eventregistrations", "eventregistrations.event_id", "=", "events.id")
		->join("eventtypes", "eventtypes.id", "=", "events.eventtype_id")
		->leftJoin("created_events", function($join){
			$join->on("created_events.event_id", "=", "events.id")
			->on("created_events.ended_at", "=", DB::raw("'0000-00-00 00:00:00'"));
		})
		->where("events.ended_at", "0000-00-00 00:00:00")
		->where("eventregistrations.user_id", $user_id)
		->select("events.*", "eventtypes.min_submissions", "eventregistrations.created_event_id")
		->first();
		//$queries = DB::getQueryLog();
		//dd($queries[46]);
		$tmpData = array();
		if(!empty($data) && count($data)>0){
			$event_id = $data->id;
			$minSubmissions = $data->min_submissions;
			$created_event_id = $data->created_event_id;
			$start_at = $data->start_at;

			// Wenn ein Event gefunden dann kontrollieren was f�r ein status es hat
			// Check: noch nicht gestartet -> dann anzeiigen man ist in einem Event angemeldet
			switch($created_event_id){
				// Event noch nciht gestartet
				case "0":
				$tmpData['count'] = 1;
				$tmpData['message'] = "You signed-in to an Event (#".$event_id.")";
				$tmpData['href'] = "";
				break;
						// spieler war zu sp�t / wurde nciht zufgelassen
				case "-1":
				if(time() <= strtotime($start_at)+600){
					$tmpData['count'] = 1;
					$tmpData['message'] = "Sorry, you signed-in too late to the Event(#".$event_id."). ".$minSubmissions." other Players were found before you signed-in.";
					$tmpData['href'] = "";
				}

				break;
				case "-3":
				if(time() <= strtotime($start_at)+600){
					$tmpData['count'] = 1;
					$tmpData['message'] = "Sorry, the Event has been canceled, because not enough players sign-in to Event #".$event_id.".";
					$tmpData['href'] = "";
				}

				break;
						// Event wurde ge�ffnet und spieler ist drin
				default:
				$tmpData['count'] = 1;
				$tmpData['message'] = "Event successfully created. You are in Sub-Event: ".$created_event_id.". Click on this notification to get redirected to Event-page.";
				$tmpData['href'] = URL::to("event/".$event_id."/".$created_event_id);

				break;
			}



			return $tmpData;
		}
	}
	public static function getCanceledEventNotification($user_id){
		$tmpData = array();
		$checkinTime = time()-GlobalSetting::getEventEndSubmissionBorder();
		
		$data2 = EventModel::join("eventregistrations", "eventregistrations.event_id", "=", "events.id")
		->where("eventregistrations.user_id", $user_id)
		->where("events.ended_at", date("Y-m-d H:i:s", $checkinTime))->first();

		// ob canceled pr�fen
		if(!empty($data2) && count($data2) > 0){

			$event_id = $data2->id;
			$created_event_id = $data2->created_event_id;

				// spieler war zu sp�t / wurde nciht zufgelassen
			if($created_event_id == -2){
				$tmpData['count'] = 1;
				$tmpData['message'] = "Sorry, Event #".$event_id." was canceled. There were not enough Players that signed-in for this Event.";
				$tmpData['href'] = "";
			}
		}

		return $tmpData;
	}

	public static function getEventReminderNotification($user_id){
		$tmpData = array();

		$date = date("Y-m-d");
		$eventData = EventModel::getNextEvent()->first(); 
		if(!empty($eventData) && count($eventData)>0){
			//dd($eventData);
			$nextEventDate = date("Y-m-d", strtotime($eventData->start_at));
			
			if($date == $nextEventDate && time() <= strtotime($eventData->start_at)){
				$tmpData['count'] = 1;
				$tmpData['message'] = "Today is an Event at: <b>".date("H:i",strtotime($eventData->start_at))."</b> - probably till ".date("H:i",strtotime($eventData->start_at)+7200)."<br>(Sign-in at: ".date("H:i",strtotime($eventData->start_submission_at)).")";
				$tmpData['href'] = "";
				
			}
		}

		return $tmpData;
	}
}

