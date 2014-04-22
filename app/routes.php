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
Route::get("start", "LoginController@handleLoginPage");
Route::get("start_forumipboard", "LoginController@showForumIPBoard");

//Help
Route::get('help/faq', 'HelpController@showFAQ');
Route::get('help/rules', 'HelpController@showRules');

//

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
	
	//last Matches
	Route::get("lastMatches", "MatchesController@showLastMatches");

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
	Route::post("find_match/setQueueLock", array("before" => "csrf", "uses" => "QueueLocksController@setQueueLock"));
	Route::post("find_match/cleanUpFailedQueue", array("before" => "csrf", "uses" => "MatchmakingController@cleanUpFailedQueue"));
	Route::post("find_match/leaveQueue", array("before" => "csrf", "uses" => "GameQueuesController@leaveQueue"));
	Route::post("find_match/acceptMatch", array("before" => "csrf", "uses" => "MatchmakingController@acceptMatch"));
	Route::post("find_match/cleanUpMatchedUsers", array("before" => "csrf", "uses" => "MatchmakingController@cleanUpMatchedUsers"));
	
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
	Route::post("match/moveScreenshotFile", array("before" => "csrf", "uses" => "MatchesController@moveScreenshotFile"));
	Route::post("match/checkScreenshotUploaded", array("before" => "csrf", "uses" => "MatchesController@checkScreenshotUploaded"));
	Route::post("match/getMatchData", array("before" => "csrf", "uses" => "MatchesController@getMatchData"));

	//profile
	Route::get("profile/getPointsHistoryData", array("before" => "csrf", "uses" => "UserpointsController@getPointsHistoryData"));
	Route::get("profile/getPointRoseData", array("before" => "csrf", "uses" => "UserpointsController@getPointRoseData"));
	Route::post("profile/syncWithSteam", array("before" => "csrf", "uses" => "ProfileController@syncWithSteam"));
	
	//ladder
	Route::get("ladder/getLadderData", array("before" => "csrf", "uses" => "LadderController@getLadderData"));
	
	// events
	Route::post("events/joinEvent", array("before" => "csrf", "uses" => "EventsController@joinEvent"));
	Route::post("events/signOut", array("before" => "csrf", "uses" => "EventsController@signOut"));
	Route::post("events/setEventReadyStatus", array("before" => "csrf", "uses" => "EventsController@setEventReadyStatus"));

	// notifications
	Route::get("notification/getEventStart", array("before" => "csrf", "uses" => "NotificationController@getEventStart"));
	Route::get("notification/getEventCheckIn", array("before" => "csrf", "uses" => "NotificationController@getEventCheckIn"));
	Route::post("notification/sendPing", array("before" => "csrf", "uses" => "NotificationController@sendPing"));
	Route::get("notification/getPing", array("before" => "csrf", "uses" => "NotificationController@getPing"));
	
	// chat
	Route::get("chat/getChatUsers", array("before" => "csrf", "uses" => "FetchViewController@getChatUsers"));
	Route::get("chat/getChatUser", array("before" => "csrf", "uses" => "FetchViewController@getChatUser"));
	Route::get("chat/getMessage", array("before" => "csrf", "uses" => "FetchViewController@getMessage"));
	Route::post("chat/postMessage", array("before" => "csrf", "uses" => "ChatsController@postMessage"));
	Route::get("chat/getOlderMessages", array("before" => "csrf", "uses" => "ChatsController@getOlderMessages"));
	Route::get("chat/getTabContent", array("before" => "csrf", "uses" => "FetchViewController@getTabContent"));
	Route::get("chat/getScaffoldMessageAndChatuser", array("before" => "csrf", "uses" => "FetchViewController@getScaffoldMessageAndChatuser"));
	Route::get("chat/getWhisperModeInputAddon", array("before" => "csrf", "uses" => "FetchViewController@getWhisperModeInputAddon"));

	// Test
	Route::get("testpage", "TestpageController@showTestpage");
	/* 
	// Admin
	*/
	Route::group(array('before' => 'admin'), function(){
		Route::get("admin", "AdminController@home");
		Route::get("admin/queues", "AdminController@showQueueManagement");
		Route::get("admin/matches", "AdminController@showMatchManagement");
		Route::get("admin/bans", "AdminController@showBansManagement");

		Route::post("admin/queue/insertInQueue", "GameQueuesController@insertRandomUserIntoQueue");
		Route::post("admin/queue/setAllReady", "Matched_usersController@setAllReady");
		
		Route::post("admin/matches/insertFakeMatchSubmits", "MatchdetailsController@insertFakeMatchSubmits");
		Route::post("admin/matches/setLeaverForMatch", "MatchdetailsController@setLeaverForMatch");
		Route::post("admin/matches/removeLeaverForMatch", "MatchdetailsController@removeLeaverForMatch");
		

		// Bans management
		Route::post("admin/bans/addBan", array("before" => "csrf", "uses" => "BanlistsController@addBan"));
		Route::post("admin/bans/removeLastBan", array("before" => "csrf", "uses" => "BanlistsController@removeLastBan"));
		Route::post("admin/bans/permaBan", array("before" => "csrf", "uses" => "PermaBansController@permaBan"));
		Route::post("admin/bans/removePermaBan", array("before" => "csrf", "uses" => "PermaBansController@removePermaBan"));
		Route::get("admin/bans/chatBan", "BanlistsController@chatBan");
		
	// resources
		Route::resource('admin/globalsettings', 'GlobalsettingsController');
		Route::resource('admin/news', 'NewsController');
		Route::resource('admin/helps', 'HelpsController');
		Route::resource('admin/matchmodes', 'MatchmodesController');
		Route::resource('admin/matchtypes', 'MatchtypesController');
		Route::resource('admin/regions', 'RegionsController');
		Route::resource('admin/banlistreasons', 'BanlistReasonsController');
		Route::resource('admin/pointtypes', 'PointtypesController');
		Route::resource('admin/skillbrackettypes', 'SkillbrackettypesController');
		
		Route::resource('users', 'UsersController');

		Route::resource('matches', 'MatchesController');

		Route::resource('queues', 'QueuesController');

		Route::resource('matchdetails', 'MatchdetailsController');

		Route::resource('queuelocks', 'QueuelocksController');

		Route::resource('permaBans', 'PermaBansController');

		Route::resource('banlists', 'BanlistsController');

		Route::resource('votetypes', 'VotetypesController');

		Route::resource('userpoints', 'UserpointsController');


		Route::resource('matched_users', 'Matched_usersController');

		Route::resource('userskillbrackets', 'UserskillbracketsController');

		Route::resource('usercredits', 'UsercreditsController');


		Route::resource('queuelocks', 'QueuelocksController');

		Route::resource('matchhosts', 'MatchhostsController');

		Route::resource('uservotecounts', 'UservotecountsController');

		Route::resource('teams', 'TeamsController');

		Route::resource('usernotifications', 'UsernotificationsController');

		Route::resource('uservotes', 'UservotesController');

		Route::resource('votetypes', 'VotetypesController');

		Route::resource('matchvotes', 'MatchvotesController');


		

		Route::resource('streamers', 'StreamersController');

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

		
		Route::resource('matchreplay_dota2s', 'Matchreplay_dota2sController');

		Route::resource('matchreplay_beststats', 'Matchreplay_beststatsController');

		Route::resource('matchreplay_chats', 'Matchreplay_chatsController');

		Route::resource('replay_beststattypes', 'Replay_beststattypesController');

		Route::resource('replay_dota2_heroes', 'Replay_dota2_heroesController');

		Route::resource('matchmodes', 'MatchmodesController');

		// test geschichten
		Route::resource('chats', 'ChatsController');
	});

});

// Login/logout stuff
Route::get('login/{action?}','SteamController@login');
Route::get('logout', 'SteamController@logout');
Route::post('logoutAjax', 'SteamController@logoutAjax');

Route::get("forumLogout", function(){
	Auth::logout();
	return Redirect::to("/");
});
if(GlobalSetting::getFakeLoginAllowed()){
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

// Dota2 Replay Upload Handling
if(GlobalSetting::getReplayUpload()){
	Route::post('replayUpload/dota2', 'Matchreplay_dota2sController@handleReplayUpload');
	Route::get('replayUpload/dota2', 'Matchreplay_dota2sController@handleReplayUpload');
}