<?php

class NotificationController extends BaseController {


	public function getEventStart(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$user_id = Auth::user()->id;
				$event_id = Redis::hget("event:user:".$user_id, "event_id");
				$created_event_id = Redis::hget("event:user:".$user_id, "created_event_id");
				if($event_id > 0){
					switch($created_event_id){
						case "-3":
						$status = "eventNotReachedEnoughPlayers";
						$ret['created_event_id'] = $created_event_id;
						$ret['status'] = true;
						$ret['alertType'] = "bootbox";
						$ret['callback'] = "reload";
						break;
						case "-2":
						$status = "declined";
						$ret['created_event_id'] = $created_event_id;
						$ret['status'] = true;
						$ret['alertType'] = "bootbox";
						$ret['callback'] = "reload";
						break;
						case "-1":
						$status = "tooLate";
						$ret['created_event_id'] = $created_event_id;
						$ret['status'] = true;
						$ret['alertType'] = "bootbox";
						$ret['callback'] = "reload";
						break;
						default:
						$status = "inEvent";
						$ret['created_event_id'] = $created_event_id;
						$ret['status'] = true;
						$ret['alertType'] = "modal";
						$ret['callback'] = URL::to("event/".$event_id."/".$created_event_id);
						break;
					}
					$ret['audio'] = true;


					$contentData = array(
						"status" => $status,
						"event_id" => $event_id,
						"created_event_id" => $created_event_id,
						"href" => $ret['callback'],
						);
					$html = View::make("notifications.event_start", $contentData)->render();
				//dd($id);
					$ret['html'] = $html;

					// delete notification
					Redis::del("event:user:".$user_id);
				}
				else{
					$ret['status'] = false;
				}				
			}
		}
		return $ret;
	}

	public function getEventCheckIn(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$user_id = Auth::user()->id;
				$event_id = Redis::hget("event:checkIn:".$user_id, "event_id");
				$start_at = Redis::hget("event:checkIn:".$user_id, "start_at");
				
				if($event_id > 0){
					$ret['event_id'] = $event_id;
					$ret['status'] = true;
					$ret['alertType'] = "modal";
					$ret['callback'] = "reload";
					
					$ret['audio'] = true;


					$contentData = array(
						"event_id" => $event_id,
						"start_at" => $start_at,
						"href" => $ret['callback'],
						);
					$html = View::make("notifications.event_checkIn", $contentData)->render();
				//dd($id);
					$ret['html'] = $html;

					// delete notification
					Redis::del("event:checkIn:".$user_id);
				}
				else{
					$ret['status'] = false;
				}				
			}
		}
		return $ret;
	}


	public function sendPing(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$ping_user_id = Input::get("user_id");
				$match_id = Input::get("match_id");
				$user_id = Auth::user()->id;

				$inMatch = Match::isUserInMatch($ping_user_id, $match_id);
				if($inMatch){
					$lastPingTime = Redis::get("ping:user:".$ping_user_id);
					if(time()-$lastPingTime >= GlobalSetting::getPingLockTime() || $lastPingTime == null){
						Redis::set("ping:user:".$ping_user_id.":noticed", false);
						Redis::set("ping:user:".$ping_user_id, time());
						Redis::publish("notification", "ping");
						$ret['status'] = true;
					}
					else{
						$ret['status'] = false;
					}
				}
				else{
					$ret['status'] = false;
				}

				
			}
		}
		return $ret;
	}

	public function getPing(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$user_id = Auth::user()->id;

				$ping = Redis::get("ping:user:".$user_id);
				$noticed = Redis::get("ping:user:".$user_id.":noticed");
				$ret['pingData'] = $ping;
				$ret['pingNoticed'] = $noticed;
				if($ping != null && $noticed == false){
					$ret['status'] = true;
					$ret['alertType'] = "ping";
					Redis::set("ping:user:".$user_id.":noticed", true);
				}
				else{
					$ret['status'] = false;
				}
			}
		}
		return $ret;
	}
	
}
