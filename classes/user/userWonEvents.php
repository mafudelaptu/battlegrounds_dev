<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserWonEvents{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertUserWonEvent($steamID, $eventID, $createdEventID) {
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertUserWonEvent <br>\n";

		if($eventID > 0 && $createdEventID > 0 && $steamID > 0){
			$insertArray = array();
			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['EventID'] = (int) $eventID;
			$insertArray['CreatedEventID'] = (int) $createdEventID;
			
			$retINs = $DB->insert("UserWonEvents", $insertArray);
			
			$ret['status'] = $retINs;
		}
		else{
			$ret['status'] = "eventID = 0 | createdEventID = 0 | teamID = 0";
		}


		$ret['debug'] .= "End insertUserWonEvent <br>\n";

		return $ret;
	}

}

?>