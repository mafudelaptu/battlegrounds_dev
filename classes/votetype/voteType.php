<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class VoteType{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getVoteTypes($type="all"){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getVoteTypes <br>\n";

		switch($type){
			case "1":
				// nur positive Votes
				$typeSQL = "AND Type = 1";
				break;
			case "-1":
				// nur positive Votes
				$typeSQL = "AND Type = -1";
				break;
			default:
				$typeSQL = "";
				break;
		}
		
		$sql = "SELECT *
				FROM `VoteType`
				WHERE Display = 1 ".$typeSQL."
				";
		$data = $DB->multiSelect($sql);
		
		$ret['debug'] .= p($data,1);
		
		$ret['data'] = $data;
		$ret['status'] = "MatchID = 0";


		$ret['debug'] .= "End getVoteTypes <br>\n";

		return $ret;
	}
}

?>