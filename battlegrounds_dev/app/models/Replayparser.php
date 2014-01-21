<?php

class Replayparser extends Eloquent {
	protected $guarded = array();

	public static $rules = array();
	public $firstBlood = array();
	public static function checkForContentOfZip($sourceFile){
		$ret = array();
		$rightCountOfFiles = 17; // 16 files + ordner
		$i=0;
		$ret['status'] = true;
		$zip = zip_open($sourceFile);

		while($entry = zip_read($zip)) {
			$file_name = zip_entry_name($entry);
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			//folder
			if($ext == ""){
				$file_name = substr($file_name, 0, -1); // "/" entfernen
				if(!is_numeric($file_name)){
					$ret['status'] = "Zip no Replay-Folder";
					break;
				}
			}
			else{
				if(strtoupper($ext) !== 'JSON') {
					$ret['status'] = "wrong File-type";
					break;
				}
			}
			// close entry
			zip_entry_close($entry);
			$i++;
		}

		if($i == $rightCountOfFiles OR $i == ($rightCountOfFiles-1)){
		}
		else{
			$ret['status'] = "wrong File-count";
		}

		zip_close($zip);
		return $ret;
	}

	public static function getAllStats($path){
		$ret = array();
		// alle hochgeladenen Replays auslesen

		$alleReplayFolders = glob($path."*");

		if(count($alleReplayFolders) > 0 && is_array($alleReplayFolders)){
			foreach($alleReplayFolders as $k => $v){

				$pathToFiles = $v;
				$replayID = str_replace($path, "", $pathToFiles);
				$ret['replayID'] = $replayID;

				// alle files auslesen
				$alleJsonFiles = glob($pathToFiles."/*.json");
				//p("3:".memory_get_usage() . "\n");
				if(count($alleJsonFiles)>0){
					$i=0;

					foreach($alleJsonFiles as $datei){

						$dateiname = str_replace($pathToFiles."/", "", $datei);
						unset($string);
						//p("4:".memory_get_usage() . "\n");
						$string = file_get_contents($datei);
						unset($json_a);
						$json_a = "";
						//if($dateiname != "combatlog.json"){
						$json_a = json_decode($string,true);
						//}

						unset($string);


						switch ($dateiname){
							case "banpicks.json":
							break;
							case "buildings.json":
							break;
							case "buybacks.json":
							break;
							case "chat.json":
							$retC = Replayparser::getChat($json_a);
							$ret['chat'] = $retC['data'];

							$retGL = Replayparser::getGameLength($json_a);
							$ret['gameLength'] = $retGL['data'];
							break;
							case "combatlog.json":

								//$firstBlood = Replayparser::getFirstBlood($json_a);
							$firstBlood = Replayparser::getFirstBlood($json_a);
								//p("first blood");
							$ret['firstBlood'] = $firstBlood['data'];
							break;
							case "cs.json":
							$cs = Replayparser::getLastHits($json_a);
							$ret = array_merge_recursive($ret, $cs);

							break;
							case "denies.json":
							$denies = Replayparser::getDenies($json_a);
							$ret = array_merge_recursive($ret, $denies);

							break;
							case "glyphs.json":
							break;
							case "gold.json":
							$gold = Replayparser::getGoldData($json_a);
							$ret = array_merge_recursive($ret, $gold);
							break;
							case "herokills.json":
							$heroKills = Replayparser::getHerokills($json_a);
							
							$ret = array_merge_recursive($ret, $heroKills);
							break;
							case "itemtimes.json":
							break;
							case "levelups.json":
							$lvl = Replayparser::getLvl($json_a);
							$ret = array_merge_recursive($ret, $lvl);
							break;
							case "pauses.json":
							break;
							case "players.json":
							$playerData = Replayparser::getPlayerData($json_a);
							$ret['generalData'] = $playerData['data'];
							break;
							case "roshan.json":
							break;
							case "runes.json":
							break;
						}
						unset($string);
						unset($json_a);
						$i++;
					}
				}
				break;
			}
		}

		/*
		 * Best Stats
		*/
		// Killer
		$killerRet = Replayparser::getMost($ret, "kills");
		$ret['bestStats']['mostKills'] = $killerRet['data'];

		// Most Last Hits
		$csRet = Replayparser::getMost($ret, "cs");
		$ret['bestStats']['mostLastHits'] = $csRet['data'];

		// Most Denies
		$deniesRet = Replayparser::getMost($ret, "denies");
		$ret['bestStats']['mostDenies'] = $deniesRet['data'];

		// Most totalGold
		$goldRet = Replayparser::getMost($ret, "totalGold");
		$ret['bestStats']['mostTotalGold'] = $goldRet['data'];

		// Best Teamplayer
		$teamRet = Replayparser::getMost($ret, "assists");
		$ret['bestStats']['bestTeamplayer'] = $teamRet['data'];
		return $ret;
	}

