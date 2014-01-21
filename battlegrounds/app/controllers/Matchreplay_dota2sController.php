<?php

class Matchreplay_dota2sController extends BaseController {

	function moveReplayFile(){
		$ret = array();
		if(Auth::check()){
			if (Request::ajax()){
				$match_id = Input::get("match_id");
				$fileName = Input::get("fileName");

				$sourceFile = GlobalSetting::getReplayUploadSourceDir().$fileName;
				$destDirectory = GlobalSetting::getReplayUploadDestDir().$match_id."/";
				
				$replayInDB = Matchreplay_dota2::replayInDB($match_id);

				if($replayInDB){
					$ret['status'] = "replayInDB";
					unlink($sourceFile);
				}
				else{
					$retCheck = Replayparser::checkForContentOfZip($sourceFile);

					if($retCheck['status'] === true){
						$zip = new ZipArchive;
						if ($zip->open($sourceFile) === TRUE) {

							if ( ! is_dir($destDirectory)) {
								mkdir($destDirectory);
							}
					// alles löschen was user vorher schon upgeloaded hat
							else{
								$this->deleteDir($destDirectory);
							}

							$zip->extractTo($destDirectory);
							$zip->close();

							$retRP = Replayparser::getAllStats($destDirectory);
							$replayID = $retRP['replayID'];
							$replayInDB = Matchreplay_dota2::replayInDB($match_id, $replayID);
							if($replayInDB){
								$ret['status'] = "replayInDB";
								unlink($sourceFile);
								$this->deleteDir($destDirectory);
							}
							else{
						// Replay mit usern in DB hauen
								if(is_array($retRP['generalData']) && count($retRP['generalData']) > 0){
									$insertArray = array();
									//$retCheckIn = Replayparser::checkAllPlayersIn($match_id, $retRP['generalData']);
									//$checkIn = $retCheckIn['status'];
							//p($retCheckIn);
							//p($retRP['debug2']);
							$checkIn = true;
									if($checkIn){
										foreach ($retRP['generalData']as $k => $v) {
											$steamID = $v['steamid'];
											$hero = $v['hero'];
											$tmp['id'] = $replayID;
											$tmp['match_id'] = (int) $match_id;
											$tmp['user_id'] = $steamID;
											$tmp['hero'] = $hero;
											$tmp['gamelength'] = (int) $retRP['gameLength'];

									// Stats
											$stats = $retRP['data'][$hero];
											//dd($stats);
											
											// $tmp['kills'] = (int) $stats['kills'];
											// $tmp['deaths'] = (int) $stats['deaths'];
											// $tmp['assists'] = (int) $stats['assists'];
											// $tmp['lvl'] = (int) $stats['lvl'];
											// $tmp['total_gold'] = (int) $stats['totalGold'];
											// $tmp['cs'] = (int) $stats['cs'];
											// $tmp['denies'] = (int) $stats['denies'];

											$tmp['kills'] = (!empty($stats['kills'])) ? $tmp['kills'] = $stats['kills'] : $tmp['kills'] = 0;
											$tmp['deaths'] = (!empty($stats['deaths'])) ? $tmp['deaths'] = $stats['deaths'] : $tmp['deaths'] = 0;
											$tmp['assists'] = (!empty($stats['assists'])) ? $tmp['assists'] = $stats['assists'] : $tmp['assists'] = 0;
											$tmp['lvl'] = (!empty($stats['lvl'])) ? $tmp['lvl'] = $stats['lvl'] : $tmp['lvl'] = 0;
											$tmp['total_gold'] = (!empty($stats['totalGold'])) ? $tmp['total_gold'] = $stats['totalGold'] : $tmp['total_gold'] = 0;
											$tmp['cs'] = (!empty($stats['cs'])) ? $tmp['cs'] = $stats['cs'] : $tmp['cs'] = 0;
											$tmp['denies'] = (!empty($stats['denies'])) ? $tmp['denies'] = $stats['denies'] : $tmp['denies'] = 0;

									// Killstreaks TODO

									// FirstBlood
											if($retRP['firstBlood']['killer'] == $hero){
												$tmp['first_blood_at'] = (int) $retRP['firstBlood']['time'];
											}
											else{
										$tmp['first_blood_at'] = (int) 0; // muss sein wegen row count in insert-statement
									}

									$insertArray[] = $tmp;
								}
								//dd($insertArray);
								
								if($insertArray[0]['id'] > 0){
									Matchreplay_dota2::insert($insertArray);
								}

								// Best Stats
								if(is_array($retRP['bestStats']) && count($retRP['bestStats']) > 0){
									foreach ($retRP['bestStats'] as $k => $v) {
										switch($k){
											case "mostKills":
											$bestStatsTypeID = 1;
											break;
											case "bestTeamplayer":
											$bestStatsTypeID = 2;
											break;
											case "mostLastHits":
											$bestStatsTypeID = 3;
											break;
											case "mostDenies":
											$bestStatsTypeID = 4;
											break;
											case "mostTotalGold":
											$bestStatsTypeID = 5;
											break;
												// Killstreaks  TODO
										}

										$insertArray = array();
										if(is_array($v) && count($v) > 0){
											foreach ($v as $k => $ar) {
												$steamID = $ar['steamid'];
												$value = $ar['value'];

												$tmp = array();
												$tmp['matchreplay_id'] = $replayID;
												$tmp['match_id'] = (int) $match_id;
												$tmp['replay_beststattype_id'] = (int) $bestStatsTypeID;
												$tmp['user_id'] = $steamID;
												$tmp['value'] = (int) $value;
												$tmp['created_at'] = new DateTime;
												$insertArray[] = $tmp;
											}
											Matchreplay_beststat::insert($insertArray);
										}
									}
									// Chat eintragen
									if(is_array($retRP['chat']) && count($retRP['chat']) > 0){
										unset($insertArray);
										$insertArray = array();
										foreach ($retRP['chat'] as $k => $v) {
											$tmp = array();
											$tmp['matchreplay_id'] = $replayID;
											$tmp['match_id'] = (int) $match_id;
											$tmp['name'] = mysql_real_escape_string($v['player']);
											$tmp['time'] =  $v['time'];
											$tmp['msg'] = mysql_real_escape_string($v['msg']);
											$insertArray[] = $tmp;
										}
										Matchreplay_chat::insert($insertArray);
									}
									//if($retInsGeneral && $retInsBest && $retInsChat){
									$this->deleteDir($destDirectory);
									// alles löschen was user vorher schon upgeloaded hat
									unlink($sourceFile);

									$retUC = Usercredit::insertReplayBonus($match_id, Auth::user()->id);
									$ret['status'] = true;

								}
							}
							else{
								$ret['status'] = "wrongReplay";
							}
						}
					}

				}
				else {
					
					$ret['status'] = "error beim zip datei oeffnen";
				}
			}
			else{
				$ret['status'] = "fuckYou";
			}
		}
	}
}
return $ret;
}

