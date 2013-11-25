<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class CreatedEvents{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertNewCreatedEvent($eventID, $eventTypeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertNewCreatedEvent <br>\n";
		if($eventID > 0 && $eventTypeID > 0){

			$insertArray = array();
			$insertArray['EventID'] = (int) $eventID;
			$insertArray['EventTypeID'] = (int) $eventTypeID;
			$insertArray['EndTimestamp'] = (int) 0;
			$insertArray['TeamWonID'] = (int) 0;
			$insertArray['Canceled'] = (int) 0;

			$retIns = $DB->insert("CreatedEvents", $insertArray);
			$lastID = mysql_insert_id();
			$ret['data'] = (int)$lastID;
			$ret['status'] = $retIns;
		}
		else{
			$ret['status'] = "eventID = 0 AND eventTypeID = 0";
		}

		$ret['debug'] .= "End insertNewCreatedEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getCreatedEventData($createdEventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getCreatedEventData <br>\n";
		if( $createdEventID > 0){
			$sql = "SELECT *
					FROM `CreatedEvents`
					WHERE CreatedEventID = ".(int) $createdEventID."
							";
			$data = $DB->select($sql);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "createdEventID = 0";
		}


		$ret['debug'] .= "End getCreatedEventData <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function cancelCreatedEvent($createdEventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start cancelCreatedEvent <br>\n";

		if($createdEventID > 0 ){
			$sql = "UPDATE `CreatedEvents`
					SET Canceled = 1, EndTimestamp = ".time()."
							WHERE CreatedEventID = ".(int) $createdEventID."
									";
			$retUpdate = $DB->update($sql);
			$ret['status'] = $retUpdate;
		}
		else{
			$ret['status'] = "createdEventID = 0";
		}


		$ret['debug'] .= "End cancelCreatedEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function setWinnerForCreatedEvent($createdEventID, $teamWonID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start setWinnerForCreatedEvent <br>\n";

		if($createdEventID > 0 && $teamWonID > 0){
			$sql = "UPDATE `CreatedEvents`
					SET TeamWonID = ".(int) $teamWonID.", EndTimeStamp = ".time()."
							WHERE CreatedEventID = ".(int) $createdEventID."
									";
			$retUpdate = $DB->update($sql);
			$ret['status'] = $retUpdate;
		}
		else{
			$ret['status'] = "createdEventID = 0 | teamWOnID 0";
		}


		$ret['debug'] .= "End setWinnerForCreatedEvent <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllCreatedEventsOfEvent($eventID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllCreatedEventsOfEvent <br>\n";
		
		if($eventID > 0){
			$sql = "SELECT *
							FROM `CreatedEvents`
							WHERE EventID = ".(int)$eventID."
			";
			$data = $DB->multiSelect($sql);
			$ret['data'] = $data; 
			$ret['debug'] .= p($sql,1);
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventID 0";
		}
		
		$ret['debug'] .= "End getAllCreatedEventsOfEvent <br>\n";
		return $ret;
	}
}

?>