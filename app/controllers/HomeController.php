<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	protected $layout = "master";
	protected $title = "Arena - DotaCinema";
	
	public function home(){
		$this->layout->title = $this->title;
		$lastMatchesData =  Match::getGlobalLastMatches(5);
		$user_id = Auth::user()->id;
		$qs5vs5Single = array(
			"queueCount" => (int) GameQueue::getPlayersInQueue(1)->groupBy("user_id")->count(),
			"openMatches" => 0,
			"maxMatchmode" => "",
			"maxRegion" => "",
			);
		$queueStats5vs5Single = json_decode(json_encode($qs5vs5Single), FALSE);

		$qs1vs1 = array(
			"queueCount" => (int) GameQueue::getPlayersInQueue(2)->groupBy("user_id")->count(),
			"openMatches" => "",
			"maxMatchmode" => "",
			"maxRegion" => "",
		);
		$queueStats1vs1 = json_decode(json_encode($qs1vs1), FALSE);
		
		$statsOfUser = User::getStatsOfUser($user_id);
		//dd($statsOfUser);

		// Events
		$eventsData = EventModel::getNextEvents()->get();
		$eventsData = EventModel::setStatusOfEvents($eventsData);
		
		$contentData = array(
			"heading" => $this->title,
			"newsData" => News::getAllActiveNews()->get(),
			"streamerData" => Streamer::getAllPlayingStreamers(5),
			"bestPlayers" => Ladder::getBestPlayers(1,5),
			"lastMatches" => $lastMatchesData->get(),
			"matchmodes" => Matchmode::getAllActiveModes()->get(),
			"highestCredits" => Usercredit::getHighestUserCredits(5)->get(),
			"matchtypes" => Matchtype::getAllActiveMatchtypes()->get(),
			"queueStats5vs5Single" => $queueStats5vs5Single,
			"queueStats1vs1" => $queueStats1vs1,
			"inMatch" => Match::isUserInMatch(Auth::user()->id),
			"stats" => $statsOfUser['stats'],
			"points" => $statsOfUser['points'],
			"events" => $eventsData,
			);
		$this->layout->nest("content", 'home.index', $contentData);
	}
}