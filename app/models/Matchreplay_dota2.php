<?php

class Matchreplay_dota2 extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'match_id' => 'required',
		'user_id' => 'required',
		'hero' => 'required',
		'kills' => 'required',
		'deaths' => 'required',
		'assists' => 'required',
		'lvl' => 'required',
		'cs' => 'required',
		'denies' => 'required',
		'total_gold' => 'required',
		'first_blood_at' => 'required'
		);

	public $timestamps = false;

	public static function  getReplayData($match_id){
		return Matchreplay_dota2::leftJoin("users", "users.id", "=", "matchreplay_dota2s.user_id")
		->leftJoin("replay_dota2_heroes", "replay_dota2_heroes.id", "=", "matchreplay_dota2s.hero")
		->leftJoin("matchdetails", function($join){
			$join->on("matchdetails.match_id", "=", "matchreplay_dota2s.match_id")
			->on("matchdetails.user_id", "=", "matchreplay_dota2s.user_id");
		})
		->where("matchreplay_dota2s.match_id", $match_id)
		;
	}

	public static function replayInDB($match_id, $replay_id=0){
		$data = Matchreplay_dota2::where("id", $replay_id)->orWhere("match_id", $match_id)->count();
		if($data > 0){
			return true;
		}
		else{
			return false;
		}
	}

	public static function getMostPlayedHeroesOfUser($user_id, $limit=9){
		$ret = array();
		$ret['data'] = null;
		$ret['status'] = false;
		$replaysCount = Matchreplay_dota2::getReplayesCountOfUser($user_id);
		if($replaysCount > 0){
			$data = Matchreplay_dota2::where("user_id", $user_id)
			->leftJoin("replay_dota2_heroes", "replay_dota2_heroes.id", "=", "matchreplay_dota2s.hero")
			->groupBy("matchreplay_dota2s.hero")
			->orderBy("value", "desc")
			->take($limit)
			->select(
				"matchreplay_dota2s.hero",
				"replay_dota2_heroes.name as heroname",
				DB::raw("CAST((COUNT(matchreplay_dota2s.hero)/".(int)$replaysCount.")*100 AS DECIMAL(12,2)) as value")
				)->get();
			// add Images
			if(!empty($data) && count($data) > 0){
				foreach ($data as $k => $v) {
					$hero = $v->hero;
					$tmp = explode("npc_dota_hero_", $hero);
					$heroname = end($tmp);
					$data[$k]['src'] = 'http://media.steampowered.com/apps/dota2/images/heroes/'.$heroname.'_lg.png';
				}
			}

			$ret['data'] = $data;
			$ret['status'] = true;
		}
		return $ret;
	}

	public static function getReplayesCountOfUser($user_id){
		return Matchreplay_dota2::where("user_id", $user_id)->count();
	}

	public static function getGameStatsOfUser($user_id){
		$ret = array();
		$ret['data'] = null;
		$ret['status'] = false;

		$sql = "CAST(AVG(gamelength) AS DECIMAL(12,0)) as gamelength,
		CAST(AVG(kills) AS DECIMAL(12,2)) as kills,
		CAST(AVG(deaths) AS DECIMAL(12,2)) as deaths,
		CAST(AVG(assists) AS DECIMAL(12,2)) as assists,
		CAST(AVG(cs) AS DECIMAL(12,2)) as creep_kills,
		CAST(AVG(denies) AS DECIMAL(12,2)) as creep_denies,
		CAST(AVG(total_gold/(gamelength/60)) AS DECIMAL(12,0)) as gold_minute";

		$data = Matchreplay_dota2::where("user_id", $user_id)
		->select(DB::raw($sql))
		->groupBy("matchreplay_dota2s.user_id")->first();
		if(!empty($data) && count($data)>0){
			// GameLength umschreiben
			$data->gamelength = gmdate("H:i:s", $data->gamelength);
		}
		
		$ret['data'] = $data;
		$ret['status'] = true;

		return $ret;
	}

		/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-10-25
	*/
	public static function getMatchStatsOfUser($user_id=0){
		$ret = array();
		$ret['data'] = null;
		$ret['status'] = false;
		$sql = "MAX(kills) as MaxKills,
		MAX(deaths) as MaxDeaths,
		MAX(assists) as MaxAssists";

		$data = Matchreplay_dota2::where("user_id", $user_id)
		->groupBy("matchreplay_dota2s.user_id")
		->select(DB::raw($sql))->first();

		$data2 = Matchreplay_beststat::where("user_id", $user_id)
		->groupBy("matchreplay_beststats.replay_beststattype_id")
		->select("replay_beststattype_id", DB::raw("Count(replay_beststattype_id) as count"))->get();

		if(!empty($data2) && count($data2) > 0){
			foreach ($data2 as $k => $v) {
				$type = $v->replay_beststattype_id;
				$count = $v->count;
				switch($type){
						case "1": // most Kills
						$data['MostKills'] = (int)$count;
						break;
						case "2": // most Supports
						$data['MostSupports'] = (int)$count;
						break;
						case "3": // most CS
						$data['MostCS'] = (int)$count;
						break;
						case "4": // most Denies
						$data['MostDenies'] = (int)$count;
						break;
						case "5": // most total Gold
						$data['MostGold'] = (int)$count;
						break;
						case "6": // firstBlood
						break;
						case "7": // most Kills
						break;
						case "8": // most Kills
						break;
						case "9": // most Kills
						break;
						case "10": // most Kills
						break;
					}
				}
			}

			$ret['data'] = $data;
			$ret['status'] = true;

			return $ret;
		}
		
	}
