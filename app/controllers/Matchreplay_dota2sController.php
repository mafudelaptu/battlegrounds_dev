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
									$retCheckIn = Replayparser::checkAllPlayersIn($match_id, $retRP['generalData']);
									$checkIn = $retCheckIn['status'];
							//p($retCheckIn);
							//p($retRP['debug2']);
							//$checkIn = true;
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
											if(!empty($retRP['data'][$hero]) && count($retRP['data'][$hero])>0){
												$stats = $retRP['data'][$hero];

												$tmp['kills'] = (!empty($stats['kills'])) ? $tmp['kills'] = $stats['kills'] : $tmp['kills'] = 0;
												$tmp['deaths'] = (!empty($stats['deaths'])) ? $tmp['deaths'] = $stats['deaths'] : $tmp['deaths'] = 0;
												$tmp['assists'] = (!empty($stats['assists'])) ? $tmp['assists'] = $stats['assists'] : $tmp['assists'] = 0;
												$tmp['lvl'] = (!empty($stats['lvl'])) ? $tmp['lvl'] = $stats['lvl'] : $tmp['lvl'] = 0;
												$tmp['total_gold'] = (!empty($stats['totalGold'])) ? $tmp['total_gold'] = $stats['totalGold'] : $tmp['total_gold'] = 0;
												$tmp['cs'] = (!empty($stats['cs'])) ? $tmp['cs'] = $stats['cs'] : $tmp['cs'] = 0;
												$tmp['denies'] = (!empty($stats['denies'])) ? $tmp['denies'] = $stats['denies'] : $tmp['denies'] = 0;
											}
											else{
												$tmp['kills'] = $tmp['kills'] = 0;
												$tmp['deaths'] = $tmp['deaths'] = 0;
												$tmp['assists'] = $tmp['assists'] = 0;
												$tmp['lvl'] = $tmp['lvl'] = 0;
												$tmp['total_gold'] = $tmp['total_gold'] = 0;
												$tmp['cs'] = $tmp['cs'] = 0;
												$tmp['denies'] = $tmp['denies'] = 0;
											}

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
									//dd($insertArray);
									try{
										Matchreplay_dota2::insert($insertArray);
									}
									catch(Exception $e){
										
									}
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
											try{

												Matchreplay_beststat::insert($insertArray);
											}
											catch(Exception $e){
												
											}
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
											$tmp['name'] = $v['player'];
											$tmp['time'] =  $v['time'];
											$tmp['msg'] = $v['msg'];
											$insertArray[] = $tmp;
										}
										try{
											Matchreplay_chat::insert($insertArray);
										}
										catch(Exception $e){
											
										}
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

