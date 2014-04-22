<?php

class Match extends Eloquent {
	protected $guarded = array();


	public static $rules = array();

	protected $table = 'matches';
	protected $primaryKey = 'id';

	public static function getAllMatchesPlayed(){
		return Match::where("canceled", 0)
		->where("check", 0)
		->where("team_won_id","!=",0)
		->where("closed","!=","0000-00-00 00:00:00");
	}

	public static function getAllLiveMatches(){
		return Match::join("matchdetails", "matchdetails.match_id", "=", "matches.id")->where("canceled", 0)
		->where("check", 0)
		->where("team_won_id",0)
		->where("closed","0000-00-00 00:00:00")
		->groupBy("matchdetails.match_id")
		->select("matches.*", DB::raw("COUNT(matchdetails.submissionFor) as submits"))
		->where("matchdetails.submissionFor", "0")
		->having("submits", "=", "10");
	}

	public static function getAllOpenMatches(){
		return Match::where("canceled", 0)
		->where("team_won_id",0)
		->where("check", 0)
		->where("closed","0000-00-00 00:00:00");
	}

	public static function getAllOpenMatchesOfUser($user_id){
		$ret = array();

		$data = Match::getAllOpenMatches()->join("matchdetails", "matchdetails.match_id", "=", "matches.id")
		->leftJoin("matchvotes", function($join) use ($user_id){
			$join->on("matchvotes.match_id", "=", "matches.id")
			->on("matchvotes.user_id", "=", DB::raw($user_id))
			->on("matchvotes.matchvotetype_id", "=", DB::raw(1));
		})
		->where("matchdetails.user_id", $user_id)
		->select(
			"matches.*",
			"matchdetails.submitted",
			DB::raw("IF(COUNT(matchvotes.user_id) > 0, COUNT(matchvotes.user_id), null) as cancelSubmits")
			);
						//->having("cancelSubmits", ">", 0)
		
		
		return $data;
	}

