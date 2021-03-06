<?php

class MatchesController extends BaseController {

	protected $layout = "master";
	protected $title = "Match";

	public function showMatch($match_id){
		$this->layout->title = $this->title;
		$user_id = Auth::user()->id;
		$matchStateData = Match::getStateOfMatch($match_id, $user_id);
		$matchData = Match::getMatchData($match_id, true, true, true)
		->select("matches.*", "matchmodes.name as matchmode", "matchmodes.shortcut as mm_shortcut",
			"regions.name as region", "regions.shortcut as r_shortcut", "matchtypes.name as matchtype")
		->first();
		$matchdetailsData = Matchdetail::getMatchdetailData($match_id, true, true)->orderBy("matchdetails.points")
		->select("matchdetails.*", "users.*", "userpoints.pointschange")
		->get();
		if(!empty($matchdetailsData) && count($matchdetailsData)>0){


			if(!empty($matchdetailsData) && count($matchdetailsData)>0){

				$matchPlayersData = Match::getPlayersData($matchdetailsData, $matchData->matchtype_id);

				$contentData = array(
					"heading" => $this->title,
					"match_id" => $match_id,
					"matchState" => $matchStateData['status'],
					"inMatch" => Match::isUserInMatch($user_id, $match_id),
					"host" => Matchhost::getHost($match_id, true)->first(),
					"matchPlayersData" => $matchPlayersData,
					"matchData" => $matchData,
					"voteCounts" => Uservotecount::getVoteCounts($user_id)->first(),
					"team" => Team::getTeamsAsArray(),
					"teamStats" => Match::getAveragePointsOfTeams($matchdetailsData, $matchData->matchtype_id),
					"userVotes" => Uservote::getVotesOfUser($user_id, $match_id)->lists("user_id"),
					"voteStats" => Uservote::getVoteStatsForMatch($match_id, $matchdetailsData),
					);

		// Replay handling
				$ru = GlobalSetting::getReplayUpload();
				$contentData['showUpload'] = $ru;
				$contentData['replayData'] = null;
				$contentData['replayBestStats'] = null;
				$contentData['replaySubmitted'] = false;
				$contentData['replayChat'] = null;

				if($ru){
					switch ($ru) {
						case 'Dota2':
						$replayData = Matchreplay_dota2::getReplayData($match_id)
						->select("matchreplay_dota2s.*", "users.*", "replay_dota2_heroes.name as heroname", "matchdetails.team_id")->get();
						$replayDataBestStats = Matchreplay_beststat::getBestStats($match_id);
					//dd($replayDataBestStats['data']);
						$replaySubmitted = Matchreplay_dota2::replayInDB($match_id);
						$replayChat = Matchreplay_chat::getChat($match_id)->get();
						break;
					}
					$contentData['replayData'] = $replayData;
					$contentData['replayBestStats'] = $replayDataBestStats['data'];
					$contentData['replaySubmitted'] = $replaySubmitted;
					$contentData['replayChat'] = $replayChat;
				}

		// Screenshot handling
				if($matchData->matchtype_id == "2"){
					$screenshotsData = Match::getScreenshots($match_id);
					if(!empty($screenshotsData) && count($screenshotsData)>0){

						$contentData['screenshots'] = $screenshotsData['data'];
					}
				}
		//dd($contentData['userVotes']);
				$this->layout->nest("content", 'matches.match.index', $contentData);
			}
			else{
				return Redirect::to("/");
			}
		}
		else{
			return Redirect::to("/");
		}
	}

	public function showOpenMatches(){
		$title = "Open Matches";
		$this->layout->title = $title;

		$user_id = Auth::user()->id;

		$openMatches = Match::getAllOpenMatches($user_id)
		->join("matchdetails", "matchdetails.match_id", "=", "matches.id")
		->join("matchmodes", "matchmodes.id", "=", "matches.matchmode_id")
		->join("matchtypes", "matchtypes.id", "=", "matches.matchtype_id")
		->where("matchdetails.user_id", $user_id)
		->select(
			"matches.*",
			"matchdetails.submitted",
			"matchmodes.name as matchmode",
			"matchmodes.shortcut as mm_shortcut",
			"matchtypes.name as matchtype"
			)
		->get();

		if(!empty($openMatches)){
			$submitCountsArray = array();
			foreach ($openMatches as $key => $match) {
				$submitCountsArray[$match->id] = (int) Matchdetail::getSubmitCountOfMatch($match->id);
			}
		}
		$contentData = array(
			"heading" => $title,
			"data" => $openMatches,
			"submitCountsArray" => $submitCountsArray,
			);

		//dd($contentData);
		$this->layout->nest("content", 'matches.openMatches.index', $contentData);
	}