	public static function getMost($data, $key){
		$ret = array();
		$ret['status'] = false;
		$heroData = $data['data'];
		if(count($heroData) > 0 && is_array($heroData)){
			$max = 0;
			foreach($heroData as $k => $v){
				if(!empty($v[$key])){
					$val = $v[$key];
				}
				else{
					$val = 0;
				}
				if($val >= $max){
					$max = $val;
					$hero = $k;
				}
			}
			// Wenn max bereits bestimmt, dann nochma durchgucken obs mehrere max gibt
			$heros = array();
			foreach($heroData as $k => $v){
				if(!empty($v[$key])){
					$val = $v[$key];
				}
				else{
					$val = 0;
				}
				if($val == $max){
					$tmp['hero'] = $k;
					$retSteamID = Replayparser::getSteamIDByHero($k, $data['generalData']);
					$tmp['steamid'] = $retSteamID['data'];
					(!empty($retSteamID['data'])) ? $tmp['steamid'] = $retSteamID['data'] : $tmp['steamid'] = $k;
					$heros[] = $tmp;
				}
			}
			if(is_array($heros) && count($heros) > 0){
				foreach ($heros as $k => $v) {
					$heros[$k]['value'] = $max;
				}
			}
			$ret['data'] = $heros;

		}
		else{
			$ret['status'] = false;
		}

		return $ret;
	}

	public static function getFirstBlood($json_a){
		$ret = array();
		$ret['status'] = false;

		$array = $json_a['combatlog'];
		$retArr = array();
		if(count($array) > 0 && is_array($array)){
			$firstBlood = array();
			foreach($array as $k => $v){

				$type = $v['type'];
				$target = $v['target'];
				$killer = $v['attacker'];
				$time = $v['time'];

				if($type == "4" && strpos($target, "hero") > 0 && count($firstBlood) == 0){
					$tmp['killer'] = $killer;
					$tmp['target'] = $target;
					$tmp['time'] = round($time/30,0); // in seconds

					$firstBlood = $tmp;
					//break;
				}
				if($type == "4" && strpos($target, "hero")>0){
				}
			}
		}
		$ret['data'] = $firstBlood;
		return $ret;
	}

	public static function getChat($json_a){
		$ret = array();
		$ret['status'] = false;

		$array = $json_a['chat'];
		if(count($array) > 0 && is_array($array)){
			$data = array();
			foreach($array as $k => $v){

				$time =  gmdate("H:i:s", $v['time']/30);
				$player = $v['player'];
				$msg = $v['msg'];
				$tmp['time'] = $time;
				$tmp['player'] = $player;
				$tmp['msg'] = $msg;
				$data[] = $tmp;
			}
		}
		$ret['data'] = $data;
		return $ret;
	}

	public static function getGameLength($json_a){
		$ret = array();
		$ret['status'] = false;

		$array = $json_a['chat'];
		$retArr = array();
		$lastEntry = end($array);
		$time = $lastEntry['time'];
		$time =  round($time/30,0); // in seconds
		
		$ret['data'] = $time;
		return $ret;
	}

	public static function getLastHits($json_a){
		$ret = array();
		$ret['status'] = false;

		$array = $json_a['cs'];
		$retArr = array();
		if(count($array) > 0 && is_array($array)){
			foreach($array as $k => $v){
				$hero = $v['hero'];
				$retArr[$hero] = array("cs"=>0);
			}
			foreach($array as $k => $v){
				$hero = $v['hero'];
				$retArr[$hero]['cs']++;

			}
		}
		$ret['data'] = $retArr;
		return $ret;
	}

