<?php

class Login extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function checkForumLogin($user_id, $pass){
		$ret = array();
		switch(GlobalSetting::getLoginVia()){
			case "Forum_IPBoard":
			if($user_id > 0 && $pass != ""){
				$data = DB::connection('forum')->table("members")
				->where("members.member_id",$user_id)
				->where("member_login_key", $pass)->first();
				if(!empty($data)){
					if($data->member_id > 0){
						$ret['status'] = true;
					}
				}
				else{
					$ret['status'] = false;

				}
			}
			else{
				$ret['status'] = "userID == 0 || pass == ''";
			}
			break;
		}
		return $ret;
	}

	public static function checkDotacinemaSteamID($user_id){
		$ret = array();
		
		if($user_id > 0){
			$data = DB::connection('dc')->table("steam_login")
			->where("user_id",$user_id)
			->first();

			if(!empty($data)){
				if($data->steam_id != ""){
					$ret['steam_id'] = $data->steam_id;
					$ret['status'] = true;
				}
			}
			else{
				$ret['steam_id'] = "";
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "userID == 0";
		}
		
		return $ret;
	}

	public static function insertNewUserAndLogin($user_id){
		$userData = User::find($user_id);
		//var_dump($userData);
		if(!empty($userData)){
			$user = User::find($user_id);
			switch (GlobalSetting::getLoginVia()) {
				case 'Steam':
				case "Forum_IPBoard":
				default:
					if(GlobalSetting::getForumHost() == "Dotacinema"){
						Login::updateSteamUser($user_id);
					}
					break;
			}
			//var_dump($user);
			Auth::login($user);

		}
		else{
			switch (GlobalSetting::getLoginVia()) {
				case 'Steam':
				case "Forum_IPBoard":
				default:
					if(GlobalSetting::getForumHost() == "Dotacinema"){
						// Create SteamId Object
						$steamIdObject = new SteamId( "$user_id" );

						$steam_avatar = $steamIdObject->getIconAvatarUrl();
						$steam_avatarFull = $steamIdObject->getFullAvatarUrl();
						
						$steam_name = $steamIdObject->getNickname();

						$date = new \DateTime;
						$user = new User;
						$user->id = $user_id;
						$user->name = $steam_name;
						$user->avatar = $steam_avatar;
						$user->avatarFull = $steam_avatarFull;
						$user->basePoints = GlobalSetting::getBasePoints();
						$user->basePointsUpdatedTimestamp = $date;
						$user->region_id = GlobalSetting::getDefaultRegionID();
						$user->save();

						$user = User::find($user_id);
					}
					break;
			}
			

			Auth::login($user);

			// set init uservotecounts
			Uservotecount::initUserVoteCounts($user->id);
		}

		// set first Skillbrackets
		Userskillbracket::setSkillbrackets($user_id);
		// set init uservotecounts
		Uservotecount::initUserVoteCounts($user->id);
	}

	public static function updateSteamUser($user_id){
		// Create SteamId Object
		$steamIdObject = new SteamId( "$user_id" );

		$steam_avatar = $steamIdObject->getIconAvatarUrl();
		$steam_avatarFull = $steamIdObject->getFullAvatarUrl();
		
		$steam_name = $steamIdObject->getNickname();

		$updateArray = array(
			"name"=>$steam_name,
			"avatar"=>$steam_avatar,
			"avatarFull"=>$steam_avatarFull,
			"updated_at"=>new DateTime,
			);
		User::where("id", $user_id)->update($updateArray);
	}
}
