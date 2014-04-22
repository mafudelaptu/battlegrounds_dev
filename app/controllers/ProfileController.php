<?php

class ProfileController extends BaseController {

	protected $layout = "master";
	protected $title = "Profile";

	public function showProfile($user_id){
		$this->layout->title = $this->title;
		
		if($user_id == Auth::user()->id){
			$visitor = false;
		}
		else{
			$visitor = true;
		}
		
		// get Userdata
		$userData = User::getUserData($user_id)->first();
		$statsOfUser = User::getStatsOfUser($user_id);
		$credits = Usercredit::getCreditCount($user_id);

		$nextSkillbracket = User::getRequirementsForNextSkillbracket($user_id, $statsOfUser['stats'], $credits);
		$lastMatches = Match::getLastMatches($user_id, 6);

		// all warns
		$warns = Banlist::getAllBans($user_id)->join("banlistreasons", "banlistreasons.id", "=", "banlists.banlistreason_id")
						->leftJoin("users", "users.id", "=", "banlists.bannedBy")
						->select(
							"banlists.*",
							DB::raw("DATE_ADD(banlists.created_at, INTERVAL ".GlobalSetting::getBanDecayTime()." second) as expires"),
							"users.avatar as bannedByAvatar",
							"users.name as bannedByName",
							"banlistreasons.name as banReasonText"
							)->get();



		$contentData = array(
			"visitor" => $visitor,
			"userData" => $userData,
			"points" => $statsOfUser['points'],
			"stats" => $statsOfUser['stats'],
			"lastMatches" => $lastMatches['data'],
			"lastMatchesLeaverArray" =>  $lastMatches['leaverArray'],
			"credits" => $credits,
			"matchmodes" => Matchmode::getAllActiveModes()->get(),
			"matchtypes" => Matchtype::getAllActiveMatchtypes()->get(),
			"nextSkillbracket" => $nextSkillbracket['data'],
			"skillbracket" => Userskillbracket::getSkillbracketsAsArray($user_id, true),
			"activeBansCount" => Banlist::getAllActiveBans($user_id)->count(),
			"allBansCount" => Banlist::getAllBans($user_id)->count(),
			"bansData" => $warns,
			"bestRankings" => null,
			);

		// replay Data
		$ru = GlobalSetting::getReplayUpload();
		$contentData['replayUploadActive'] = $ru;
		$contentData['matchModesStatsData'] = null;
		$contentData['heroesStatsData'] = null;
		$contentData['gameStats'] = null;
		$contentData['matchStats'] = null;
		if($ru){
			switch($ru){
				case "Dota2":
					$mostMatchmodesPlayed = Matchmode::getMostMatchmodesPlayedOfUser($user_id, 6)->get();
		// $query = DB::getQueryLog();
		// die($query['63']['query']);
					$retMDR = Matchreplay_dota2::getMostPlayedHeroesOfUser($user_id);

					$mostPlayedHeroes = $retMDR['data'];
					//dd($mostPlayedHeroes);
					$gameStats = Matchreplay_dota2::getGameStatsOfUser($user_id);

					$matchStats = Matchreplay_dota2::getMatchStatsOfUser($user_id);

					$contentData['matchModesStatsData'] = $mostMatchmodesPlayed;
					$contentData['heroesStatsData'] = $mostPlayedHeroes;
					$contentData['gameStats'] = $gameStats['data'];
					$contentData['matchStats'] = $matchStats['data'];
					break;
			}
		}

		// get Permabans
		$contentData['permaban'] = PermaBan::getPermaBanOfUser($user_id)->first();

		//perm($contentData);
		$this->layout->nest("content", 'profile/index', $contentData);
	}

	public function syncWithSteam(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$user_id = Auth::user()->id;

				$steamIdObject = new SteamId( "$user_id" );
				$steamIdObject->fetchData();
				$steam_avatar = "";
				$steam_avatarFull = "";
				$steam_name = "Steam connection failed";
				if($steamIdObject->isFetched()){
					$steam_avatar = $steamIdObject->getIconAvatarUrl();
					$steam_avatarFull = $steamIdObject->getFullAvatarUrl();
					$steam_name = $steamIdObject->getNickname();

					// update data
					User::updateUserData($user_id, $steam_name, $steam_avatar, $steam_avatarFull);
					$ret['status'] = true;
				}
				else{
					dd($steamIdObject->fetchTime);
					$ret['status'] = false;
				}

			}
		}
		return $ret;
	} 
}
