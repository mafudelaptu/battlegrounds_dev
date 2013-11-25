<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class RaceWinner{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getWinnerData($raceID, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getWinnerData <br>\n";

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$sql = "SELECT *
					FROM `RaceWinner`
					WHERE SteamID = ".secureNumber($steamID)." AND RaceID = ".(int)$raceID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End getWinnerData <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getPickPositionOfRace($raceID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPickPositionOfRace <br>\n";
		if($raceID > 0){

			$sql = "SELECT *
					FROM `RaceWinner`
					WHERE RaceTypeID = ".(int) $raceID." AND RacePrizeID > 0
							ORDER BY Position DESC
							LIMIT 1
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			if(is_array($data) && count($data) > 0){
				$pos = $data['Position']+1;
			}
			else{
				$pos = 1;
			}
			$ret['data']  = (int) $pos;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "raceID = 0";
		}

		$ret['debug'] .= "End getPickPositionOfRace <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function setSelectedPrize($itemID, $raceID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start setSelectedPrize <br>\n";
		
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		
		if($itemID > 0){
			$sql = "UPDATE `RaceWinner` SET RacePrizeID = ".(int) $itemID."
								WHERE SteamID = ".secureNumber($steamID)." AND RaceID = ".(int)$raceID."
								";
			$ret['debug'] .= p($sql,1);
			$data = $DB->update($sql);
			$ret['debug'] .= p($data,1);
			
			// itemliste aktualisieren
			$sql = "UPDATE `RacePrizes` SET Choosen = 1
								WHERE RacePrizeID = ".(int) $itemID."
								
								";
			$ret['debug'] .= p($sql,1);
			$data2 = $DB->update($sql);
			$ret['debug'] .= p($data,1);
			
			
			
			$RacePrizes = new RacePrizes();
			$retRP = $RacePrizes->getPrizeData($itemID);
			$backPackItemID = $retRP['data']['BackpackItemID'];
			$ret['prizeTypeID'] = $retRP['data']['PrizeTypeID'];
			$ret['backpackItemID'] = (int)$backPackItemID;
			
			// wenn gewonnenes BackpackItem, dann user geben
			if($backpackItemID !== (int) 0){
				$insert = array();
				$insert['UserBackpackID'] = "''";
				$insert['SteamID'] = secureNumber($steamID);
				$insert['BackpackItemID'] = (int)$backPackItemID;
				$insert['GetTimestamp'] = (int)time();
				//$ret['debug'] .= p($DB->insert("UserBackpack", $insert, null, 1),1);
				$retIns = $DB->insert("UserBackpack", $insert);
				$ret['status'] = $retIns;
			}
			else{
				$ret['backpackItemIDTest'] = false;
				$ret['status'] = true;
			}
			
		}
		else{
			$ret['status'] = "itemID = 0";
		}

		$ret['debug'] .= "End setSelectedPrize <br>\n";

		return $ret;
	}
}

?>