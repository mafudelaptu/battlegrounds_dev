<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserVoteCounts{
	const WeeklyUpvoteCount = 10;
	const WeeklyDownvoteCount = 5;

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getLastUpdate(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getLastUpdate <br>\n";


		$sql = "SELECT DISTINCT Updated
				FROM `UserVoteCounts`
				LIMIT 1";
		$data = $DB->select($sql);

		$ret['data'] = $data['Updated'];
		$ret['status'] = true;


		$ret['debug'] .= "End getLastUpdate <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function haveUserAlreadyUserVoteCounts(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start haveUserAlreadyUserVoteCounts <br>\n";
		$steamID = $_SESSION['user']['steamID'];

		$sql = "SELECT SteamID
				FROM `UserVoteCounts`
				WHERE SteamID = ".secureNumber($steamID)."
						";
		$count = $DB->countRows($sql);

		if ($count > 0) {
			$ret['data'] = true;
		}
		else {
			$ret['data'] = false;
		}

		$ret['status'] = true;

		$ret['debug'] .= "End haveUserAlreadyUserVoteCounts <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function userAllowToVote(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start userAllowToVote <br>\n";
		$steamID = $_SESSION['user']['steamID'];

		// initUserVoteCOunts wenn nötig
		$retInit = $this->initUserVoteCounts($steamID);

		$sql = "SELECT Upvotes, Downvotes
				FROM `UserVoteCounts`
				WHERE SteamID = ".secureNumber($steamID)."
						";
		$data = $DB->select($sql);


		$array = array();
		// check Upvotes
		if ($data['Upvotes'] > 0) {
			$array['upvotesAllowed']  = true;
			$array['upvotesCount']  = $data['Upvotes'];
		}
		else {
			$array['upvotesAllowed']  = false;
			$array['upvotesCount']  = 0;
		}

		// check Downvotes
		if ($data['Downvotes'] > 0) {
			$array['downvotesAllowed']  = true;
			$array['downvotesCount']  = $data['Downvotes'];
		}
		else {
			$array['downvotesAllowed']  = false;
			$array['downvotesCount']  = 0;
		}

		$ret['data'] = $array;
		$ret['status'] = true;

		$ret['debug'] .= "End userAllowToVote <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function initUserVoteCounts($steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start userAllowToVote <br>\n";

		$haveCounts = $this->haveUserAlreadyUserVoteCounts();
		if(!$haveCounts['data']){
			// initialen Count eintragen
			$insertArray = array();
			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['Upvotes'] = UserVoteCounts::WeeklyUpvoteCount;
			$insertArray['Downvotes'] = UserVoteCounts::WeeklyDownvoteCount;

			$DB->insert("UserVoteCounts", $insertArray);
		}
		$ret['status'] = true;

		$ret['debug'] .= "End userAllowToVote <br>\n";
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function updateVotesForUser($voteType, $steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		$ret['debug'] .= "Start updateVotesForUser <br>\n";
		$ret['debug'] .= p("HIERRERER::: ".$voteType."-".$steamID,1);
		if($steamID > 0){

			switch($voteType){
				case "1":
					$update = "Upvotes = Upvotes - 1";
					break;
				case "-1":
					$update = "Downvotes = Downvotes - 1";
					break;
			}

			if($update != ""){
				$sql = "UPDATE `UserVoteCounts`
						SET ".$update."
								WHERE SteamID = ".secureNumber($steamID)."
										";
				$ret['debug'] .= p($sql,1);

				$DB->update($sql);
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End updateVotesForUser <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function updateVotesOfUserByHand($steamID=0, $upvotesUpdate=1, $downvotesUpdate=1){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		$ret['debug'] .= "Start updateVotesOfUserByHand <br>\n";
		if($steamID > 0){

			$updateUP = "Upvotes = Upvotes + ".(int) $upvotesUpdate;


			$updateDOWN = "Downvotes = Downvotes + ".(int) $downvotesUpdate;


				
			$sql = "UPDATE `UserVoteCounts`
					SET ".$updateUP.", ".$updateDOWN." 
							WHERE SteamID = ".secureNumber($steamID)."
									";
			$ret['debug'] .= p($sql,1);

			$DB->update($sql);
				
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End updateVotesOfUserByHand <br>\n";

		return $ret;
	}

}

?>