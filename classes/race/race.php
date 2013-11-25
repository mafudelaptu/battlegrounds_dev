<?php 

class Race{

	function insertNewRace($raceTypeID, $raceData) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertNewRace <br>\n";

		if($raceTypeID > 0){

			// Timestamps (start + end) berechnen
			$retThis = $this->getNextRaceTimestamps($raceData);
			$start = $retThis['start'];
			$end = $retThis['end'];
			
			$data = array();
			$data['RaceTypeID'] = (int) $raceTypeID;
			$data['StartTimestamp'] = (int) $start;
			$data['EndTimestamp'] = (int) $end;
			$data['Ended'] = (int) 0;
			
			$retIns = $DB->insert("Race", $data);
			$ret['data'] = $data;
			$ret['status'] = $retIns;
		}
		else{
			$ret['status'] = "raceTypeID == 0";
		}
		$ret['debug'] .= "End insertNewRace <br>\n";
		return $ret;
	}

	function getNextRaceTimestamps($raceData){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getNextRaceTimestamps <br>\n";

		if(is_array($raceData) && count($raceData) > 0){
			 //get start date
			 $startDay = $raceData['StartDay'];
			 $startTimestamp = strtotime("next ".$startDay);
			 
			 // end Timestamp
			 $duration = $raceData['StrToTimeDuration'];
			 $endTimestamp = strtotime($duration, $startTimestamp);
			 
			 $ret['start'] = (int) $startTimestamp;
			 $ret['end'] = (int) $endTimestamp;
			 $ret['status'] = true;
		}
		else{
			$ret['status'] = "matchID == 0";
		}
		$ret['debug'] .= "End getNextRaceTimestamps <br>\n";
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getActiveRace(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getActiveRace <br>\n";
	
		$sql = "SELECT *
							FROM Race r JOIN `RaceType` rt ON r.RaceTypeID = rt.RaceTypeID
							WHERE r.Ended = 0 AND rt.Active = 1
				";
		$ret['debug'] .= p($sql,1);
		$data = $DB->select($sql);
	
		$ret['data']  =$data;
		$ret['status'] = true;
	
		$ret['debug'] .= "End getActiveRace <br>\n";
	
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getRaceData($raceID){
	
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getRaceData <br>\n";
		if($raceID > 0){
			$sql = "SELECT *
					FROM `Race` r JOIN `RaceType` rt ON r.RaceTypeID = rt.RaceTypeID
					WHERE r.RaceID = ".(int) $raceID."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);
			$ret['data']  =$data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "raceID = 0";
		}
	
		$ret['debug'] .= "End getRaceData <br>\n";
	
		return $ret;
	
	}
	
}

?>