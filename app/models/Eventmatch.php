<?php

class Eventmatch extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function insertEventMatchToEvent($event_id, $created_event_id, $match_id, $round, $team1, $team2, $match_number){
		$insertArray = array(
			"event_id" => $event_id,
			"created_event_id" => $created_event_id,
			"match_id" => $match_id,
			"round" => $round,
			"team1" => $team1,
			"team2" => $team2,
			"created_at" => new DateTime,
			"match_number" => $match_number,
			);
		try{
			Eventmatch::insert($insertArray);
		}
		catch(Exception $e){
			return false;
		}
	}

	public static function getAllFinishedEventMatches(){
		return Match::join("eventmatches", "eventmatches.match_id", "=", "matches.id")
		->join("events", "events.id", "=",  "eventmatches.event_id")
		->where(function($where){
			$where->where(function($where2){
				$where2->where("matches.team_won_id","!=", 0)
				->where("matches.closed", "!=", "0000-00-00 00:00:00");
			})
			->orWhere("matches.canceled", 1)
			->orWhere("matches.check", 1);
		})
		->where("eventmatches.team_won_id", 0)
		->where("events.ended_at", "0000-00-00 00:00:00");
	}

	public static function getMatchDataByMatchID($event_id, $created_event_id, $match_id){
		return Eventmatch::where("event_id", $event_id)
		->where("created_event_id",$created_event_id)
		->where("match_id",$match_id);
	}

	public static function updateEventMatchesTeamWonID($event_id, $created_event_id, $match_id, $team_won_id){
		$updateArray = array(
			"team_won_id"=>$team_won_id,
			"updated_at" => new DateTime,
			);
		Eventmatch::getMatchDataByMatchID($event_id, $created_event_id, $match_id)->update($updateArray);
	}

	public static function getAllPlayedMatches($matchtype_id=1){

		return Eventmatch::join("events", "events.id", "=", "eventmatches.event_id")
		->join("created_events", "created_events.id", "=", "eventmatches.created_event_id")
		->join("eventtypes", "eventmatches.event_id", "=", "eventtypes.id")
		->join("matchtypes", "matchtypes.id", "=", "eventtypes.matchtype_id")
		->where("events.ended_at", "0000-00-00 00:00:00")
		->where("events.canceled", 0)
		->where("created_events.canceled", 0)
		->where("created_events.ended_at", "0000-00-00 00:00:00")
		//->where("eventtypes.matchtype_id", $matchtype_id)
		->select("eventmatches.event_id", "eventmatches.created_event_id", 
			"eventmatches.team_won_id", "eventtypes.min_submissions","matchtypes.playercount")
		->distinct()
		;
	}

	public static function getEventmatches($event_id, $created_event_id){
		return Eventmatch::where("event_id", $event_id)->where("created_event_id", $created_event_id);
	}

	public static function getEventMatchesStatus($event_id, $created_event_id){
		$ret = array();
		$data = Eventmatch::getEventmatches($event_id, $created_event_id)
		->where("team_won_id" > 0)
		->get();
		if(!empty($data)){
			$retArray = array();
			foreach($data as $k => $v){
				$tmpArray = array(
					"team1" => $v->team1,
					"team2" => $v->team2,
					"team_won_id" => $v->team_won_id,
					);

				$retArray[$v->round][] = $tmpArray;
			}
			$ret['data'] = $retArray;
			$ret['status'] = true;
		}
		return $ret;
	}

	public static function checkIfRound2AlreadyCreated($event_id, $created_event_id){
		$ret = array();
		$count = Eventmatch::getEventmatches($event_id, $created_event_id)
		->where("round", 2)
		->where("team1", 5)
		->where("team2", 6)
		->count();
		if($count > 0){
			$ret['status'] = true;
		}
		else{
			$ret['status'] = false;
		}
		return $ret;
	}

	public static function getMatchData($event_id, $created_event_id, $team_id){
		return Eventmatch::where("event_id", $event_id)
		->where("created_event_id", $created_event_id)
		->where(function($where) use ($team_id){
			$where->where("team1", $team_id)->orWhere("team2", $team_id);
		});
		;

	}

}
