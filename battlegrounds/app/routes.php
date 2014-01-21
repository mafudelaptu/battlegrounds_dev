<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


Route::get("start", function(){
	$title = "Login Page";
	//dd("test");
	//dd(GlobalSetting::getLoginVia());
	switch (GlobalSetting::getLoginVia()) {
		case "Forum_IPBoard":
		if(GlobalSetting::getForumActive()){
			//dd($_COOKIE['member_id']." ".$_COOKIE['pass_hash']);
			if(!empty($_COOKIE['member_id']) && !empty($_COOKIE['pass_hash'])){
			// check Login first
				$user_id = $_COOKIE['member_id'];
				$pass = $_COOKIE['pass_hash'];
				$check = Login::checkForumLogin($user_id, $pass);
			//dd($check);
				if($check['status']){
					switch(GlobalSetting::getForumHost()){
						case "Dotacinema":
						//dd(GlobalSetting::getForumActive());
						$checkSteamID = Login::checkDotacinemaSteamID($user_id);

						if($checkSteamID['steam_id'] != "" && $checkSteamID['status'] == true){
							Login::insertNewUserAndLogin($checkSteamID['steam_id']);
							return Redirect::to("/");
						}
						else{
								//dd("asd");
								// redirect zu add-steam_id-seite
							return Redirect::to('http://'.$_SERVER['SERVER_NAME'].'/arena_login');

						}
						break;
					}
				}
				else{

				}
			}
			else{

				return View::make("start.forum_ipboard")->with("title", $title);
			}
		}
		else{
			return Redirect::to('http://'.$_SERVER['SERVER_NAME'].'/arena_comingsoon');
		}
		
		break;
		case "Steam":
		default:
		return View::make("start.index")->with("title", $title);
		break;
	}
});

//Help
Route::get('help/faq', 'HelpController@showFAQ');
Route::get('help/rules', 'HelpController@showRules');


Route::group(array('before' => 'auth|setSkillbracket'), function()
{

	Route::get('/', 'HomeController@home');
	Route::get('find_match', 'FindMatchController@index');
	

	Route::pattern('match_id', '[0-9]+');
	Route::pattern('user_id', '[0-9]+');
	Route::pattern('event_id', '[0-9]+');
	Route::pattern('created_event_id', '[0-9]+');
	//match
	Route::get("match/{match_id}", "MatchesController@showMatch");

	//open matches
	Route::get("openMatches", "MatchesController@showOpenMatches");

	// profile
	Route::get("profile", function(){
		return Redirect::to('profile/'.Auth::user()->id);
	});
	Route::get('profile/{user_id}', 'ProfileController@showProfile');
	
	// ladder
	Route::get("ladder", function(){
		return Redirect::to('ladder/'.Auth::user()->id);
	});
	Route::get('ladder/{user_id}', 'LadderController@showLadder');

	// events
	Route::get('events', 'EventsController@index');
	Route::get("event/{event_id}/{created_event_id}", 'EventsController@showEvent');
	
	/* 
	*    ####### AJAX ###########
	*/ 
	// findMatch
	Route::post("find_match/checkJoinQueue", array('before' => 'csrf', 'uses' => 'GameQueuesController@checkJoinQueue'));
	Route::post("find_match/joinQueue", array('before' => 'csrf', 'uses' => 'GameQueuesController@joinQueue'));
	Route::get("find_match/getMMInfo", array('before' => 'csrf', 'uses' => 'FetchViewController@getMMInfo'));
	Route::get("find_match/doMatchmaking", array("before" => "csrf", "uses" => "GameQueuesController@doMatchmaking"));
	Route::get("find_match/getReadyMatch", array("before" => "csrf", "uses" => "FetchViewController@getReadyMatch"));
	Route::get("find_match/getWaitingForOtherUsers", array("before" => "csrf", "uses" => "FetchViewController@getWaitingForOtherUsers"));
	Route::get("find_match/checkAllReadyForMatch", array("before" => "csrf", "uses" => "MatchmakingController@checkAllReadyForMatch"));
	Route::post("find_match/setQueueLock", array("before" => "csrf", "uses" => "QueuelocksController@setQueueLock"));
	Route::post("find_match/cleanUpFailedQueue", array("before" => "csrf", "uses" => "MatchmakingController@cleanUpFailedQueue"));
	Route::post("find_match/leaveQueue", array("before" => "csrf", "uses" => "GameQueuesController@leaveQueue"));
	Route::post("find_match/acceptMatch", array("before" => "csrf", "uses" => "MatchmakingController@acceptMatch"));
	
	// general
	Route::post("setRegion", array('before' => 'csrf', 'uses' => 'RegionsController@setRegion'));

	//matchmode
	Route::get("matchmodes/getQuickJoinModes", array('before' => 'csrf', 'uses' => 'MatchmodesController@getQuickJoinModes'));
	Route::get("matchmodes/getMatchmodeData", array('before' => 'csrf', 'uses' => 'MatchmodesController@getMatchmodeData'));

	//match
	Route::get("match/getSubmitModal", array("before" => "csrf", "uses" => "FetchViewController@matchSubmitModal"));
	Route::get("match/getCancelModal", array("before" => "csrf", "uses" => "FetchViewController@matchCancelModal"));
	Route::get("match/getReplayUploadModal", array("before" => "csrf", "uses" => "FetchViewController@replayUploadModal"));
	Route::post("match/submitResult", array("before" => "csrf", "uses" => "MatchesController@submitResult"));
	Route::post("match/votePlayer", array("before" => "csrf", "uses" => "MatchesController@votePlayer"));
	Route::post("match/cancelVote", array("before" => "csrf", "uses" => "MatchvotesController@cancelVote"));
	Route::post("match/moveReplayFile", array("before" => "csrf", "uses" => "Matchreplay_dota2sController@moveReplayFile"));

	//profile
	Route::get("profile/getPointsHistoryData", array("before" => "csrf", "uses" => "UserpointsController@getPointsHistoryData"));
	Route::get("profile/getPointRoseData", array("before" => "csrf", "uses" => "UserpointsController@getPointRoseData"));
	
	//ladder
	Route::get("ladder/getLadderData", array("before" => "csrf", "uses" => "LadderController@getLadderData"));
	
	// events
	Route::post("events/joinEvent", array("before" => "csrf", "uses" => "EventsController@joinEvent"));
	Route::post("events/signOut", array("before" => "csrf", "uses" => "EventsController@signOut"));

	// Test
	Route::get("testpage", "TestpageController@showTestpage");

	/* 
	// Admin
	*/
	Route::group(array('before' => 'admin'), function(){
		Route::get("admin", "AdminController@home");

		Route::post("admin/queue/insertInQueue", "GameQueuesController@insertRandomUserIntoQueue");
		Route::post("admin/queue/setAllReady", "Matched_usersController@setAllReady");
		Route::post("admin/queue/insertFakeMatchSubmits", "MatchdetailsController@insertFakeMatchSubmits");
		

	// resources
		Route::resource('users', 'UsersController');

		Route::resource('globalsettings', 'GlobalsettingsController');

		Route::resource('matches', 'MatchesController');

		Route::resource('queues', 'QueuesController');


		Route::resource('matchtypes', 'MatchtypesController');

		Route::resource('matchmodes', 'MatchmodesController');


		Route::resource('matchdetails', 'MatchdetailsController');

		Route::resource('queuelocks', 'QueuelocksController');

		Route::resource('permabans', 'PermabansController');

		Route::resource('banlistreasons', 'BanlistreasonsController');

		Route::resource('banlists', 'BanlistsController');

		Route::resource('votetypes', 'VotetypesController');

		Route::resource('userpoints', 'UserpointsController');

		Route::resource('regions', 'RegionsController');

		Route::resource('matched_users', 'Matched_usersController');

		Route::resource('userskillbrackets', 'UserskillbracketsController');

		Route::resource('usercredits', 'UsercreditsController');

		Route::resource('skillbrackettypes', 'SkillbrackettypesController');

		Route::resource('queuelocks', 'QueuelocksController');

		Route::resource('matchhosts', 'MatchhostsController');

		Route::resource('uservotecounts', 'UservotecountsController');

		Route::resource('teams', 'TeamsController');

		Route::resource('usernotifications', 'UsernotificationsController');

		Route::resource('uservotes', 'UservotesController');

		Route::resource('votetypes', 'VotetypesController');

		Route::resource('matchvotes', 'MatchvotesController');

		Route::resource('pointtypes', 'PointtypesController');

		Route::resource('news', 'NewsController');

		Route::resource('streamers', 'StreamersController');


		Route::resource('globalsettings', 'GlobalsettingsController');

		Route::resource('eventtypes', 'EventtypesController');

		Route::resource('eventrequirements', 'EventrequirementsController');

		Route::resource('events', 'EventsController');

		Route::resource('eventregistrations', 'EventregistrationsController');

		Route::resource('created_events', 'Created_eventsController');

		Route::resource('eventteams', 'EventteamsController');

		Route::resource('eventmatches', 'EventmatchesController');

		Route::resource('eventpools', 'EventpoolsController');

		Route::resource('user_won_events', 'User_won_eventsController');

		Route::resource('prizetypes', 'PrizetypesController');

		Route::resource('tournamenttypes', 'TournamenttypesController');
	});

});

