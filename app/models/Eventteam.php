<?php

class Eventteam extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function insertPlayerIntoTeam($event_id, $created_event_id, $team_id, $user_id, $points, $round){
		if($event_id > 0 && $created_event_id > 0 && $team_id > 0 && $user_id > 0 && $round > 0){
			$insertArray = array(
				"event_id"=> $event_id,
				"created_event_id"=> $created_event_id,
				"user_id"=> $user_id,
				"eventteam_id"=> $team_id,
				"points"=> $points,
				"round"=> $round,
				"created_at"=> new DateTime,
				);
			try{
				Eventteam::insert($insertArray);
			}
			catch(Exception $e){
				return false;
			}
		}
		else{

		}
		
	}

	public static function getJustTeamsOfRound($event_id, $created_event_id, $round){
		return Eventteam::where("round", $round)
		->groupBy("eventteam_id")
		->select("eventteam_id")
		;
	}	

	public static function getTeam($event_id, $created_event_id, $team_id){
		return Eventteam::where("event_id", $event_id)
		->where("created_event_id", $created_event_id)
		->where("eventteam_id", $team_id)
		->select(
			"eventteams.*",
			DB::raw("IF(".$team_id."%2=0, 2, 1) as team_id")
			);
	}	

	public static function getFirstRoundTeams($event_id, $created_event_id){
		$ret = array();
		$teamsData = Eventteam::getJustTeamsOfRound($event_id, $created_event_id, 1);
		$teamCount = $teamsData->count();
		//dd($teamCount);
		if($teamCount > 0){
			$team_1 = 1;
			$team_2 = 2;
			for ($i=0; $i < $teamCount/2; $i++) { 

				$team1Data = Eventteam::getTeam($event_id, $created_event_id, $team_1)->get();
				$team2Data = Eventteam::getTeam($event_id, $created_event_id, $team_2)->get();
				if(!empty($team1Data) && count($team1Data) > 0 && !empty($team2Data) && count($team2Data) > 0){
					$retArray[$i][$team_1] = $team1Data;
					$retArray[$i][$team_2] = $team2Data;

					$team_1 = $team_1+2;
					$team_2 = $team_2+2;
				}
				
			}
			$ret['data'] = $retArray;
			$ret['status'] = true;
		}
		return $ret;
	}

	public static function getSecondRoundTeams($event_id, $created_event_id, $minSubmissions){
		$ret = array();
		$retArray = array();
		switch($minSubmissions){
			case "10":
			$retArray = array();
			$team1Data = Eventteam::getTeam($event_id, $created_event_id, 5)->get();
			$team2Data = Eventteam::getTeam($event_id, $created_event_id, 6)->get();
			$retArray[5] = $team1Data;
			$retArray[6] = $team2Data;
			break;
		}

		$ret['data'] = $retArray;
		$ret['status'] = true;
		
		return $ret;
	}

	public static function getTeamOfUser($event_id, $created_event_id, $round, $user_id){
		return Eventteam::where("event_id", $event_id)
		->where("created_event_id", $created_event_id)
		->where("user_id", $user_id)
		->where("round", $round)
		->join("users", "users.id", "=", "eventteams.user_id")
		;
	}

	public static function getTeamMembers($event_id, $created_event_id, $team_id){
		return Eventteam::join("users", "users.id", "=", "eventteams.user_id")
		->where("event_id", $event_id)->where("created_event_id", $created_event_id)
		->where("eventteam_id", $team_id)
		;
	}

	public static function getLastTeamOfUser($event_id, $created_event_id, $user_id){
		return Eventteam::where("event_id", $event_id)
		->where("created_event_id", $created_event_id)
		->where("user_id", $user_id)
		->join("users", "users.id", "=", "eventteams.user_id")
		->orderBy("eventteams.round", "desc")
		;
	}

}
