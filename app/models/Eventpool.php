<?php

class Eventpool extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function insertWonTeamIntoPool($event_id, $created_event_id, $round, $teamData, $team_id){
		if(!empty($teamData)){
			$insertArray = array();
			foreach($teamData as $k => $user){
				
				$tmpArray = array(
					"event_id"=>$event_id,
					"created_event_id"=>$created_event_id,
					"round"=>$round,
					"user_id"=>$user->user_id,
					"points"=>$user->points,
					"created_at"=>new DateTime,
					"team_id"=>$team_id,
					);
				$insertArray[] = $tmpArray;
			}
			
		}
		// cancel wert eintragen
		else{
			$tmpArray = array(
					"event_id"=>$event_id,
					"created_event_id"=>$created_event_id,
					"round"=>$round,
					"user_id"=>rand(-999999, -1),
					"points"=>0,
					"created_at"=>new DateTime,
					"team_id"=>$team_id,
					);
			$insertArray[] = $tmpArray;
		}
		try{

				Eventpool::insert($insertArray);
			}
			catch(Exception $e){
				return false;
			}
	}

	public static function checkPoolEnoughPlayerForRound2($event_id, $created_event_id){
		$ret = array();
		$poolData = Eventpool::where("round", 2)
		->where("event_id", $event_id)
		->where("created_event_id", $created_event_id)
		;
		$data = $poolData->get();
		$count = count($data);
		$retData = array();
		// prepare for seperate10PlayersInto2BalancedTeams
		if(!empty($data) && count($data)>0){
			foreach ($data as $key => $v) {
				$tmp = array(
					"user_id" => $v->user_id,
					"points" => $v->points,
					"event_id" => $v->event_id,
					"created_event_id" => $v->created_event_id,
					"round" => $v->round,
					"created_at" => $v->created_at,
					"updated_at" => $v->updated_at,
					);
				$retData[] = $tmp;
			}
		}
		$ret['data'] = $retData;
		$ret['count'] = (int)$count;
		$ret['status'] = true;

		return $ret;
	}

	public static function checkTeamAlreadyInPool($team_id){

	}
}