	public function showLastMatches(){
		$title = "Last Matches";
		$this->layout->title = $title;

		$user_id = Auth::user()->id;

		$lastMatches = Match::getLastMatches($user_id, "*");

		$contentData = array(
			"heading" => $title,
			"data" => $lastMatches['data'],
			);

		//dd($contentData["data"][0]);
		$this->layout->nest("content", 'matches.lastMatches.index', $contentData);
	}


	function submitResult(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$result = Input::get("result");
				$match_id = Input::get("match_id");
				$leaverArr = Input::get("leaver");

				$user_id =  Auth::user()->id;
				if(Match::isUserInMatch($user_id, $match_id)){
					Matchdetail::submitResult($user_id, $match_id, $result);
					$isEventMatch = Match::isEventMatch($match_id);
					// matched_user clearen
					Matched_user::removeMatchedUserEntry($match_id, $user_id);

					if(is_array($leaverArr) && count($leaverArr) > 0){
						foreach ($leaverArr as $key => $leaver) {
							if(Match::isUserInMatch($leaver, $match_id)){
								Matchvote::insertLeaverVote($match_id, $user_id, $leaver);
							}
						}
					}
					$ret['isEventMatch'] = $isEventMatch;

					$uploaded = Matchdetail::checkScreenshotUploaded($match_id, $user_id);
					if(!$uploaded){
						$ret['status'] = "screenshotNotUploaded";
					}
					else{
						$ret['status'] = true;
					}
				}
				else{
					$ret['status'] = false;
				}
				return $ret;
			}
		}
	}

	function votePlayer(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){

				$vote_user_id = Input::get("user_id");
				$match_id = Input::get("match_id");
				$votetype_id = Input::get("type");
				$user_id =  Auth::user()->id;

				if(Match::isUserInMatch($user_id, $match_id) && Match::isUserInMatch($vote_user_id, $match_id)){
					$retVote = Uservote::insertVote($user_id, $vote_user_id, $votetype_id, $match_id);
					Uservotecount::updateCounts($user_id, $votetype_id);

					if($retVote !== false){
						$ret['status'] = true;
					}
					else{
						$ret['status'] = "insertVoteFail";
						
					}
				}
				else{
					$ret['status'] = "notInMatch";
				}
			}
		}
		return $ret;
	}

	function getMatchData(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){

				$match_id = Input::get("match_id");
				$user_id =  Auth::user()->id;

				if(Match::isUserInMatch($user_id, $match_id)){
					$matchData = Match::getMatchData($match_id)->first();
					$ret['data'] = $matchData;
					$ret['status'] = true;

				}
				else{
					$ret['status'] = "notInMatch";
				}
			}
		}
		return $ret;
	}

	function moveScreenshotFile(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$match_id = Input::get("match_id");
				$fileName = Input::get("fileName");
				$user_id =  Auth::user()->id;

				$sourceFile = GlobalSetting::getScreenshotUploadSourceDir().$fileName;
				$destDirectory = GlobalSetting::getScreenshotUploadDestDir().$match_id."/";
				$destFileName = $user_id."_".$fileName;
				$destFile = $destDirectory.$destFileName;
				if ( ! is_dir($destDirectory)) {
					mkdir($destDirectory);
				}
				else{
					// alles löschen was user vorher schon upgeloaded hat
					$files = glob($destDirectory."*"); // finds all txt files in the directory
					if(!empty($files)){
						foreach($files as $file){
							if(strpos($file, $user_id)){
								unlink($file);
							}
						}
					}

					// verschieben (von source wird gelöscht)
					if (!rename($sourceFile, $destFile)) {
						$ret['status'] = "rename schlug fehl...\n";
					}
					else{
						Matchdetail::setScreenshotUploaded($match_id, $user_id);
						$ret['fileName'] = $destFileName;
						$ret['status'] = true;
					}
				}
			}
		}
		return $ret;
	}

	function checkScreenshotUploaded(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$match_id = Input::get("match_id");
				$user_id =  Auth::user()->id;

				$uploaded = Matchdetail::checkScreenshotUploaded($match_id, $user_id);
				$ret['status'] = $uploaded;

			}
		}
		return $ret;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('matches.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('matches.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return View::make('matches.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		return View::make('matches.edit');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
