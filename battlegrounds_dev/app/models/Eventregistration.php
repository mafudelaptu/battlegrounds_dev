<?php

class Eventregistration extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function throwOutPlayersOutOfEventWhoDontRdy($event_id){
		$data = Eventregistration::getAllUsersThatNotRdyOrDeclined($event_id)->get();
		if(!empty($data)){
			foreach($data as $k => $v){
				$user_id = $v->user_id;
				Eventregistration::updateCreatedEventValueOfPlayer($user_id, $event_id, -2);
			}
		} 
	}

	public static function getAllUsersThatNotRdyOrDeclined($event_id){
		return Eventregistration::where("event_id", $event_id)
		->where("ready", "<=", 0);
	}

	public static function updateCreatedEventValueOfPlayer($user_id, $event_id, $created_event_id){
		if($user_id > 0 && $event_id > 0){
			$updateArray = array(
				"created_event_id" => $created_event_id,
				"updated_at" => new DateTime,
				);
			Eventregistration::where("user_id", $user_id)->where("event_id", $event_id)->update($updateArray);
		}
	}

	public static function getAllRegistrations($event_id, $userData=false, $matchtype_id=0){
		if($userData){
		return Eventregistration::where("eventregistrations.event_id", $event_id)
									->join("users", "users.id", "=", "eventregistrations.user_id")
									->leftJoin("userpoints", function($join) use ($matchtype_id){
										$join->on("userpoints.user_id", "=", "eventregistrations.user_id")
										->on("userpoints.matchtype_id", "=", DB::raw($matchtype_id));
									})
									->select("eventregistrations.*",
										"users.*",
										DB::raw("IF(SUM(userpoints.pointschange)+users.basePoints > 0, SUM(userpoints.pointschange)+users.basePoints, users.basePoints) as points"))
									->groupBy("eventregistrations.user_id")
									->orderBy("eventregistrations.created_at", "asc");

		}
		else{
			
		return Eventregistration::where("eventregistrations.event_id", $event_id);
		}
	}

	public static function chunkSingedInPlayersInEvent($event_id, $minSubmissions, $matchtype_id){
		$ret = array();
		if($event_id > 0){
			$data = Eventregistration::getAllRegistrations($event_id)
			->join("users", "users.id", "=", "eventregistrations.user_id")
			->leftJoin("userpoints", function($join) use ($matchtype_id){
										$join->on("userpoints.user_id", "=", "eventregistrations.user_id")
										->on("userpoints.matchtype_id", "=", DB::raw($matchtype_id));
									})
			->groupBy("eventregistrations.user_id")
			->select(
				"eventregistrations.*",
				"users.name",
				"users.avatar",
				DB::raw("IF(SUM(userpoints.pointschange)+users.basePoints > 0, SUM(userpoints.pointschange)+users.basePoints, users.basePoints) as points")
				)
			->orderBy("eventregistrations.created_at", "ASC")
			->get();
			$tmpArray = array();
			if(!empty($data)){
				foreach($data as $k => $v){
					$tmpArray[] = $v;
				}
			} 
			if(!empty($tmpArray)){
				
				$chuckedArray = array_chunk($tmpArray, $minSubmissions);
				$ret['data']  = $chuckedArray;
				$ret['status'] = true;
			}
			else{
				$ret['data']  = $data;
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "event_id = 0";
		}
		return $ret;
	}


	public static function checkIfPlayerAlreadySignedIn($event_id, $user_id){
		$ret = array();
		//dd($event_id);
		$count = Eventregistration::where("event_id",$event_id)->where("user_id", $user_id)->count();
		if($count > 0){
			$ret['status'] = true;
		}
		else{
			$ret['status'] = false;
		}

		return $ret;
	}

	public static function getRegistrationOfUser($event_id, $user_id){
		return Eventregistration::where("event_id",$event_id)->where("user_id", $user_id);
	}

	public static function getPlayersSubmissionOfEvent($event_id, $matchtype_id){
		$ret = array();
		$data = Eventregistration::getAllRegistrations($event_id)
		->join("users", "users.id", "=", "eventregistrations.user_id")
		->leftJoin("userpoints", function($join) use ($matchtype_id){
										$join->on("userpoints.user_id", "=", "eventregistrations.user_id")
										->on("userpoints.matchtype_id", "=", DB::raw($matchtype_id));
									})
		->groupBy("eventregistrations.user_id")
		->select(
			"eventregistrations.*",
			"users.name",
			"users.avatar",
			DB::raw("IF(SUM(userpoints.pointschange)+users.basePoints > 0, SUM(userpoints.pointschange)+users.basePoints, users.basePoints) as points")
			)
		->orderBy("eventregistrations.created_at", "ASC");

		$ret['data'] = $data->get();
		// $query = DB::getQueryLog();
		// die($query[38]['query']);
		$ret['count'] = count($ret['data']);

		return $ret;
	}

	public static function insertRegistration($event_id, $user_id){
		$insertArray = array();
		$insertArray['user_id'] = $user_id;
		$insertArray['event_id'] = $event_id;
		$insertArray['created_at'] = new DateTime;

		Eventregistration::insert($insertArray);
	}

	public static function signOut($event_id, $user_id){
		Eventregistration::where("event_id",$event_id)->where("user_id", $user_id)->delete();
	}
}
