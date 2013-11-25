<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class FakeMatches{

	private $testTotalCounts;
	private $testSuccessCounts;

	function unitTestMatches(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		global $testTotalCounts;
		global $testSuccessCounts;

		// TRUNCATE ALL
		$this->truncateAllTestMatches();

		// INSERT

			
		// INSERT dazu MatchDetails
		$userData = $this->get10RandomUser();

		//$ret['debug'] .= p($userData,1);

		for($i=0; $i<=5; $i++){
			for($j=0; $j<=5; $j++){
				for($k=0; $k<=$i; $k++){
					for($l=0; $l<=$j; $l++){
						//$ret['debug'] .= "[".$i.",".$j.",".$k.",".$l."] <br>\n";
						// INSERT new Match
						$matchID = $this->insertNewFoundMatch(1, 1, 1);

						$retInsertMD = $this->insertMatchDetails($matchID, $userData, $i, $k, $j, $l, 1200, 1200);
						//$ret['debug'] .= $retInsertMD['debug'];
						$ret['test'] .= $retInsertMD['test'];
						if($testTotalCounts > 0){
							$ret['ergebnis'] = round($testSuccessCounts/$testTotalCounts*100,2);
						}
					}
				}
			}
		}
			
			
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertNewFoundMatch($regionID, $matchTypeID, $matchModeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$insertArray["TeamWonID"] = -1;
		$insertArray["MatchTypeID"] = (int) $matchTypeID;
		$insertArray["MatchModeID"] = (int) $matchModeID;
		$insertArray['TimestampCreated'] = time();
		$insertArray["Region"] = (int) $regionID;

		//$DB->insert("Match",$insertArray);
		$matchID = mysql_insert_id();
			
		$ret = $matchID;
			
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertMatchDetails($matchID, $userData, $anz_subs_team1, $anz_subs_win_team1, $anz_subs_team2, $anz_subs_win_team2, $elo_team1, $elo_team2){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= p("Start insertMatchDetails",1);
		if($matchID > 0){
			// INSERT User in MatchDetails
			$retInsertUser = $this->insertAllUsersInMatchDetails($matchID, $userData, $elo_team1, $elo_team2);
			$ret['debug'] .= $retInsertUser['debug'];

			// UPDATE Submission
			$retUpdateUser = $this->updateUserSubmissions($matchID, $retInsertUser['data'], $anz_subs_team1, $anz_subs_win_team1, $anz_subs_team2, $anz_subs_win_team2);
			$ret['debug'] .= $retUpdateUser['debug'];

			$data = $retUpdateUser['data'];

			// Test durchführen
			$retTests = $this->testDataset($data, $anz_subs_team1, $anz_subs_win_team1, $anz_subs_team2, $anz_subs_win_team2);
			$ret['test'] = $retTests['test'];

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID 0";
		}
		$ret['debug'] .= p("End insertMatchDetails",1);
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function get10RandomUser(){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$sql = "SELECT SteamID
				FROM User
				ORDER BY RAND()
				LIMIT 10
				";
		$data = $DB->multiSelect($sql);
		$ret = $data;
		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertAllUsersInMatchDetails($matchID, $data, $elo_team1, $elo_team2){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= p("Start insertAllUsersInMatchDetails",1);
		if($matchID > 0){
			//$ret['debug'] .= p($data,1);
			if(is_array($data) && count($data) > 0){
				$i=0;
				$insertArray = array();
				foreach($data as $k => $v){
					$tmp = array();
					$steamID = $v['SteamID'];
					if($i<5){
						$teamID = 1;
						$rank = $elo_team1;
					}
					else{
						$teamID = 2;
						$rank = $elo_team2;
					}

					$tmp['MatchID'] = $matchID;
					$tmp['SteamID'] = $steamID;
					$tmp['TeamID'] = $teamID;
					$tmp['Rank'] = $rank;

					$insertArray[] = $tmp;
					$i++;
				}
				//$ret['debug'] .= p($insertArray,1);
				//	$DB->multiInsert("MatchDetails", $insertArray);
				$ret['data'] = $insertArray;
				$ret['status'] = true;
			}

		}
		else{
			$ret['status'] = "SteamID 0";
		}

		$ret['debug'] .= p("End insertAllUsersInMatchDetails",1);

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function truncateAllTestMatches(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$sql = "TRUNCATE TABLE `TestMatch`;";
		$DB->select($sql,0);

		$sql = "TRUNCATE TABLE `TestMatchDetails`;";
		$DB->select($sql,0);

		return true;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function updateUserSubmissions($matchID, $MDdata, $anz_subs_team1, $anz_subs_win_team1, $anz_subs_team2, $anz_subs_win_team2){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($matchID > 0){


			if(is_array($MDdata) && count($MDdata) > 0){
				$countSubsTeam1 = $anz_subs_team1;
				$countSubsTeam2 = $anz_subs_team2;

				$countSubsWinTeam1 = $anz_subs_win_team1;
				$countSubsWinTeam2 = $anz_subs_win_team2;
				foreach($MDdata as $k => $v){
					$steamID = $v['SteamID'];
					$teamID = $v['TeamID'];


					if($teamID == 1){
						$countSubs = $countSubsTeam1;
						$countSubsWin = $countSubsWinTeam1;
					}
					else{
						$countSubs = $countSubsTeam2;
						$countSubsWin = $countSubsWinTeam2;
					}

					// generelle Submitts
					if($countSubs > 0){
						$submitted = 1;
						($teamID == 1 ? $countSubsTeam1-- : $countSubsTeam2--);

						// Win Submitts
						if($countSubsWin > 0){
							$submittedFor = 1;
							($teamID == 1 ? $countSubsWinTeam1-- : $countSubsWinTeam2--);
						}
						else{
							$submittedFor = -1;
						}
					}
					else{
						$submitted = 0;
						$submittedFor = 0;
					}

					$sql = "UPDATE `MatchDetails`
							SET Submitted = ".(int) $submitted.", SubmissionFor = ".(int) $submittedFor.", SubmissionTimestamp = ".time()."
									WHERE MatchID = ".(int) $matchID." AND SteamID = ".secureNumber($steamID)."";
					//$DB->update($sql);
					$MDdata[$k]['Submitted'] = $submitted;
					$MDdata[$k]['SubmissionFor'] = $submittedFor;
					//$ret['debug'] .= p($sql, 1);

				}

				//$ret['debug'] .= p($MDdata,1);
			}
			$ret['data'] = $MDdata;

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID 0";
		}
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function testDataset($data, $anz_subs_team1, $anz_subs_win_team1, $anz_subs_team2, $anz_subs_win_team2){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		global $testSuccessCounts;
		global $testTotalCounts;

		// erwartete Werte bestimmen
		$ret['test'] .= "<p>";
		$ret['test'] .= "<strong>Test für: $anz_subs_team1, $anz_subs_win_team1, $anz_subs_team2, $anz_subs_win_team2: </strong>";

		// genug submits Test
		// Data nur submissions filtern
		if(is_array($data) && count($data) > 0){
			$nurSubmitsData = array();
			foreach($data as $k => $v){
				if($v['Submitted'] == 1){
					$nurSubmitsData[] = $data[$k];
				}
			}
		}



		$c[0] = ((count($nurSubmitsData) >= (10-MatchDetails::screenshotGrenze)) ? 'success' : 'important');
		$ret['test'] .= "<span class='label label-$c[0]'>Submits</span>";


		// ungewöhnliche Submits test
		$MatchDetails = new MatchDetails();
		$falscherWert = $MatchDetails->checkForStrangeWinLoseSubmissionData($nurSubmitsData);
		$falscherWert = $falscherWert['falscherWert'];


		$c[1] = ($falscherWert <= MatchDetails::screenshotGrenze ? 'success' : 'important');

		$ret['test'] .= '<span class="label label-'.$c[1].'">ScreenshotGrenze</span>&nbsp;';
			
		// TeamWOn test
		if(is_array($data) && count($data) > 0){
			$nurWinSubmits = array();
			foreach($data as $k => $v){
				if($v['Submitted'] == 1 && $v['SubmissionFor'] == 1){
					$nurWinSubmits[] = $data[$k];
				}
			}
		}
		$teamWon = $MatchDetails->getTeamWonData($nurWinSubmits);
		$teamWonID = $teamWon['teamWonID'];
		$c[2] = ($teamWonID != "-1" ? 'success' : 'important');
		$ret['test'] .= 'TeamWon: <span class="label label-'.$c[2].'">'.$teamWonID.'</span>&nbsp;';

		$ret['test'] .= "</p>";

		$testTotalCounts++;

		if(!in_array("important", $c)){
			$testSuccessCounts++;
		}

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertRandomTestMatches(){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertRandomTestMatches <br>\n";

			

		//$ret['debug'] .= p($userData,1);
		for($a=0; $a<200; $a++){
			for($i=5; $i<=5; $i++){
				for($j=5; $j<=5; $j++){
					for($k=4; $k<=$i; $k++){
						for($l=0; $l<=1; $l++){
							//$ret['debug'] .= "[".$i.",".$j.",".$k.",".$l."] <br>\n";
							// INSERT dazu MatchDetails
							$userData = $this->get10RandomUser();

							$randomMatchType = 1;

							$randomMatchMode = rand(1, 11);

							// INSERT new Match
							$matchID = $this->insertNewFoundMatch2(1, $randomMatchType, $randomMatchMode);

							$randomEloTeam1 = rand(1000, 1400);
							$randomEloTeam2 = rand(1000, 1400);

							$retInsertMD = $this->insertMatchDetails2($matchID, $userData, $i, $k, $j, $l, $randomEloTeam1, $randomEloTeam2);
							$ret['debug'] .= $retInsertMD['debug'];
						}
					}
				}
			}
		}

		$ret['debug'] .= "End insertRandomTestMatches <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertNewFoundMatch2($regionID, $matchTypeID, $matchModeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$insertArray["TeamWonID"] = -1;
		$insertArray["MatchTypeID"] = (int) $matchTypeID;
		$insertArray["MatchModeID"] = (int) $matchModeID;
		$insertArray['TimestampCreated'] = time();
		$insertArray["Region"] = (int) $regionID;

		$DB->insert("Match",$insertArray);
		$matchID = mysql_insert_id();
			
		$ret = $matchID;
			
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertMatchDetails2($matchID, $userData, $anz_subs_team1, $anz_subs_win_team1, $anz_subs_team2, $anz_subs_win_team2, $elo_team1, $elo_team2){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= p("Start insertMatchDetails2",1);
		if($matchID > 0){
			// INSERT User in MatchDetails
			$retInsertUser = $this->insertAllUsersInMatchDetails2($matchID, $userData, $elo_team1, $elo_team2);
			$ret['debug'] .= $retInsertUser['debug'];

			// UPDATE Submission
			$retUpdateUser = $this->updateUserSubmissions2($matchID, $retInsertUser['data'], $anz_subs_team1, $anz_subs_win_team1, $anz_subs_team2, $anz_subs_win_team2);
			$ret['debug'] .= $retUpdateUser['debug'];

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID 0";
		}
		$ret['debug'] .= p("End insertMatchDetails2",1);
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertAllUsersInMatchDetails2($matchID, $data, $elo_team1, $elo_team2){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= p("Start insertAllUsersInMatchDetails2",1);
		if($matchID > 0){
			//$ret['debug'] .= p($data,1);
			if(is_array($data) && count($data) > 0){
				$i=0;
				$insertArray = array();
				foreach($data as $k => $v){
					$tmp = array();
					$steamID = $v['SteamID'];
					if($i<5){
						$teamID = 1;
						$rank = $elo_team1;
					}
					else{
						$teamID = 2;
						$rank = $elo_team2;
					}

					$tmp['MatchID'] = $matchID;
					$tmp['SteamID'] = $steamID;
					$tmp['TeamID'] = $teamID;
					$tmp['Elo'] = $rank;

					$insertArray[] = $tmp;
					$i++;
				}
				//$ret['debug'] .= p($insertArray,1);
				$DB->multiInsert("MatchDetails", $insertArray);
				$ret['data'] = $insertArray;
				$ret['status'] = true;
			}

		}
		else{
			$ret['status'] = "MatchID 0";
		}

		$ret['debug'] .= p("End insertAllUsersInMatchDetails2",1);

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function updateUserSubmissions2($matchID, $MDdata, $anz_subs_team1, $anz_subs_win_team1, $anz_subs_team2, $anz_subs_win_team2){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($matchID > 0){


			if(is_array($MDdata) && count($MDdata) > 0){
				$countSubsTeam1 = $anz_subs_team1;
				$countSubsTeam2 = $anz_subs_team2;

				$countSubsWinTeam1 = $anz_subs_win_team1;
				$countSubsWinTeam2 = $anz_subs_win_team2;
				foreach($MDdata as $k => $v){
					$steamID = $v['SteamID'];
					$teamID = $v['TeamID'];


					if($teamID == 1){
						$countSubs = $countSubsTeam1;
						$countSubsWin = $countSubsWinTeam1;
					}
					else{
						$countSubs = $countSubsTeam2;
						$countSubsWin = $countSubsWinTeam2;
					}

					// generelle Submitts
					if($countSubs > 0){
						$submitted = 1;
						($teamID == 1 ? $countSubsTeam1-- : $countSubsTeam2--);

						// Win Submitts
						if($countSubsWin > 0){
							$submittedFor = 1;
							($teamID == 1 ? $countSubsWinTeam1-- : $countSubsWinTeam2--);
						}
						else{
							$submittedFor = -1;
						}
					}
					else{
						$submitted = 0;
						$submittedFor = 0;
					}

					$sql = "UPDATE `MatchDetails`
							SET Submitted = ".(int) $submitted.", SubmissionFor = ".(int) $submittedFor.", SubmissionTimestamp = ".time()."
									WHERE MatchID = ".(int) $matchID." AND SteamID = ".secureNumber($steamID)."";
					$DB->update($sql);

					//$ret['debug'] .= p($sql, 1);

				}

				//$ret['debug'] .= p($MDdata,1);
			}

			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID 0";
		}
		return $ret;
	}


}

?>