// Login/logout stuff
Route::get('login/{action?}','SteamController@login');
Route::get('logout', 'SteamController@logout');
Route::get("forumLogout", function(){
	Auth::logout();
	return Redirect::to("/");
});

if(Config::get('app.debug') == true){
	Route::get('fakelogin', function(){

		$user = User::getFakeUser();
		$user = $user[0];
		$fakeUser = User::find($user->id);
		Auth::login($fakeUser);

		switch (GlobalSetting::getLoginVia()) {
			case 'Steam':
			case "Forum_IPBoard":
			default:
			if(GlobalSetting::getForumHost() == "Dotacinema"){
				Login::updateSteamUser($user->id);
			}
			break;
		}

		//var_dump($fakeUser->id);
		// set first Skillbrackets
		Userskillbracket::setSkillbrackets($user->id);

		// set init uservotecounts
		Uservotecount::initUserVoteCounts($user->id);
		
		return Redirect::to("/");
	});

	Route::get('fakelogin/{user_id}', function($user_id){

		$fakeUser = User::find($user_id);
		Auth::login($fakeUser);

		switch (GlobalSetting::getLoginVia()) {
			case 'Steam':
			case "Forum_IPBoard":
			default:
			if(GlobalSetting::getForumHost() == "Dotacinema"){
				Login::updateSteamUser($user_id);
			}
			break;
		}

		//var_dump($fakeUser->id);
		// set first Skillbrackets
		Userskillbracket::setSkillbrackets($user_id);

		// set init uservotecounts
		Uservotecount::initUserVoteCounts($user_id);
		
		return Redirect::to("/");
	});
}

// ajax


// test geschichten




Route::resource('matchreplay_dota2s', 'Matchreplay_dota2sController');

Route::resource('matchreplay_beststats', 'Matchreplay_beststatsController');

Route::resource('matchreplay_chats', 'Matchreplay_chatsController');

Route::resource('replay_beststattypes', 'Replay_beststattypesController');

Route::resource('replay_dota2_heroes', 'Replay_dota2_heroesController');