<?php

class User_won_event extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function insertUserWonEvent($user_id, $event_id, $created_event_id){
		$insertArray = array(
			"user_id"=>$user_id,
			"event_id"=>$event_id,
			"created_event_id"=>$created_event_id,
			"created_at"=>new DateTime,
			);
		try{
			User_won_event::insert($insertArray);
		}
		catch(Exception $e){
			return false;
		}
	}

	public static function getUsersWonEvent($event_id){
		return User_won_event::where("event_id", $event_id);
	}
}
