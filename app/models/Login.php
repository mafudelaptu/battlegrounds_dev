<?php

class Login extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public static function checkForumLogin($user_id, $pass, $loginVia=null){
		$ret = array();
		($loginVia==null) ? $loginVia = GlobalSetting::getLoginVia() : $loginVia = $loginVia;
		switch($loginVia){
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
		$ret['steam_id'] = "";
		$ret['status'] = false;
		
		if($user_id > 0){
			$data = DB::connection('dc')->table("steam_login")
			->where("user_id",$user_id)
			->first();

			if(!empty($data)){
				if($data->steam_id_64 != ""){
					$ret['steam_id'] = $data->steam_id_64;	
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
					$steamIdObject->fetchData();
					$steam_avatar = "";
					$steam_avatarFull = "";
					$steam_name = "Steam connection failed";

					if($steamIdObject->isFetched()){
						$steam_avatar = $steamIdObject->getIconAvatarUrl();
						$steam_avatarFull = $steamIdObject->getFullAvatarUrl();
						$steam_name = $steamIdObject->getNickname();
					}


					$date = new \DateTime;
					$user = new User;
					$user->id = $user_id;
					$user->name = $steam_name;
					$user->avatar = $steam_avatar;
					$user->avatarFull = $steam_avatarFull;
					$user->basePoints = GlobalSetting::getBasePoints();
					$user->basePointsUpdatedTimestamp = $date->getTimestamp();
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
		if(!empty($steamIdObject->fetchTime)){
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

	public static function handleForumIPBoardLogin(){
		$loginVia = "Forum_IPBoard";
		$forumActive = GlobalSetting::getForumActive();
		// check Login first
		(isset($_COOKIE['member_id'])) ? $user_id = $_COOKIE['member_id'] : $user_id = 0;
		(isset($_COOKIE['pass_hash'])) ? $pass = $_COOKIE['pass_hash'] : $pass = "";
		$check = Login::checkForumLogin($user_id, $pass, $loginVia);
			//dd($check);

		switch(GlobalSetting::getForumHost()){
			case "Dotacinema":
			$redirect = Login::handleDotacinemaLogin($forumActive, $check['status'], $user_id, $pass);
			break;
			case "IHearthU":
			$redirect = Login::handleIHearthULogin($forumActive, $check['status'], $user_id, $pass);
			break;
		}
		return $redirect;
	}

	public static function handleDotacinemaLogin($checkForumActive, $checkForumLogin, $member_id, $pass_hash){
		$redirectEnter = "/";
		if($_SERVER['ENVIRONMENT'] === 'production'){
			$redirectLogin = 'http://www.dotacinema.com/arena_login';
			$redirectComingSoon = 'http://www.dotacinema.com/arena_comingsoon';
			$redirectActivateBetaKey = "http://www.dotacinema.com/arena_key_activate";
		}else{
			$redirectLogin = 'http://'.$_SERVER['SERVER_NAME'].'/arena_login';
			$redirectComingSoon = 'http://'.$_SERVER['SERVER_NAME'].'/arena_comingsoon';
			$redirectActivateBetaKey = 'http://'.$_SERVER['SERVER_NAME'].'/arena_key_activate';
		}
		$redirectForumLogin = "start_forumipboard";

		$redirect = $redirectLogin;

		
		if(!empty($member_id) && !empty($pass_hash)){
			if($checkForumLogin){

				$checkSteamID = Login::checkDotacinemaSteamID($member_id);
				if($checkForumActive){
					// login via beta-key active?
					if(GlobalSetting::getLoginViaKey()){
						if(Login::checkLoginViaKey($member_id)){
							if($checkSteamID['steam_id'] != "" && $checkSteamID['status'] == true){
								Login::insertNewUserAndLogin($checkSteamID['steam_id']);
								$redirect = $redirectEnter;
							}
						}
						else{
							$redirect = $redirectActivateBetaKey;
						}
					}
					else{
						if($checkSteamID['steam_id'] != "" && $checkSteamID['status'] == true){
							Login::insertNewUserAndLogin($checkSteamID['steam_id']);
							$redirect = $redirectEnter;
						}
					}
				}
				else{
					if(User::isAdmin($checkSteamID['steam_id'])){
						Login::insertNewUserAndLogin($checkSteamID['steam_id']);
						$redirect = $redirectEnter;
					}
					else{
						$redirect = $redirectComingSoon;
					}
				}
			}
		}
		else{
			$redirect = $redirectForumLogin;
		}

		return $redirect;
	}

	public static function handleIHearthULogin($checkForumActive, $checkForumLogin, $member_id, $pass_hash){
		$redirectEnter = "/";
		if($_SERVER['ENVIRONMENT'] === 'production'){
			$redirectFalse = 'http://www.dotacinema.com/arena_login';
		}else{
			$redirectFalse = 'http://'.$_SERVER['SERVER_NAME'].'/arena_login';
		}
		$redirect = $redirectFalse;

		if($checkForumLogin){
			//dd(GlobalSetting::getForumActive());
			$checkSteamID = Login::checkDotacinemaSteamID($user_id);
			if($checkSteamID['steam_id'] != "" && $checkSteamID['status'] == true){
				Login::insertNewUserAndLogin($checkSteamID['steam_id']);
				$redirect = $redirectEnter;
			}
			else{
				$redirect = $redirectFalse;
			}
		}
		else{
			$redirect = $redirectFalse;
		}

		return Redirect::to($redirect);
	}

	public static function checkLoginViaKey($user_id){
		$data = DB::connection('dc')->table("arena_beta_keys")
		->where("used_by_id",$user_id)
		->first();
		if(!empty($data) && count($data)>0){
			return true;
		}
		else{
			return false;
		}
	}

}
