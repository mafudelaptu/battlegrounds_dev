<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class ReplayParser{
	public $firstBlood = array();
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllStats($path){
		$ret = array();
		$ret['debug'] .= "Start getAllStats <br>\n";
		// alle hochgeladenen Replays auslesen

		$alleReplayFolders = glob($path."*");

		if(count($alleReplayFolders) > 0 && is_array($alleReplayFolders)){
			foreach($alleReplayFolders as $k => $v){
					
				$pathToFiles = $v;
				$replayID = str_replace($path, "", $pathToFiles);
				$ret['debug'] .= p($replayID,1);
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
						if($dateiname != "combatlog.json"){
							$json_a = json_decode($string,true);
						}

						unset($string);


						switch ($dateiname){
							case "banpicks.json":
								break;
							case "buildings.json":
								break;
							case "buybacks.json":
								break;
							case "chat.json":
								$retC = $this->getChat($json_a);
								$ret['debug'] .= p($retC,1);
								$ret['chat'] = $retC['data'];
								
								$retGL = $this->getGameLength($json_a);
								$ret['gameLength'] = $retGL['data'];
								$ret['debug2'] .= p($retGL,1);
								break;
							case "combatlog.json":

								$this->firstBlood = array();
								//$firstBlood = $this->getFirstBlood($json_a);
								//$firstBlood = $this->getFirstBlood2($datei);
								//p("first blood");
								$ret['firstBlood'] = $firstBlood['data'];
								break;
							case "cs.json":
								$cs = $this->getLastHits($json_a);
								$ret = array_merge_recursive($ret, $cs);

								break;
							case "denies.json":
								$denies = $this->getDenies($json_a);
								$ret = array_merge_recursive($ret, $denies);

								break;
							case "glyphs.json":
								break;
							case "gold.json":
								$gold = $this->getGoldData($json_a);
								$ret = array_merge_recursive($ret, $gold);
								break;
							case "herokills.json":
								$heroKills = $this->getHerokills($json_a);
								$ret = array_merge_recursive($ret, $heroKills);
								break;
							case "itemtimes.json":
								break;
							case "levelups.json":
								$lvl = $this->getLvl($json_a);
								$ret = array_merge_recursive($ret, $lvl);
								break;
							case "pauses.json":
								break;
							case "players.json":
								$playerData = $this->getPlayerData($json_a);
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
		$killerRet = $this->getMost($ret, "kills");
		$ret['bestStats']['mostKills'] = $killerRet['data'];

		// Most Last Hits
		$csRet = $this->getMost($ret, "cs");
		$ret['bestStats']['mostLastHits'] = $csRet['data'];

		// Most Denies
		$deniesRet = $this->getMost($ret, "denies");
		$ret['bestStats']['mostDenies'] = $deniesRet['data'];

		// Most totalGold
		$goldRet = $this->getMost($ret, "totalGold");
		$ret['bestStats']['mostTotalGold'] = $goldRet['data'];

		// Best Teamplayer
		$teamRet = $this->getMost($ret, "assists");
		$ret['bestStats']['bestTeamplayer'] = $teamRet['data'];
		$ret['debug'] .= "################ TESTSETSET <br>\n";
		$ret['debug'] .= "End getAllStats <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getHerokills($json_a){
		$ret = array();
		$statsArr = array();
		$ret['debug'] .= "Start getHerokills <br>\n";

		$array = $json_a["herokills"];

		foreach ($array as $key => $arr) {
			$killer = $arr["killer"];
			$dead = $arr['dead'];
			$aegis = $arr['aegis'];
			$assists = $arr['assists'];


			if(strpos($killer, "hero") > 0){
				if($aegis=="0"){
					$statsArr[$killer]['kills']++;
					$statsArr[$dead]['deaths']++;
					foreach ($assists as $key => $hero) {
						$statsArr[$hero]['assists']++;
					}
				}
			}
		}
		$ret['data'] = $statsArr;
		$ret['debug'] .= "End getHerokills <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPlayerData($json_a){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getPlayerData <br>\n";

		$array = $json_a["players"];

		$ret['data'] = $array;

		$ret['debug'] .= "End getPlayerData <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLastHits($json_a){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getLastHits <br>\n";

		$array = $json_a['cs'];
		$retArr = array();
		if(count($array) > 0 && is_array($array)){
			foreach($array as $k => $v){
				$hero = $v['hero'];
					
				$retArr[$hero]['cs']++;
					
			}
		}
		$ret['data'] = $retArr;
		$ret['debug'] .= "End getLastHits <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getDenies($json_a){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getDenies <br>\n";

		$array = $json_a['denies'];
		$retArr = array();
		if(count($array) > 0 && is_array($array)){
			foreach($array as $k => $v){
				$hero = $v['hero'];
					
				$retArr[$hero]['denies']++;
					
			}
		}
		$ret['data'] = $retArr;
		$ret['debug'] .= "End getDenies <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGoldData($json_a){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getGoldData <br>\n";

		$array = $json_a['gold'];
		$retArr = array();
		if(count($array) > 0 && is_array($array)){
			foreach($array as $k => $v){
				$hero = $v['hero'];
				$gold = $v['gold'];

				$retArr[$hero]['totalGold'] += $gold;
					
			}
		}
		$ret['data'] = $retArr;
		$ret['debug'] .= "End getGoldData <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLvl($json_a){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getLvl <br>\n";

		$array = $json_a['leveluptimes'];
		$retArr = array();
		if(count($array) > 0 && is_array($array)){
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
		$ret['debug'] .= "End getLvl <br>\n";
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGameLength($json_a){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getGameLength <br>\n";
	
		$array = $json_a['chat'];
		$retArr = array();
		$lastEntry = end($array);
		$ret['debug'] .= p($lastEntry,1);
		$time = $lastEntry['time'];
		$time =  round($time/30,0); // in seconds
		
		$ret['data'] = $time;
		$ret['debug'] .= "End getGameLength <br>\n";
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getFirstBlood($json_a){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getFirstBlood <br>\n";

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
		$ret['debug'] .= "End getFirstBlood <br>\n";
		return $ret;
	}

	function getFirstBlood2($file){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getFirstBlood <br>\n";

		require_once "inc/jsonParser/JSONParser.php";

		function objStart($value, $property) {
			//printf("{\n");
		}

		function objEnd($value, $property) {
			//printf("}\n");
		}

		function arrayStart($value, $property) {
			//	printf("[\n");
		}

		function arrayEnd($value, $property) {
			// 			p($value);
			// 			p($property);
			// 			printf("]\n");
		}

		function property($value, $property) {
			//printf("Property2: %s\n", $value);
		}

		function scalar($value, $property) {
			//printf("P:%s V:%s\n",$property, $value);
			switch($property){
				case "attacker":
					$killer = $value;
					break;
				case "target":
					$target = $value;
					break;
				case "time":
					$time = $value;
					break;
				case "type":
					$type = $value;
					if($type == "4" && strpos($target, "hero") > 0 && count($this->firstBlood) == 0){
						$tmp['killer'] = $killer;
						$tmp['target'] = $target;
						$tmp['time'] = round($time/30,0); // in seconds

						$this->firstBlood = $tmp;

						//return $ret;
						exit();
					}
				default:
			}
		}

		// initialise the parser object
		$parser = new JSONParser();

		// sets the callbacks
		//$parser->setArrayHandlers('arrayStart', 'arrayEnd');
		//	$parser->setObjectHandlers('objStart', 'objEnd');
		//$parser->setPropertyHandler('property');
		$parser->setScalarHandler('scalar');

		// parse the document
		$parser->parseDocument($file);
		$ret['data'] = $this->firstBlood;
		$ret['debug'] .= "End getFirstBlood <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getSteamIDByHero($hero, $generalData){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getSteamIDByHero <br>\n";
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

		$ret['debug'] .= "End getSteamIDByHero <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getMost($data, $key){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getMost <br>\n";
		$heroData = $data['data'];

		if(count($heroData) > 0 && is_array($heroData)){
			$max = 0;
			foreach($heroData as $k => $v){
				$val = $v[$key];
				if($val >= $max){
					$max = $val;
					$hero = $k;
				}
			}
			// Wenn max bereits bestimmt, dann nochma durchgucken obs mehrere max gibt
			$heros = array();
			foreach($heroData as $k => $v){
				$val = $v[$key];
				if($val == $max){
					$tmp['hero'] = $k;
					$retSteamID = $this->getSteamIDByHero($k, $data['generalData']);
					$tmp['steamid'] = $retSteamID['data'];
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

		$ret['debug'] .= "End getMost <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getChat($json_a){
		$ret = array();
		$ret['status'] = false;
		$ret['debug'] .= "Start getChat <br>\n";

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
		$ret['debug'] .= "End getChat <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function replayInDB($matchID, $replayID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start replayInDB <br>\n";

		if($matchID > 0){
			$sql = "SELECT *
					FROM `MatchDetailsReplay`
					WHERE MatchID = ".(int) $matchID." OR MatchDetailsReplayID = ".secureNumber($replayID)."
							";
			$data = $DB->countRows($sql);
			$ret['debug'] .= p($sql,1);
			$ret['debug'] .= p($data,1);
			if($data > 0){
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "matchID = 0";
		}

		$ret['debug'] .= "End replayInDB <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllReplayBestStatsTypes(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllReplayBestStatsTypes <br>\n";

		$sql = "SELECT *
				FROM `ReplayBestStatsType`
				";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);
		$ret['data'] = $data;
		$ret['status'] .= true;

		$ret['debug'] .= "End getAllReplayBestStatsTypes <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkForContentOfZip($sourceFile){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkForContentOfZip <br>\n";
		$rightCountOfFiles = 17; // 16 files + ordner
		$i=0;
		$ret['status'] = true;
		$zip = zip_open($sourceFile);

		while($entry = zip_read($zip)) {
			$file_name = zip_entry_name($entry);
			$ret['debug'] .= p("FN:".$file_name,1);
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			$ret['debug'] .= p("EXT:".$ext,1);
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
			$ret['debug'] .= p("C:".$i,1);
			$ret['status'] = "wrong File-count ";
		}

		zip_close($zip);;

		$ret['debug'] .= "End checkForContentOfZip <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkAllPlayersIn($matchID, $playersArray){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkAllPlayersIn <br>\n";
		$ret['debug'] .= p($playersArray,1);
		if($matchID > 0){
			// prüfen ob alle spieler drin -> sonst falsches replay
			$MatchDetails = new MatchDetails();
			$retM = $MatchDetails->getAllPlayersInMatch($matchID);
			$allPlayers = $retM['data'];
			if(is_array($playersArray) && count($playersArray) > 0){
				foreach ($playersArray as $k => $v) {
					$steamID = $v['steamid'];
					$ret['debug'] .= p("STEAMID:".$steamID,1);
					if(is_array($allPlayers) && count($allPlayers) > 0){
						$key = "-1";
						foreach ($allPlayers as $kk => $vv) {
							$ret['debug'] .= p("Test:".$steamID."-".$vv['SteamID'],1);
							if($steamID == $vv['SteamID']){
								$key = (int)$kk;
								break;
							}
						}
							
						if($key != "-1"){
							$ret['debug'] .= p("KEY:".$key,1);
							unset($allPlayers[(int)$key]);
						}
							
					}
				}
				$ret['debug'] .= p($allPlayers,1);
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
		$ret['debug'] .= "End checkAllPlayersIn <br>\n";
		return $ret;
	}

}


?>