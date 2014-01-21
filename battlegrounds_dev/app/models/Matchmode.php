<?php

class Matchmode extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public function gamequeues(){
		return $this->hasMany("Gamequeue", "matchmode_id");
	}

	public static function getQuickJoinModes(){
		$matchmode_id = GlobalSetting::getQuickJoinMatchmode();
		if($matchmode_id > 0){
			$mm = Matchmode::where("id", $matchmode_id);
		}
		else{
			$mm = null;
		}
		
		return $mm;
	}

	public static function getAllActiveModes(){
		$modes = Matchmode::where("active", 1)->remember(60);
		return $modes;
	}

	public static function getMatchmodeData($matchmode_id){
		$modes = Matchmode::where("id", $matchmode_id)->remember(60);
		return $modes;
	}

	public static function getMostMatchmodesPlayedOfUser($user_id, $limit=6){
		$matchesPlayedCount = Match::getMatchesPlayedCount($user_id);
		$data = array();
		if($matchesPlayedCount > 0){
			$data = Match::join("matchdetails", "matchdetails.match_id", "=", "matches.id")
				->join("matchmodes", "matchmodes.id", "=", "matches.matchmode_id")
				->where("matchdetails.user_id", $user_id)
				->where("matchmodes.active", 1)
				->where("matches.canceled", 0)
				->where("matches.team_won_id","!=", 0)
				->groupBy("matches.matchmode_id")
				->orderBy("value", "desc")
				->take($limit)
				->select("matches.matchmode_id",
					DB::raw("CAST((Count(matches.matchmode_id)/".(int) $matchesPlayedCount.")*100 AS DECIMAL(12,2)) as value"),
					"matchmodes.name as matchmode",
					"matchmodes.shortcut as mm_shortcut"
					);
			;
		}
		return $data;
	}
}
