<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
p("Start Race StartEndHandling");
$DB = new DB();
$con = $DB->conDB();

p("Start new Race Handling");
$aktuelleUhrzeitRace = date("H:i");
$aktuelleUhrzeitRace = "00:00";
if($aktuelleUhrzeitRace == "00:00"){
	// für jeden eingetragengen EventTyp der aktiv ist ein Event eintragen
	$sql = "SELECT *
		FROM `RaceType`
		WHERE Active = 1
		";
	p($sql);
	$data = $DB->multiSelect($sql);
	
	if(is_array($data) && count($data) > 0){
		foreach($data as $k =>$v){
			$raceTypeID = $v['RaceTypeID'];
			$raceData = $v;
			// kontrollieren was drin steht  sortiert nach EndDatum
			$sql = "SELECT *
				FROM `Race`
				WHERE RaceTypeID = ".(int)$raceTypeID."
						ORDER BY RaceID DESC
						LIMIT 1
						";
			p($sql);
			$data2 = $DB->select($sql);
	
			$Race = new Race();
	
			// wenn Datensatz da  dann kontrollieren ob EndTimestamp schon gesetzt
			// wenn ja dann neues Event eintragen
			if(is_array($data2) && count($data2) > 0){
				if($data2['Ended'] == 1){
					// neues Event in dem EventType anlegen
					$retIns = $Race->insertNewRace($raceTypeID, $raceData);
					if($retIns['status']){
						p($retIns['status']);
						p("neues Race mit RaceType:".$raceTypeID." eingetragen");
					}
				}
				else{
					p("neues Race bereits eingetragen");
				}
			}
			// das erste mal wird ein Event eingetragen
			else{
				$retIns = $Race->insertNewRace($raceTypeID, $raceData);
				if($retIns['status']){
					p($retIns);
					p("neues Race mit RaceType:".$raceTypeID." eingetragen");
				}
	
			}
		}
	}
	
}
else{
	p("noch nciht soweit: ".$aktuelleUhrzeitRace);
}
p("End new EVENT Handling");


// RaceTypeID
$Race = new Race();
$RaceType = new RaceType();
$retRTData = $Race->getActiveRace();
$raceData =  $retRTData['data'];
$raceTypeID = $retRTData['data']['RaceTypeID'];
$raceID =  $retRTData['data']['RaceID'];

/**
 * Items kontinuierlich auslesen/erneuern bis ende
 * 
 */
//$DomParser = new DomParser();
//$ret = $DomParser->parsePrizesFromInventory($raceTypeID);
//p($ret);
//p($retRTData);


$timestamp = time();

if(is_array($raceData) && count($raceData) > 0){
	//p($raceData);
	// wenn EndTimestamp kleiner als aktueller Timestamp dann sieger festlegen
	$endTimestamp = $raceData['EndTimestamp'];
	//$endTimestamp = 100;
	if($endTimestamp < $timestamp){
		// get WinnerList
		$UserPoints = new UserPoints();
		$RaceCoinPrizes = new RaceCoinPrizes();
		$retRCP = $RaceCoinPrizes->getAllCoinPrizesData();
		$retUP = $UserPoints->getRaceWinnerList($raceData['StartTimestamp'], $endTimestamp, $raceData['WinnerCount'], $raceData['WinnerCountType'], $retRCP['data']);
		p($retUP);
		$winnerList = $retUP['data'];
		
		// get count of prizes in last race
		$RacePrizes = new RacePrizes();
		$retRP = $RacePrizes->getPrizeCountOfRace($raceData['RaceID']);
		$count = $retRP['data'];
		
	
		
		if(is_array($winnerList) && count($winnerList) > 0){
			$insertArray = array();
			$insertCoinsArray = array();
			$i=1;
			foreach($winnerList as $k =>$v){
				$tmpArray = array();
				$tmpArray['SteamID'] = secureNumber($v['SteamID']);
				$tmpArray['RaceID'] = (int) $raceID;
				$tmpArray['RaceTypeID'] = (int)$raceTypeID;
				$tmpArray['Position'] = $i;

				$insertArray[] = $tmpArray;
				
				// Coin Array
				$coinArray = array();
				$coinArray['SteamID'] =  secureNumber($v['SteamID']);
				$coinArray['CoinsTypeID'] = 2; // Race-Win
				$coinArray['CoinsChange'] = (int) $v['CoinPrize'];
				$coinArray['Timestamp'] = time();
				$insertCoinsArray[] = $coinArray;
				
				// Notifications array
				$RaceCoinsPrizes = new RaceCoinPrizes();
				$retRCP = $RaceCoinsPrizes->getCoinPrizeByPlacement($i, $retRCP['data']);
				$coins = $retRCP['data']['RewardCoins'];
				
				$notArray = array();
				$notArray['SteamID'] = secureNumber($v['SteamID']);
				$notArray['TimestampCreated'] = time();
				$notArray['NotificationTypeID'] = 3; // general Notification
				
				if($count >= $i){
					$notArray['Text'] = "<h4>Congratulations!</h4> You are a Winner of the Race #".$raceData['RaceID'].". By clicking on [Ok] you can choose your Prize.";
					$notArray['Href'] = "chooseRacePrize.php?rID=".$raceData['RaceID'];
				}
				else{
					$notArray['Text'] = "<h4>Congratulations!</h4> You are a Winner of the Race #".$raceData['RaceID'].". You earned <b>".$coins."</b> N-GAGE.TV-Coins.";
					$notArray['Href'] = "";
				}
				
				$notArray['CreatedBy'] = 0;
				$notArray['Checked'] = 0;
				$notArray['TimestampChecked'] = 0;
				$insertNotArray[] = $notArray;
				
				$i++;
			}
			p("Winner");
			p($insertArray);
			
			$retIns = $DB->multiInsert("RaceWinner", $insertArray);
			
			p("insert Coins");
			$retIns2 = $DB->multiInsert("UserCoins", $insertCoinsArray);
			p($retIns2);
			
			p("insert notifications");
			p($insertNotArray);
			p($DB->multiInsert("UserNotifications", $insertNotArray, 1));
			$retIns3 = $DB->multiInsert("UserNotifications", $insertNotArray);
			p($retIns3);
			
			// Race auf inactiv setzen
			$sql = "UPDATE `Race` SET Ended = 1
								WHERE RaceID = ".(int) $raceID."
								";
			$ret['debug'] .= p($sql,1);
			$data = $DB->update($sql);
			$ret['debug'] .= p($data,1);
		}
		else{
			p("keine Winner");
		}
	}
	else{
		p("Es ist noch nicht so weit");
	}
}
else{
	p("kein active Race");
}

p("End Race StartEndHandling");

?>