public function handleReplayUpload(){
	
	// $data = '{
	// 	"players": [
	// 	{
	// 		"name": "Winston",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_riki",
	// 		"kills": 16,
	// 		"deaths": 8,
	// 		"assists": 14,
	// 		"gpm": 428,
	// 		"xpm": 611,
	// 		"cs": 145,
	// 		"denies": 1,
	// 		"radiant": true
	// 	},
	// 	{
	// 		"name": "Потерянный Компот",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_vengefulspirit",
	// 		"kills": 2,
	// 		"deaths": 8,
	// 		"assists": 18,
	// 		"gpm": 232,
	// 		"xpm": 356,
	// 		"cs": 35,
	// 		"denies": 20,
	// 		"radiant": true
	// 	},
	// 	{
	// 		"name": "Oldrinn",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_bane",
	// 		"kills": 7,
	// 		"deaths": 4,
	// 		"assists": 19,
	// 		"gpm": 304,
	// 		"xpm": 531,
	// 		"cs": 49,
	// 		"denies": 11,
	// 		"radiant": true
	// 	},
	// 	{
	// 		"name": "den",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_pudge",
	// 		"kills": 15,
	// 		"deaths": 12,
	// 		"assists": 10,
	// 		"gpm": 365,
	// 		"xpm": 512,
	// 		"cs": 103,
	// 		"denies": 4,
	// 		"radiant": true
	// 	},
	// 	{
	// 		"name": "ууууУУуу",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_mirana",
	// 		"kills": 14,
	// 		"deaths": 5,
	// 		"assists": 9,
	// 		"gpm": 651,
	// 		"xpm": 659,
	// 		"cs": 334,
	// 		"denies": 4,
	// 		"radiant": true
	// 	},
	// 	{
	// 		"name": "[BB]Kolpa",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_alchemist",
	// 		"kills": 7,
	// 		"deaths": 13,
	// 		"assists": 13,
	// 		"gpm": 601,
	// 		"xpm": 629,
	// 		"cs": 245,
	// 		"denies": 14,
	// 		"radiant": false
	// 	},
	// 	{
	// 		"name": ".303 Bookworm",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_zuus",
	// 		"kills": 11,
	// 		"deaths": 6,
	// 		"assists": 14,
	// 		"gpm": 377,
	// 		"xpm": 569,
	// 		"cs": 106,
	// 		"denies": 4,
	// 		"radiant": false
	// 	},
	// 	{
	// 		"name": "Black Tiger",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_legion_commander",
	// 		"kills": 8,
	// 		"deaths": 15,
	// 		"assists": 5,
	// 		"gpm": 356,
	// 		"xpm": 401,
	// 		"cs": 162,
	// 		"denies": 0,
	// 		"radiant": false
	// 	},
	// 	{
	// 		"name": "(A-Z)Mudar",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_windrunner",
	// 		"kills": 1,
	// 		"deaths": 13,
	// 		"assists": 11,
	// 		"gpm": 291,
	// 		"xpm": 328,
	// 		"cs": 97,
	// 		"denies": 0,
	// 		"radiant": false
	// 	},
	// 	{
	// 		"name": "Hanfpflanze",
	// 		"steamid": 76561198047012055,
	// 		"hero": "npc_dota_hero_ancient_apparition",
	// 		"kills": 6,
	// 		"deaths": 9,
	// 		"assists": 16,
	// 		"gpm": 276,
	// 		"xpm": 359,
	// 		"cs": 24,
	// 		"denies": 4,
	// 		"radiant": false
	// 	}
	// 	],
	// 	"general": {
	// 		"match_id": 563365738,
	// 		"mode": 1,
	// 		"length": 3012,
	// 		"radiant_won": false
	// 	}
	// }';
	// $data= json_decode($data);
	$response = "true";
	$data = Input::all();
	//dd($data);
	//Log::error("test log");
	//Log::error(print_r($data['json'],1));
	//Log::info('Log message', $data);
	if((!empty($data) && count($data)>0) || empty($data['json'])){
		$data = json_decode($data['json']);
		$players = $data->players;
		$general = $data->general;
		$replayInDB = Matchreplay_dota2::replayInDB(0, $general->match_id);
		Log::info('replayInDB '.(int)$replayInDB);
		if(!$replayInDB){
			Log::info('players '.print_r($players,1));
			if(!empty($players)){
			// get matchid by replay-players
				Log::info('test ');
				$matchData = Matchdetail::getMatchDataByPlayers($players);
				Log::info('matchData '.print_r($matchData,1));
				if(!empty($matchData) && count($matchData)>0){
					Log::info('test matchdata insert ');
					$match_id = $matchData->id;
					$insertArray = array();
					foreach ($players as $k => $v) {
						$tmpAr = array(
							"id" => $general->match_id,
							"match_id" => $match_id,
							"user_id" => $v->steamid,
							"hero" => $v->hero,
							"kills" => $v->kills,
							"deaths" => $v->deaths,
							"assists" => $v->assists,
							"lvl" => $v->level,
							"cs" => $v->cs,
							"gpm" => $v->gpm,
							"xpm" => $v->xpm,
							);
						$insertArray[] = $tmpAr;
					}
					Log::info('insertArray '.print_r($insertArray,1));
				// insert Data into DB
					try{
						Matchreplay_dota2::insert($insertArray);
					}
					catch(Exception $e){
						$response = "couldnt insert matchData";
					}
					Log::info('before stats ');
				// Best stats
					$stats = Matchreplay_beststat::calculateBestStats($players);
					Log::info('stats '.print_r($stats,1));
					if(is_array($stats) && count($stats) > 0){
						Log::info('stats not null');
						$insertArray = array();
						foreach ($stats as $k => $v) {
							$beststatstype_id = $k;
							$value = $v['val'];
							Log::info('sv '.print_r($v,1));
							foreach ($v['players'] as $pk => $pv) {
								$tmp = array();
								$tmp['matchreplay_id'] = $general->match_id;
								$tmp['match_id'] = (int) $match_id;
								$tmp['replay_beststattype_id'] = (int) $beststatstype_id;
								$tmp['user_id'] = $pv->steamid;
								$tmp['value'] = (int) $value;
								$tmp['created_at'] = new DateTime;
								$insertArray[] = $tmp;
							}
						}
						Log::info('stats insertArray '.print_r($insertArray,1));
						try{
							Matchreplay_beststat::insert($insertArray);
						}
						catch(Exception $e){
							$response = "couldnt insert best Stats";
						}
					}
					else{
						$response = "couldnt calculate best Stats";
					}

					return Response::make("true", 200);
				}
				else{
					$response = "couldnt get MatchID of Arena";
				}
			}
			else{
				$response = "error in parsed data. data is empty.";
			}
		}
		else{
			$response = "replay already uploaded.";
		}
	}
	else{
		$response = "json-data is null.";

	}
	return Response::make($response, 200);
	
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
		$input = Input::all(); $input = General::unsetByKeyStartStr("/arena/", $input);




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
