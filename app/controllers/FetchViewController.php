<?php

class FetchViewController extends BaseController {

	public function getMMInfo(){
		if(Auth::check()){
				$modes = Input::get("modes");
				$objModes = json_decode(json_encode($modes), FALSE);
				//var_dump($modes);
				$content = View::make('findMatch/modals/matchmaking/matchmaking')->with("modes",$objModes)->render();
				//var_dump($content);
				return Response::json(array("html"=>$content));
				//return View::make("findMatch/modals/mmInfo");
		}
		
	}

	public function getReadyMatch(){
		if(Auth::check()){
				//var_dump($modes);
				$content = View::make('findMatch/modals/matchReady')->render();
				//var_dump($content);
				return Response::json(array("html"=>$content));
		}
	}	

	public function getWaitingForOtherUsers(){
		if(Auth::check()){
				$matchtype_id = Input::get("matchtype_id");
				switch ($matchtype_id) {
					case 2:
					$content = View::make('findMatch/modals/waitingForOtherUsers1vs1')->render();
					break;
					
					default:
					$content = View::make('findMatch/modals/waitingForOtherUsers')->render();
					break;
				}
				
				return Response::json(array("html"=>$content));
			}
	}

	public function matchSubmitModal(){
		if(Auth::check()){
				$match_id = Input::get("match_id");
				//dd($match_id);
				$matchData = Match::getMatchData($match_id)->first();
				$matchdetails = Matchdetail::getMatchdetailData($match_id, true, false)->orderBy("matchdetails.points")->get();
				$players = Match::getPlayersData($matchdetails);
				$content = View::make('matches/modals/submitResult')
				->with("players", $players)
				->with("matchData", $matchData)
				->render();

				

				return Response::json(array("html"=>$content, "matchtype_id"=>$matchData->matchtype_id));
		}
	}

	public function matchCancelModal(){
		if(Auth::check()){
				$match_id = Input::get("match_id"); // dc fix for getting match_id

				$matchdetails = Matchdetail::getMatchdetailData($match_id, true, false)->orderBy("matchdetails.points")->get();
				$players = Match::getPlayersData($matchdetails);

				$content = View::make('matches/modals/cancelMatch')
				->with("players", $players)
				->render();

				return Response::json(array("html"=>$content));
			
		}
	}	

	public function replayUploadModal(){
		if(Auth::check()){
				$content = View::make('matches/modals/replayUpload')->render();
				return Response::json(array("html"=>$content));
			
		}
	}

	public function getChatUsers(){
		if(Auth::check()){
				$users = Input::get("users");
				$chatname = Input::get("chat");

				if(!empty($users) && count($users) > 0){
					foreach($users as $k => $v){
						$users[$k]['user_id'] = $k;
						$users[$k]['admin'] = User::isAdmin($k);
					}
					// sortieren nach key
					asort($users);
				} 

				$isSteamGame = GlobalSetting::isSteamGame();
				
				$count = count($users);
				$content = View::make('prototypes/chat/chatUsers')
				->with("data",$users)
				->with("chatname",$chatname)
				->with("isSteamGame",$isSteamGame)
				->render();
				return Response::json(array("html"=>$content, "count"=>$count));
			
		}
	}

	// public function getChatUser(){
	// 	if(Auth::check()){
	// 		if (Request::ajax()){
	// 			$user = Input::get("user");
	// 			$chatname = Input::get("chat");
	// 			$admin = User::isAdmin($user['user_id']);

	// 			$isSteamGame = GlobalSetting::isSteamGame();

	// 			$count = count($users);
	// 			$content = View::make('prototypes/chat/chatUsers')
	// 			->with("data",$users)
	// 			->with("chatname",$chatname)
	// 			->with("isSteamGame",$isSteamGame)
	// 			->with("rooms", $rooms)
	// 			->render();
	// 			return Response::json(array("html"=>$content, "count"=>$count));
	// 		}
	// 	}
	// }

	public function getMessage(){
		if(Auth::check()){
				$user_id = Input::get("user_id"); // dc fix for getting match_id

				$contentArray = array(
					"user_id" => $user_id,
					"username" => Input::get("username"),
					"avatar" => Input::get("avatar"),
					"time" => Input::get("time"),
					"msg" => Input::get("msg"),
					"whisper"=>Input::get("whisper"),
					"whisper_user_id"=>Input::get("whisper_user_id"),
					"whisper_username"=>Input::get("whisper_username"),
					"whisper_avatar"=>Input::get("whisper_avatar"),
					);

				$content = View::make('prototypes/chat/chatMessage')->with("data",$contentArray)->render();
				return Response::json(array("html"=>$content));
			
		}
	}

	public function getTabContent(){
		if(Auth::check()){
				$room = Input::get("room");
				$chat = Input::get("chat");
				$height = Input::get("height");
				$active = Input::get("active");

				$content = View::make('prototypes/chat/tabContent')
				->with("room",$room)
				->with("chatname",$chat)
				->with("height",$height)
				->with("active",$active)
				->render();
				return Response::json(array("html"=>$content));
			
		}
	}

	public function getScaffoldMessageAndChatuser(){
		if(Auth::check()){
				$user = Input::get("user");
				$chat = Input::get("chat");

				$user_id = $user['user_id'];
				$username = $user['username'];
				$avatar = $user['avatar'];

				// Msg
				$contentArray = array(
					"user_id" =>$user_id,
					"username" => $username,
					"avatar" => $avatar,
					"time" => "",
					"msg" => "",
					);
				$msg = View::make('prototypes/chat/chatMessage')->with("data",$contentArray)->render();
				$ret['msg'] = $msg;

				// Chatuser
				$isSteamGame = GlobalSetting::isSteamGame();
				$user['admin'] = User::isAdmin($user_id);
				$ret['admin'] = $user['admin'];
				
				$chatuser = View::make('prototypes/chat/chatUser')
				->with("data",$user)
				->with("chatname",$chat)
				->with("isSteamGame",$isSteamGame)
				->render();
				
				$ret['chatuser'] = $chatuser;
			
		}
		return $ret;
	}

	public function getWhisperModeInputAddon(){
		if(Auth::check()){
				$user_id = Input::get("user_id");
				$username  = Input::get("username");
				$avatar =  Input::get("avatar");
				$html = View::make('prototypes/chat/whisperInputAddon')
				->with("name",$username)
				->with("user_id",$user_id)
				->with("avatar",$avatar)
				->render();

				$ret['html'] = $html;
			
		}
		return $ret;
	}

	public function getBansOfUser(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$user_id = Input::get("user_id");

				$all_bans = Banlist::getAllBans($user_id);
				$active_bans = $all_bans->where("display", 1)->get();
				$old_bans = $all_bans->where("display", 0)->get();

				$html_active = View::make('admin.bans.bansTable')
				->with("data",$active_bans)
				->with("id","bansTableActive")
				->with("title","Active Bans/Warns")
				->render();

				$html_old = View::make('admin.bans.bansTable')
				->with("data",$old_bans)
				->with("id","bansTableOld")
				->with("title","Old Bans/Warns")
				->render();
				$ret['html_active'] = $html_active;
				$ret['html_old'] = $html_old;
				$ret['status'] = true;
			}
		}
		return $ret;
	}
}
