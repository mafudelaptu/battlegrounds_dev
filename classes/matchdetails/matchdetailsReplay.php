<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class MatchDetailsReplay{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getReplayData($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getReplayData <br>\n";

		if($matchID > 0){

			$sql = "SELECT mdr.*, u.*, rhl.Name as HeroName, md.TeamID
					FROM `MatchDetailsReplay` mdr
					LEFT JOIN User u ON mdr.SteamID = u.SteamID
					LEFT JOIN ReplayHeroList rhl ON rhl.HeroID = mdr.Hero
					LEFT JOIN MatchDetails md ON md.MatchID = mdr.MatchID AND md.SteamID = mdr.SteamID
					WHERE mdr.MatchID = ".(int) $matchID."
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);



			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID == 0";
		}

		$ret['debug'] .= "End getReplayData <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getReplayDataBestStats($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getReplayData <br>\n";

		if($matchID > 0){

			$sql = "SELECT mdrbs.*, u.*, rbst.Name as BestStatsTypeLabel
					FROM `MatchDetailsReplayBestStats` mdrbs
					LEFT JOIN User u ON mdrbs.SteamID = u.SteamID
					LEFT JOIN ReplayBestStatsType rbst ON rbst.ReplayBestStatsTypeID = mdrbs.ReplayBestStatsTypeID AND rbst.Active = 1
					WHERE mdrbs.MatchID = ".(int) $matchID."
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);

			if(is_array($data) && count($data) > 0){
				foreach ($data as $k => $v) {
					$type = $v['ReplayBestStatsTypeID'];
					$value = $v['StatsValue'];
					$label = $v['BestStatsTypeLabel'];
					$data2[$type]['general']['Value'] = $value;
					$data2[$type]['general']['Label'] = $label;
					$data2[$type]['players'][] = $data[$k];
				}
			}
			$ret['data'] = $data2;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID == 0";
		}

		$ret['debug'] .= "End getReplayData <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getReplayChat($matchID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getReplayChat <br>\n";

		if($matchID > 0){

			$sql = "SELECT *
					FROM `MatchDetailsReplayChat` mdrc
					LEFT JOIN User u ON mdrc.Player = u.Name
					WHERE mdrc.MatchID = ".(int) $matchID."
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);

			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID == 0";
		}

		$ret['debug'] .= "End getReplayChat <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-10-25
	*/
	function getMostPlayedHeroesOfUser($steamID = 0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMostPlayedHeroesOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
			$retThis = $this->getReplayesCountOfUser($steamID);
			$count = $retThis['data'];
			if($count > 0){
				$sql = "SELECT mdr.Hero, rhl.Name as HeroName, CAST((COUNT(Hero)/".(int)$count.")*100 AS DECIMAL(12,2)) as Value
						FROM MatchDetailsReplay mdr LEFT JOIN ReplayHeroList rhl ON rhl.HeroID = mdr.Hero
						WHERE SteamID = ".secureNumber($steamID)."
								GROUP BY mdr.Hero
								ORDER BY Value DESC
								LIMIT 6
								";
				$data = $DB->multiSelect($sql);
				$ret['debug'] .= p($sql,1);

				// add Images
				if(is_array($data) && count($data) > 0){
					foreach ($data as $k => $v) {
						$hero = $v['Hero'];
						$tmp = explode("npc_dota_hero_", $hero);
						$heroname = end($tmp);
						$data[$k]['Src'] = 'http://media.steampowered.com/apps/dota2/images/heroes/'.$heroname.'_lg.png';
					}
				}

				$ret['data'] = $data;
				$ret['status'] = true;
			}
			else{
				$ret['data'] = false;
				$ret['status'] = true;
			}

		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getMostPlayedHeroesOfUser <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-10-25
	*/
	function getReplayesCountOfUser($steamID = 0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getReplayesCountOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT *
					FROM `MatchDetailsReplay` mdr
					WHERE mdr.SteamID = ".secureNumber($steamID)."
							";
			$data = $DB->countRows($sql);
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getReplayesCountOfUser <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-10-25
	*/
	function getGameStatsOfUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getGameStatsOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){

			$sql = "SELECT CAST(AVG(GameLength) AS DECIMAL(12,0)) as GameLength,
					CAST(AVG(Kills) AS DECIMAL(12,2)) as Kills,
					CAST(AVG(Deaths) AS DECIMAL(12,2)) as Deaths,
					CAST(AVG(Assists) AS DECIMAL(12,2)) as Assists,
					CAST(AVG(CS) AS DECIMAL(12,2)) as CreepKills,
					CAST(AVG(Denies) AS DECIMAL(12,2)) as CreepDenies,
					CAST(AVG(TotalGold/(GameLength/60)) AS DECIMAL(12,0)) as GoldMinute
					FROM MatchDetailsReplay mdr
					WHERE mdr.SteamID = ".secureNumber($steamID)."
							GROUP BY mdr.SteamID
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);

			// GameLength umschreiben
			$data['GameLength'] = gmdate("H:i:s", $data['GameLength']);

			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getGameStatsOfUser <br>\n";
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-10-25
	*/
	function getMatchStatsOfUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getGameStatsOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){

			$sql = "SELECT MAX(Kills) as MaxKills,
					MAX(Deaths) as MaxDeaths,
					MAX(Assists) as MaxAssists

					FROM MatchDetailsReplay mdr
					WHERE mdr.SteamID = ".secureNumber($steamID)."
							GROUP BY mdr.SteamID
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);

			$sql = "SELECT Count(ReplayBestStatsTypeID) as Count, mdrbs.ReplayBestStatsTypeID
					FROM MatchDetailsReplayBestStats mdrbs
					WHERE mdrbs.SteamID = ".secureNumber($steamID)."
							GROUP BY mdrbs.ReplayBestStatsTypeID
							";
			$data2 = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);

			if(is_array($data2) && count($data2) > 0){
				foreach ($data2 as $k => $v) {
					$type = $v['ReplayBestStatsTypeID'];
					$count = $v['Count'];
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
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getGameStatsOfUser <br>\n";
		return $ret;
	}

	function getMatchesWithoutReplaysOfUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchesWithoutReplaysOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
				
			$sql = "SELECT md.*,m.*, mm.Name as MatchMode, mm.Shortcut as MatchModeShortcut
					FROM MatchDetails md JOIN `Match` m ON m.MatchID = md.MatchID
					JOIN MatchMode mm ON m.MatchModeID = mm.MatchModeID
					WHERE md.SteamID = ".secureNumber($steamID)." AND  m.TeamWonID != -1 AND ManuallyCheck = 0 AND Canceled = 0
					AND NOT EXISTS (SELECT MatchID FROM `MatchDetailsReplay` mdr WHERE mdr.MatchID = md.MatchID AND mdr.SteamID = md.SteamID)
					ORDER BY md.MatchID DESC
					";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);
				
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getMatchesWithoutReplaysOfUser <br>\n";
		return $ret;
	}
	
	function getMatchesWithoutReplaysCountOfUser($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getMatchesWithoutReplaysOfUser <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
	
		if($steamID > 0){
	
			$ret = $this->getMatchesWithoutReplaysOfUser($steamID);
			$count = count($ret['data']);
			
			$ret['data'] = (int)$count;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getMatchesWithoutReplaysOfUser <br>\n";
		return $ret;
	}
}

?>