	public static function getDenies($json_a){
		$ret = array();
		$ret['status'] = false;

		$array = $json_a['denies'];
		$retArr = array();
		if(count($array) > 0 && is_array($array)){
			foreach($array as $k => $v){
				$hero = $v['hero'];
				$retArr[$hero] = array("denies"=>0);
			}
			foreach($array as $k => $v){
				$hero = $v['hero'];

				$retArr[$hero]['denies']++;

			}
		}
		$ret['data'] = $retArr;
		return $ret;
	}

	public static function getGoldData($json_a){
		$ret = array();
		$ret['status'] = false;

		$array = $json_a['gold'];
		$retArr = array();
		if(count($array) > 0 && is_array($array)){
			foreach($array as $k => $v){
				$hero = $v['hero'];
				$retArr[$hero] = array("totalGold"=>0);
			}
			foreach($array as $k => $v){
				$hero = $v['hero'];
				$gold = $v['gold'];

				$retArr[$hero]['totalGold'] += $gold;

			}
		}
		$ret['data'] = $retArr;
		return $ret;
	}

	public static function getLvl($json_a){
		$ret = array();
		$ret['status'] = false;

		$array = $json_a['leveluptimes'];
		$retArr = array();
		if(count($array) > 0 && is_array($array)){
			foreach($array as $k => $v){
				$hero = $v['hero'];
				$retArr[$hero] = array("lvl"=>0);
			}
			foreach($array as $k => $v){
				$hero = $v['hero'];

				$retArr[$hero]['lvl']++;

			}
		}
		if(count($retArr) > 0 && is_array($retArr)){
			foreach($retArr as $k => $v){
				$retArr[$k]['lvl']++;
			}
		}

		$ret['data'] = $retArr;
		return $ret;
	}

	public static function getHerokills($json_a){
		$ret = array();
		$statsArr = array();

		$array = $json_a["herokills"];

		foreach ($array as $key => $arr) {
			$killer = $arr["killer"];
			$dead = $arr['dead'];
			$aegis = $arr['aegis'];
			$assists = $arr['assists'];

			if(strpos($killer, "hero") > 0){
				if($aegis=="0"){
					(!empty($statsArr[$killer]['kills'])) ? $statsArr[$killer]['kills']++ : $statsArr[$killer]["kills"] = 1;
					(!empty($statsArr[$dead]['deaths'])) ? $statsArr[$dead]['deaths']++ : $statsArr[$dead]['deaths'] = 1;
					// $statsArr[$killer]['kills']++;
					// $statsArr[$dead]['deaths']++;
					foreach ($assists as $key => $hero) {
						// $statsArr[$hero]['assists']++;
						(!empty($statsArr[$hero]['assists'])) ? $statsArr[$hero]['assists']++ : $statsArr[$hero]['assists'] = 1;
					}
				}
			}
		}
		$ret['data'] = $statsArr;
		return $ret;
	}

	public static function getPlayerData($json_a){
		$ret = array();
		$ret['status'] = false;

		$array = $json_a["players"];

		$ret['data'] = $array;

		return $ret;
	}

	public static function getSteamIDByHero($hero, $generalData){
		$ret = array();
		$ret['status'] = false;
		if($hero != "") {
			if(count($generalData) > 0 && is_array($generalData)){
				foreach($generalData as $k => $v){
					if($hero == $v['hero']){
						$ret['data'] = $v['steamid'];
					}
				}
			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "Hero = ''";
		}

		return $ret;
	}

	public static function checkAllPlayersIn($matchID, $playersArray){
		$ret = array();
		if($matchID > 0){
			// prüfen ob alle spieler drin -> sonst falsches replay
			$allPlayers = Matchdetail::getMatchdetailData($matchID)->get();
			if(is_array($playersArray) && count($playersArray) > 0){
				foreach ($playersArray as $k => $v) {
					$steamID = $v['steamid'];
					if(is_array($allPlayers) && count($allPlayers) > 0){
						$key = "-1";
						foreach ($allPlayers as $kk => $vv) {
							if($steamID == $vv->user_id){
								$key = (int)$kk;
								break;
							}
						}

						if($key != "-1"){
							unset($allPlayers[(int)$key]);
						}

					}
				}
				if(count($allPlayers) === 0){
					$ret['status'] = true;
				}
				else{
					$ret['status'] = false;
				}
			}
		}
		else{
			$ret['status'] = "matchID == 0";
		}
		return $ret;
	}

}
