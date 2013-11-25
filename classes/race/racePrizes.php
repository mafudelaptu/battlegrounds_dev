<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class RacePrizes{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getRacePrizes($raceID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getRacePrizes <br>\n";
		if($raceID > 0){

			$sql = "SELECT rp.*, bpi.Name as BackpackName, bpi.Image as BackpackImage
					FROM `RacePrizes` rp LEFT JOIN BackpackItems bpi ON bpi.BackpackItemID = rp.BackpackItemID
					WHERE rp.RaceID = ".(int) $raceID." AND rp.Choosen = 0
							ORDER BY rp.RarityType DESC
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			// Array nach RareType Gruppieren
			if(is_array($data) && count($data) > 0){
				$data2 = array();
				foreach($data as $k =>$v){
					$rarityType = $v['RarityType'];
					$data2[$rarityType][] = $v;
				}
				$ret['data']  = $data2;
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}


		}
		else{
			$ret['status'] = "raceID = 0";
		}

		$ret['debug'] .= "End getRacePrizes <br>\n";

		return $ret;
	}

	function getPrizeCountOfRace($raceID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPrizeCountOfRace <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($raceID > 0){
			$sql = "SELECT *
					FROM `RacePrizes`
					WHERE RaceID = ".(int)($raceID)."
							";
			$count = $DB->countRows($sql);
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $count;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "raceID == 0";
		}
		$ret['debug'] .= "End getPrizeCountOfRace <br>\n";
		return $ret;
	}

	function getPrizeData($prizeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getPrizeData <br>\n";

		if($prizeID > 0){
			$sql = "SELECT rp.*, bpi.Name as BackpackName, bpi.Image as BackpackImage
					FROM `RacePrizes` rp LEFT JOIN BackpackItems bpi ON bpi.BackpackItemID = rp.BackpackItemID
					WHERE RacePrizeID = ".(int)($prizeID)."
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "prizeID == 0";
		}
		$ret['debug'] .= "End getPrizeData <br>\n";
		return $ret;
	}

}

?>