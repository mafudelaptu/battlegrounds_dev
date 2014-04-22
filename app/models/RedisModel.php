<?php

class RedisModel extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function setEventStartData($user_id, $event_id, $created_event_id){
		try{
			Redis::hset("event:user:".$user_id, "created_event_id", $created_event_id);
			Redis::hset("event:user:".$user_id, "event_id", $event_id);
		}
		catch(Exception $e){
			
		}
		
	}

	public static function setEventCheckInData($user_id, $event_id, $start_at){
		try{
			Redis::hset("event:checkIn:".$user_id, "event_id", $event_id);
			Redis::hset("event:checkIn:".$user_id, "start_at", $start_at);
		}
		catch(Exception $e){
			
		}
	}
}