	public function deleteDir($dirPath){
		if (! is_dir($dirPath)) {
			return false;
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}

	/**
	 * Matchreplay_dota2 Repository
	 *
	 * @var Matchreplay_dota2
	 */
	protected $matchreplay_dota2;

	public function __construct(Matchreplay_dota2 $matchreplay_dota2)
	{
		$this->matchreplay_dota2 = $matchreplay_dota2;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$matchreplay_dota2s = $this->matchreplay_dota2->all();

		return View::make('matchreplay_dota2s.index', compact('matchreplay_dota2s'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('matchreplay_dota2s.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Matchreplay_dota2::$rules);

		if ($validation->passes())
		{
			$this->matchreplay_dota2->create($input);

			return Redirect::route('matchreplay_dota2s.index');
		}

		return Redirect::route('matchreplay_dota2s.create')
		->withInput()
		->withErrors($validation)
		->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$matchreplay_dota2 = $this->matchreplay_dota2->findOrFail($id);

		return View::make('matchreplay_dota2s.show', compact('matchreplay_dota2'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$matchreplay_dota2 = $this->matchreplay_dota2->find($id);

		if (is_null($matchreplay_dota2))
		{
			return Redirect::route('matchreplay_dota2s.index');
		}

		return View::make('matchreplay_dota2s.edit', compact('matchreplay_dota2'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Matchreplay_dota2::$rules);

		if ($validation->passes())
		{
			$matchreplay_dota2 = $this->matchreplay_dota2->find($id);
			$matchreplay_dota2->update($input);

			return Redirect::route('matchreplay_dota2s.show', $id);
		}

		return Redirect::route('matchreplay_dota2s.edit', $id)
		->withInput()
		->withErrors($validation)
		->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->matchreplay_dota2->find($id)->delete();

		return Redirect::route('matchreplay_dota2s.index');
	}

}