	public static function isUserInMatch($user_id, $match_id=0){
		if($match_id > 0){
			$user = Matchdetail::where("match_id", $match_id)->where("user_id", $user_id)->first();
			if(!empty($user)){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			$user = DB::table("matches")->join('matchdetails', 'matches.id', '=', 'matchdetails.match_id')
			->where("matchdetails.user_id", $user_id)
			->where("matchdetails.submitted", "0")
			->where("matchdetails.submissionFor", "0")
			->where("matches.team_won_id", "0")
			->where("matches.canceled", "0")
			->where("matches.check", "0")->get();

			if(!empty($user)){
				return true;
			}
			else{
				return false;
			}
		}
	}

	public static function deleteCreatedMatch($match_id){
		return DB::table("matches")->where("id", $match_id)
		->delete();
	}

	public static function createNewMatch($matchtype_id, $matchmode_id, $region_id){
		$insertArray = array();
		$insertArray['team_won_id'] = 0;
		$insertArray['matchtype_id'] = $matchtype_id;
		$insertArray['matchmode_id'] = $matchmode_id;
		$insertArray['region_id'] = $region_id;
		$insertArray['check'] = 0;
		$insertArray['canceled'] = 0;
		$insertArray['closed'] = 0;
		$insertArray['created_at'] = new DateTime;
		$insertArray['updated_at'] = new DateTime;

		try{
			$id = DB::table("matches")->insertGetId($insertArray);
		}
		catch(Exception $e){
			$id = 0;
		}

		return $id;
	}

	public static function getStateOfMatch($match_id, $user_id){
		$ret = array();

		$matchData = Match::getMatchData($match_id)->first();

		if(!empty($matchData)){
			if($matchData->team_won_id === 0){
				$submitted = Matchdetail::checkResultSubmitted($match_id, $user_id);				
				
				if($submitted){
					if($matchData->canceled == "1"){

						$ret['status'] = "canceled";
					}
					else{
						
						$ret['status'] = "submitted";
					}
				}
				else{
					$ret['status'] = "open";
				}
			}
			elseif($matchData->team_won_id > 0){
				$ret['status'] = "closed";
			}
		}
		else{
			$ret['status'] = "noMatch";
		}
		return $ret;
	}

	public static function getMatchData($match_id, $matchmodeData=false, $regionData=false, $matchtypeData=false){
		$ret = Match::where("matches.id", $match_id)->select("matches.*");
		if($matchmodeData){
			$ret = $ret->join("matchmodes", "matchmodes.id", "=", "matches.matchmode_id");
		}
		if($regionData){
			$ret = $ret->join("regions", "regions.id", "=", "matches.region_id");
		}
		if($matchtypeData){
			$ret = $ret->join("matchtypes", "matchtypes.id", "=", "matches.matchtype_id");
		}

		return $ret;
	}

	public static function getPlayersData($matchdetailsData, $matchtype_id=0){
		$ret = array();
		if(!empty($matchdetailsData)){
			foreach ($matchdetailsData as $key => $detail) {
				$tmp = array();
				$user_id = $detail['user_id'];
				$match_id = $detail['match_id'];

				$tmp["user_id"] = $user_id;
				$tmp["name"] = $detail->name;
				$tmp["avatar"] = $detail->avatar;
				$tmp['points'] = (int) $detail->points;
				$tmp['team_id'] = $detail->team_id;
				$tmp['pointschange'] = $detail->pointschange;

				if($matchtype_id > 0){					

					$gameStats = Userpoint::getGameStats($user_id, $matchtype_id);
					
					$skillbracketRet = Userskillbracket::getSkillbracket($user_id, $matchtype_id, true)->first();
					if(!empty($skillbracketRet) && count($skillbracketRet)>0){
						$skillbracket_id = $skillbracketRet->id;
					}
					else{
						$skillbracket_id = 2;
					}
					$skillbrackettypeData = Skillbrackettype::getData($skillbracket_id)->first();

					$tmp["stats"] = $gameStats['data'];
					$tmp['winPoints'] = $skillbrackettypeData->winpoints;
					$tmp['losePoints'] = $skillbrackettypeData->losepoints;
					$tmp['credits'] = Usercredit::getCreditCount($user_id);

					$leaverRet = Match::playerLeftTheMatch($user_id, $match_id);
					$tmp['leaver'] = $leaverRet['leaver'];
				}

				$ret[$detail->team_id][] = $tmp;
			}
		}
		return $ret;
	}
	public static function getAveragePointsOfTeams($matchdetailsData, $matchtype_id){
		$ret = array("team_1"=>0, "team_2"=>0);
		if(!empty($matchdetailsData) && $matchtype_id > 0){
			$rank_team = 0;
			for($i=1; $i<=2; $i++){
				$elo_sum = 0;

				foreach($matchdetailsData as $k => $v){
					if($v->team_id == $i){

						$elo_sum += $v->points;
					}
				}
				switch($matchtype_id){
					case "2":
					$rank_team = (int) $elo_sum;
					break;
					default:
					$rank_team = (int) $elo_sum / 5;
				}

				$ret['team_'.$i]= round($rank_team,0);
			}
		}
		return $ret;
	}

	public static function setMatchToManuallyCheck($match_id){
		$updateArray = array(
			"check" => 1,
			);

		return Match::where("id", $match_id)->update($updateArray);
	}

	public static function setTeamWon($match_id, $team_won_id){
		$updateArray = array(
			"team_won_id" => $team_won_id,
			"closed" => new DateTime(),
			);
		Match::where("id", $match_id)->update($updateArray);
	}

	public static function getLeaverTeamCounts($match_id, $matchdetails, $matchtype_id, $team_won_id){
		if(!empty($matchdetails)){
			$leaverArray = array(1=>0, 2=>0);
			$leaverUserArray = array();
			foreach ($matchdetails as $key => $md) {
				$team_id = $md->team_id;
				$user_id = $md->user_id;
				
				// check enough leaver votes
				$leaveData = Matchvote::getAllLeaverVotesForUser($match_id, $user_id)->groupBy("vote_for_user")
				->select("matchvotes.*", DB::raw("Count(vote_for_user) as leaveCount"))->first();

				if(!empty($leaveData)){
					switch ($matchtype_id) {
					case 2: // 1vs1
					if($leaveData->leaveCount >= 2){
						$leaverArray[$team_id]++;
						$leaverUserArray[] = $leaveData->vote_for_user;
					}
					break;
					default: // 5vs5
					if($leaveData->leaveCount >= 6){
						$leaverArray[$team_id]++;
						$leaverUserArray[] = $leaveData->vote_for_user;
					}
					break;
				}
			}
		}
		if($leaverArray[1] > 0 && $leaverArray[2] > 0 && count($leaverArray[1]) == count($leaverArray[2])){
			if($team_won_id == "1"){
				$leaverArray['handicapped'] = 2;
			}
			else{
				$leaverArray['handicapped'] = 1;
			}
		}
		else{

			if($leaverArray[1] > 0 || $leaverArray[2] > 0){
				$maxs = array_keys($leaverArray, max($leaverArray));
				$leaverArray['handicapped'] = $maxs[0];
			}
			else{
				$leaverArray['handicapped'] = false;
			}
		}
		$leaverArray['leaver'] = $leaverUserArray;

		return $leaverArray;
	}
}

public static function playerLeftTheMatch($user_id, $match_id){
	$ret = array();
	if($user_id > 0 && $match_id > 0){
		$leavercount = Matchvote::getAllLeaverVotesForUser($match_id, $user_id)->count();

		if($leavercount >= 6){
			$ret['leaver'] = true;
		}
		else{
			$ret['leaver'] = false;
		}

		$ret['status'] = true;
	}
	else{
		$ret['status'] = "steamID = 0 or matchid = 0";
	}

	return $ret;
}


public static function getLastMatches($user_id, $count=5){
	$ret = array();
	$data = Match::join("matchdetails", "matches.id", "=", "matchdetails.match_id")
	->leftJoin("userpoints", function($join) use ($user_id){
		$join->on("matches.id", "=", "userpoints.match_id")
		->on("userpoints.user_id", "=", DB::raw($user_id));
	})
	->join("matchmodes", "matchmodes.id", "=", "matches.matchmode_id")
	->join("matchtypes", "matchtypes.id", "=", "matches.matchtype_id")
	// ->where("userpoints.user_id", $user_id)
	->where("matchdetails.user_id", $user_id)
	->where("matches.closed", "!=", "0000-00-00 00:00:00")
	->where("matches.check", 0)
	->select("matches.*", 
		"matchdetails.team_id", 
		"matchmodes.name as matchmode", 
		"matchtypes.name as matchtype", 
		"matchmodes.shortcut as mm_shortcut", 
		"userpoints.pointschange")
	->orderBy("matches.id", "desc");
	if($count != "*"){
		$data = $data->take($count);
	}
	$data = $data->get();
	// $queries = DB::getQueryLog();
	// die($queries[54]['query']);

	if(!empty($data)){
		$leaverArray = array();
		foreach ($data as $key => $match) {
			$match_id = $match->id;
			$retLeaver = Match::playerLeftTheMatch($user_id, $match_id);
			$leaver = $retLeaver['leaver'];

			$leaverArray[$match->id] = $leaver;
		}
		$ret["data"] = $data;
		$ret['leaverArray'] = $leaverArray;
		$ret['status'] = true;
	}
	else{
		// dd(DB::getQueryLog());
	}
	return $ret;
}


public static function getGlobalLastMatches($count){
	$ret = array();
	$data = Match::join("matchmodes", "matchmodes.id", "=", "matches.matchmode_id")
	->join("matchtypes", "matchtypes.id", "=", "matches.matchtype_id")
	->where("matches.team_won_id", "!=", 0)
	->where("matches.canceled", 0)
	->where("matches.check", 0)
	->select("matches.*",  
		"matchmodes.name as matchmode", 
		"matchtypes.name as matchtype", 
		"matchmodes.shortcut as mm_shortcut")
	->orderBy("matches.id", "desc")
	->take($count);
	return $data;
}

public static function isEventMatch($match_id){
	$ret = array();
	$data = Eventmatch::where("match_id", $match_id)->first();
	if(!empty($data) && count($data) > 0){
		$ret['status'] = true;
		$ret['event_id'] = $data->event_id;
		$ret['created_event_id'] = $data->created_event_id;
	}
	else{
		$ret['status'] = false;
		$ret['event_id'] = 0;
		$ret['created_event_id'] =0;
	}
	return $ret;
}

public static function getMatchesPlayedCount($user_id){
	return Match::where("team_won_id","!=",0)
	->where("closed", "!=", "0000-00-00 00:00:00")
	->join("matchdetails", "matchdetails.match_id", "=", "matches.id")
	->where("user_id", $user_id)->count();
}

public static function resetMatchToOpenState($match_id, $deleteUserpoints=true){
	$updateArr = array(
		"team_won_id"=>0,
		"closed"=>"0000-00-00 00:00:00",
		"check"=>0,
		"canceled"=>0,
		"updated_at"=>new DateTime,
		);
	Match::where("id", $match_id)->update($updateArr);
	if($deleteUserpoints){
		Userpoint::deleteAllUserPointsToMatch($match_id);
	}
}

public static function getScreenshots($match_id){
	$ret = array();
	$ret['data'] = null;
	$ret['status'] = false;

	$url = GlobalSetting::getScreenshotUploadDestDir().$match_id."/";
	$alle = glob($url.'*');
	if(is_array($alle) && count($alle) > 0){
		$images = array();
		foreach ($alle as $k => $v) {
			$tmp = explode($url, $v);
			$imgName = $tmp[1];
			$path = $url.$imgName;

			$tmp = explode("_", $imgName, 2);
			$name = $tmp[1];
			$tmpImg = array(
				"src"=>URL::to("/")."/".$path,
				"realname"=>$imgName,
				"name"=>$name
				);
			$images[] = $tmpImg;
		}
		$ret['data'] = $images;
		$ret['status'] = true;
	}
	else{
		$ret['status'] = false;
	}
	return $ret;
}

}