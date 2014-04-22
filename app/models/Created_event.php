<?php

class Created_event extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function insertNewCreatedEvent($event_id, $eventtype_id){
		$insertArray = array(
			"event_id" => $event_id,
			"eventtype_id" => $eventtype_id,
			"created_at" => new DateTime,
			);
		try{			
		$id = Created_event::insertGetId($insertArray);
						}
						catch(Exception $e){
							$id = 0;
						}

		return $id;
	}

	public static function cancelCreatedEvent($created_event_id){
		$updateArray = array(
			"canceled" => 1,
			"updated_at" => new DateTime,
			"ended_at" => new DateTime,
			);
		Created_event::where("created_event_id", $created_event_id)->update($updateArray);
	}

	public static function setWinnerForCreatedEvent($created_event_id, $team_won_id){
		$updateArray = array(
			"team_won_id" => $team_won_id,
			"updated_at" => new DateTime,
			"ended_at" => new DateTime,
			);
		Created_event::where("id", $created_event_id)->update($updateArray);
	}

	public static function getAllCreatedEventsOfEvent($event_id){
		return Created_event::where("event_id", $event_id);
	}

	public static function getCreatedEventData($created_event_id){
		return Created_event::where("id", $created_event_id);
	}
}
