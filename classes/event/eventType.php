<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class EventType{

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getEventTypeData($eventTypeID, $regionID=false){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getEventTypeData <br>\n";

		if($eventTypeID > 0){

			if($regionID){
				$region = $regionID;
			}
			else{
				$region = $_COOKIE['region'];
			}
			
			$sql = "SELECT *
					FROM `EventTypes`
					WHERE EventTypeID = ".(int)$eventTypeID." AND RegionID = ".(int) $region."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->select($sql);

			$ret['data']  =$data;

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "eventTypeID = 0";
		}

		$ret['debug'] .= "End getEventTypeData <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function calculateNextStartTimestamp($startTime, $startDay, $regionID=1){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start calculateNextStartTimestamp <br>\n";
		if($startTime != "" AND $startDay != ""){

			$timestamp = strtotime("next ".$startDay." ".$startTime);

			switch($regionID){
				case "1":
					$timestamp = $timestamp;
					break;
				case "7":
					$correction = -28800;
					$timestamp = $timestamp+$correction;
					break;
			}
			
			$ret['data'] = $timestamp;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "startTime = 0 AND startDay = 0";
		}

		$ret['debug'] .= "End calculateNextStartTimestamp <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getAllEventTypes(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllEventTypes <br>\n";

		$sql = "SELECT et.*,
				mm.Name as MMName, mm.Shortcut as MMShortcut,
				r.Name as RegionName, r.Shortcut as RShortcut,
				tt.Name as TTName, tt.Shortcut as TTShortcut,
				er.PointBorder as PointReq, lt.Name as LeagueReq,
				pt.Name as PrizeName, pt.Count as PrizeCount, pt.MaxCost as PrizeCost, pt.Type as PrizeType
				FROM `EventTypes` et
				JOIN MatchMode mm ON et.MatchModeID = mm.MatchModeID
				JOIN Region r ON r.RegionID = et.RegionID
				JOIN TournamentTypes tt ON tt.TournamentTypeID = et.TournamentTypeID
				JOIN EventRequirements er ON er.EventRequirementID = et.EventRequirementID
				LEFT JOIN LeagueType lt ON lt.LeagueTypeID = er.LeagueBorder
				JOIN PrizeType pt ON pt.PrizeTypeID = et.PrizeTypeID
				WHERE et.RegionID = ".(int) $_COOKIE['region']."
				";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);

		$ret['data'] = $data;
		$ret['status'] = true;


		$ret['debug'] .= "End getAllEventTypes <br>\n";

		return $ret;
	}

}


?>