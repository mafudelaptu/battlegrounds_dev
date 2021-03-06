<?php

class Matchhost extends Eloquent {
	protected $guarded = array();
	protected $table = 'matchhosts';
	public static $rules = array();

	public $timestamps = false;

	public static function deleteHost($match_id){
		return DB::table("matchhosts")->where("match_id", $match_id)
						->delete();
	}

	public static function setHost($match_id, $users=false){
		if($users != false){
			if(!empty($users)){
				$rand_key = array_rand($users);
				$user_id = $users[$rand_key]['user_id'];
			}
		}
		else{
			$randomUser = Matchdetail::getMatchdetailData($match_id)->orderBy(DB::raw("RAND()"))->first();
			$user_id = $randomUser->user_id;
		}

		$insArray = array();
		$insArray['match_id'] = $match_id;
		$insArray['user_id'] = $user_id;
		try{

		DB::table("matchhosts")->insert($insArray);
		}
		catch(Exception $e){
			return false;
		}
	}

	public static function getHost($match_id, $userData=false){
		if($userData == true){
			$ret = Matchhost::where("match_id", $match_id)
								->join("users", "users.id", "=", "matchhosts.user_id");
		}
		else{
			$ret = Matchhost::where("match_id", $match_id);
		}
		
		return $ret;
	}
}
