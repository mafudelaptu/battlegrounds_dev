<?php 

/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
$DB = new DB();
$con = $DB->conDB();

p("START coinsHandling");

p("Start ReferAFriend Coin handling");
$aktuelleUhrzeit2 = date("i");
//$aktuelleUhrzeit2 = "00";
if($aktuelleUhrzeit2 == "00"){

	$ReferAFriend = new ReferAFriend();
	$matchDetailsCountSQL = "SELECT IF(Count(*) >= ".(int) ReferAFriend::refererCountBorder.", 1, 0) Count
			FROM `Match` m JOIN MatchDetails md ON md.MatchID = m.MatchID
			WHERE md.SteamID = raf.ReferedSteamID AND m.Canceled = 0 AND m.ManuallyCheck = 0 AND TeamWonID != -1";
	$coinCount = "SELECT COUNT(SteamID) as Count
			FROM UserCoins uc
			WHERE CoinsTypeID = 1 AND uc.SteamID = raf.SteamID";
	
	$sql = "SELECT raf.*, GROUP_CONCAT((".$matchDetailsCountSQL.") SEPARATOR '|') as ReferedCounts, (".$coinCount.") as CoinCount
			FROM `ReferAFriend` raf
			GROUP BY raf.SteamID
			";
	$data = $DB->multiSelect($sql);
	$ret['debug'] .= p($sql,1);
	if(is_array($data) && count($data) > 0){
		foreach ($data as $k => $v) {
			$ret['debug'] .= p($v,1);
			$referedCounts = $v['ReferedCounts'];
			$coinCount = $v['CoinCount'];
			$steamID = $v['SteamID'];
			$referedSteamID = $v['ReferedSteamID'];
			
			// Anzahl Refered FRiend berechnen
			$ret['debug'] .= p("referedCounts:".$referedCounts,1);
			if(strpos($referedCounts, "|") > 0){
				$ret['debug'] .= p("| drin",1);
				$tmp = explode("|", $referedCounts);
				$sum = 0;
				if(is_array($tmp) && count($tmp) > 0){
					foreach ($tmp as $k => $v) {
						$sum += $v;
					}
				}
			}
			else{
				$ret['debug'] .= p("| net da",1);
				$sum = $referedCounts;
			}
			$ret['debug'] .= p($sum,1);
			$referedCount = (int) $sum;
			// Wenn größer oder ungleich null, dann coins eintragen
			if($referedCount > $coinCount && $referedCount != 0){
				// differenz eintragen
				$diff = $referedCount - $coinCount;
				$insertArray = array();
				for ($i = 0; $i < (int)$diff; $i++) {
					$data = array();
					$data['SteamID'] = secureNumber($steamID);
					$data['CoinsTypeID'] = 1;
					$data['CoinsChange'] = (int) ReferAFriend::refererCoinBonus;
					$data['Timestamp'] = time();
					$insertArray[] = $data;
				}
				$ret['debug'] .= p($insertArray,1);
				$retIns = $DB->multiInsert("UserCoins", $insertArray);
				$ret['debug'] .= p($DB->multiInsert("UserCoins", $insertArray,1),1);
				$ret['debug'] .= p("insert:".$retIns,1);
			}
		}
	}
}
echo $ret['debug'];
p("End ReferAFriend Coin handling");

p("END coinsHandling");

?>