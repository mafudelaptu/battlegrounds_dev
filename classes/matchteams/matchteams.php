<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class MatchTeams{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertPlayersIntoMatchTeamsAfterFoundMatchmaking($sql, $data=array()){
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= $sql;

		$steamID = $_SESSION['user']['steamID'];
		//p($sql);
		$ret['debug'] .= p($data,1);
		$ret['debug'] .= p("HAHA HIER UDN SO",1);

		$log = new KLogger ( "log.txt" , KLogger::INFO );
		$log->LogInfo("found match SQL:".$sql." durch SteamID:".$steamID." Daten: ".print_r($data,1));	//Prints to the log file


		if($sql != ""){

			// neuer besserer Ansatz
			if(is_array($data) && count($data) > 0){
				$matchModeID = $data[0]['MatchModeID'];
				$matchTypeID = $data[0]['MatchTypeID'];

				$regionID = $data[0]['Region'];



				// Wenn wirklich 10 Spieler gefunden

				if(count($data) == 10){

					// neues Match eintragen
					$Match = new Match();
					$matchID = $Match->insertNewFoundMatch($regionID, $matchTypeID, $matchModeID);
					$matchID = $matchID['matchID'];

					$insertArray = array();

					// Gruppen-Typen vorbereiten
					$groupArray = array();
					foreach ($data as $k => $v){
						if($v['GroupID'] > 0){
							$tmpArray = array();
							$tmpArray['MatchID'] = (int) $matchID;
							$tmpArray['SteamID'] = secureNumber($v['SteamID']);
							$tmpArray['TeamID'] = (int) $teamID;
							$tmpArray['Elo'] = (int) $v['Elo'];
							$tmpArray['dataKey'] = (int) $k;
							$groupArray[$v['GroupID']][] = $tmpArray;
						}
					}
					$ret['debug'] .= p("GRROUUP ARRAAAAAAAAAAY",1);
					$ret['debug'] .= p($groupArray,1);

					$i=0;
					if(is_array($groupArray) && count($groupArray) > 0){
						$countTeam = array();
						foreach($groupArray as $k =>$v){
							if($i%2==0){
								$teamID = 2;
							}
							else{
								$teamID = 1;
							}
							$ret['debug'] .= p("ITERATION:".$i." Team:".$teamID,1);
							if(is_array($v) && count($v) > 0){
								foreach($v as $kk =>$vv){
									$tmpArray = array();
									$tmpArray['MatchID'] = (int) $matchID;
									$tmpArray['SteamID'] = secureNumber($vv['SteamID']);
									$tmpArray['TeamID'] = (int) $teamID;
									$tmpArray['Rank'] = (int) $vv['Elo'];
									$tmpArray['Ready'] = 0;

									$insertArray[] = $tmpArray;

									// aus Data entfernen
									unset($data[$vv['dataKey']]);
									$countTeam[$teamID]++;
								}
							}
							$i++;
						}
					}
					$ret['debug'] .= p("�briges DATA ARRRRAAAAYY",1);
					$ret['debug'] .= p($data,1);
					$ret['debug'] .= p("Bereits gef�lltes Insert ARRAAYYY",1);
					$ret['debug'] .= p($insertArray,1);
					$ret['debug'] .= p("COUNT TEAMS",1);
					$ret['debug'] .= p($countTeam,1);
					foreach($data as $k => $v){
						$tmpArray = array();
						if($i%2==0){

							$teamID = 2;

						}
						else{
							$teamID = 1;
						}
						$minAveTeamNumberRet = getMinAveEloOfTeam($team1, $team2);
						$minAveTeamNumber = $minAveTeamNumberRet['data'];

						// 						if($countTeam[$teamID] < 5){
						// 							$tmpArray['MatchID'] = (int) $matchID;
						// 							$tmpArray['SteamID'] = secureNumber($v['SteamID']);
						// 							$tmpArray['TeamID'] = (int) $teamID;
						// 							$tmpArray['Rank'] = (int) $v['Elo'];
						// 							$tmpArray['Ready'] = 0;

						// 							$insertArray[] = $tmpArray;
						// 							$countTeam[$teamID]++;
						// 						}
						// 						else{
						// 							if($teamID == 1){
						// 								$teamID = 2;
						// 							}
						// 							else{
						// 								$teamID = 1;
						// 							}
						// 							$tmpArray['MatchID'] = (int) $matchID;
						// 							$tmpArray['SteamID'] = secureNumber($v['SteamID']);
						// 							$tmpArray['TeamID'] = (int) $teamID;
						// 							$tmpArray['Rank'] = (int) $v['Elo'];
						// 							$tmpArray['Ready'] = 0;

						// 							$insertArray[] = $tmpArray;
						// 							$countTeam[$teamID]++;
						// 						}

						if($countTeam[$minAveTeamNumber] < 5){
							$tmpArray['MatchID'] = (int) $matchID;
							$tmpArray['SteamID'] = secureNumber($v['SteamID']);
							$tmpArray['TeamID'] = (int) $minAveTeamNumber;
							$tmpArray['Rank'] = (int) $v['Elo'];
							$tmpArray['Ready'] = 0;

							$insertArray[] = $tmpArray;
							$countTeam[$minAveTeamNumber]++;
						}
						else{
							if($minAveTeamNumber == 1){
								$teamID = 2;
							}
							else{
								$teamID = 1;
							}
							$tmpArray['MatchID'] = (int) $matchID;
							$tmpArray['SteamID'] = secureNumber($v['SteamID']);
							$tmpArray['TeamID'] = (int) $teamID;
							$tmpArray['Rank'] = (int) $v['Elo'];
							$tmpArray['Ready'] = 0;

							$insertArray[] = $tmpArray;
							$countTeam[$teamID]++;
						}

						$i++;
						}

						// Eintragen in MatchTeams
						$ret['debug'] .= p($countTeam,1);
						$ret['debug'] .= p($insertArray,1);
						$ret['debug'] .= p($DB->multiInsert("MatchTeams", $insertArray,1),1);
						$DB->multiInsert("MatchTeams", $insertArray);
						$log->LogInfo("MatchTeams eingetragen durch SteamID:".$steamID);	//Prints to the log file


						$retTest .= $retKick['debug'];

						// Ret Werte setzen
						$ret['matchID'] = $matchID;
						$ret['status'] = true;
					}
					else{
						$ret['status'] = "keine 10 Spieler gefunden!";
					}

				}
				// Quasi alt
				else{
					$data = $DB->multiSelect($sql);

					$where = "";
					foreach($data as $k => $v){
						if($where != ""){
							$where .= " OR ";
						}
						$where .= "SteamID = ".$v['SteamID'];
					}
					$sql = "SELECT *
							FROM Queue
							WHERE ".$where."";
					$queueData = $DB->multiSelect($sql);
					//      p($sql);
					if(is_array($queueData) && count($queueData) > 0){
						foreach($queueData as $k =>$v){
							$regionArray[] = $v['Region'];
							$matchModeArray[] = $v['MatchModeID'];
							$matchTypeArray[] = $v['MatchTypeID'];
						}

						$regionCounts = array_count_values($regionArray);
						$matchModeCounts = array_count_values($matchModeArray);
						$matchTypeCounts = array_count_values($matchTypeArray);

						//p($matchModeCounts);

						asort($regionCounts);
						asort($matchModeCounts);
						asort($matchTypeCounts);

						end($regionCounts);
						end($matchModeCounts);
						end($matchTypeCounts);

						//p($regionCounts);

						$regionID = key($regionCounts);
						$matchModeID = key($matchModeCounts);
						$matchTypeID = key($matchTypeCounts);
					}
					//p("R:".$regionID." M:".$matchModeID." MT:".$matchTypeID);
					// insertStatement zusammenbauen
					if(is_array($data) AND count($data)>0){
						$insertStatement = "";
						if(count($data) == 9){

							// neues Match eintragen
							$Match = new Match();
							$matchID = $Match->insertNewFoundMatch($regionID, $matchTypeID, $matchModeID);
							$matchID = $matchID['matchID'];
							$i=0;
							foreach($data as $k => $v){
								if($insertStatement != ""){
									$insertStatement .= ",";
								}
								if($i%2==0){
									$teamID = 2;
								}
								else{
									$teamID = 1;
								}
								$insertStatement .= "( ".(int)$matchID.", ".secureNumber($v['SteamID']).", ".(int)$teamID.", ".(int)$v['Elo'].", 0 )";

								$i++;
							}
							$sql = "INSERT INTO MatchTeams
									VALUES ".$insertStatement."
											";
							$DB->select($sql,0);


							// den USer der es gefunden hat natürlioch auch eintragen
							// Elo asulesen
							$UserElo = new UserElo();
							$elo = $UserElo->getEloOfMatchMode($steamID, $matchTypeID, $matchModeID);
							$insertArray['MatchID'] = (int) $matchID;
							$insertArray['SteamID'] = secureNumber($steamID);

							$insertArray['TeamID'] = 1;
							$insertArray['Rank'] = (int) $elo;
							$DB->insert("MatchTeams", $insertArray);

							$ret['matchID'] = $matchID;
							$ret['status'] = true;
						}
						else{
							$ret['status'] = "keine 10 datensÃ¤tze";
						}
					}
				}


			}
			else{
				$ret['status'] = "sql leer";
			}

			return $ret;
		}
		/*
		 * Copyright 2013 Artur Leinweber
		* Date: 2013-01-01
		*/
		function insertPlayersIntoMatchTeamsAfterFoundMatchmaking2($data){
			$DB = new DB();
			$con = $DB->conDB();
			$ret['debug'] .= $sql;

			$steamID = $_SESSION['user']['steamID'];
			//p($sql);
			$ret['debug'] .= p($data,1);
			$ret['debug'] .= p("HAHA HIER UDN SO",1);

			// neuer besserer Ansatz
			if(is_array($data) && count($data) > 0){
				$matchModeID = $data[0]['MatchModeID'];
				$matchTypeID = $data[0]['MatchTypeID'];

				$regionID = $data[0]['Region'];
				switch($matchTypeID){
					case "8":
						// Wenn wirklich 2 Spieler gefunden
						if(count($data) == 2){

							// neues Match eintragen mit Matchdetails
							$Match = new Match();
							$matchID = $Match->insertNewFoundMatch2($regionID, $matchTypeID, $matchModeID);
							$matchID = $matchID['matchID'];


							$insertArray = array();

								
							$ret['debug'] .= p("�briges DATA ARRRRAAAAYY",1);
							$ret['debug'] .= p($data,1);
							$ret['debug'] .= p("Bereits gef�lltes Insert ARRAAYYY",1);
							$ret['debug'] .= p($insertArray,1);
							$ret['debug'] .= p("COUNT TEAMS",1);
							$ret['debug'] .= p($countTeam,1);
							foreach($data as $k => $v){
								$tmpArray = array();
								if($i%2==0){

									$teamID = 2;

								}
								else{
									$teamID = 1;
								}

								if($countTeam[$teamID] < 5){
									$tmpArray['MatchID'] = (int) $matchID;
									$tmpArray['SteamID'] = secureNumber($v['SteamID']);
									$tmpArray['TeamID'] = (int) $teamID;
									$tmpArray['Rank'] = (int) $v['Elo'];
									$tmpArray['Ready'] = 0;

									$insertArray[] = $tmpArray;
									$countTeam[$teamID]++;
								}
								else{
									if($teamID == 1){
										$teamID = 2;
									}
									else{
										$teamID = 1;
									}
									$tmpArray['MatchID'] = (int) $matchID;
									$tmpArray['SteamID'] = secureNumber($v['SteamID']);
									$tmpArray['TeamID'] = (int) $teamID;
									$tmpArray['Rank'] = (int) $v['Elo'];
									$tmpArray['Ready'] = 0;

									$insertArray[] = $tmpArray;
									$countTeam[$teamID]++;
								}

								$i++;
							}

							// Eintragen in MatchTeams
							$ret['debug'] .= p($countTeam,1);
							$ret['debug'] .= p($insertArray,1);
							$ret['debug'] .= p($DB->multiInsert("MatchTeams", $insertArray,1),1);
							$DB->multiInsert("MatchTeams", $insertArray);

							// MatchDetails eintreagen
							$retInsMD = $Match->saveMatchDetails2($matchID);

							$ret['debug'] .= p($retInsMD,1);

							// Ret Werte setzen
							$ret['matchID'] = $matchID;
							$ret['status'] = true;
						}
						else{
							$ret['status'] = "keine 2 Spieler gefunden!";
						}
						break;
					case "9":
						// Wenn wirklich 2 Spieler gefunden
						if(count($data) == 6){

							// neues Match eintragen mit Matchdetails
							$Match = new Match();
							$matchID = $Match->insertNewFoundMatch2($regionID, $matchTypeID, $matchModeID);
							$matchID = $matchID['matchID'];


							$insertArray = array();

								
							$ret['debug'] .= p("�briges DATA ARRRRAAAAYY",1);
							$ret['debug'] .= p($data,1);
							$ret['debug'] .= p("Bereits gef�lltes Insert ARRAAYYY",1);
							$ret['debug'] .= p($insertArray,1);
							$ret['debug'] .= p("COUNT TEAMS",1);
							$ret['debug'] .= p($countTeam,1);
							foreach($data as $k => $v){
								$tmpArray = array();
								if($i%2==0){

									$teamID = 2;

								}
								else{
									$teamID = 1;
								}

								if($countTeam[$teamID] < 5){
									$tmpArray['MatchID'] = (int) $matchID;
									$tmpArray['SteamID'] = secureNumber($v['SteamID']);
									$tmpArray['TeamID'] = (int) $teamID;
									$tmpArray['Rank'] = (int) $v['Elo'];
									$tmpArray['Ready'] = 0;

									$insertArray[] = $tmpArray;
									$countTeam[$teamID]++;
								}
								else{
									if($teamID == 1){
										$teamID = 2;
									}
									else{
										$teamID = 1;
									}
									$tmpArray['MatchID'] = (int) $matchID;
									$tmpArray['SteamID'] = secureNumber($v['SteamID']);
									$tmpArray['TeamID'] = (int) $teamID;
									$tmpArray['Rank'] = (int) $v['Elo'];
									$tmpArray['Ready'] = 0;

									$insertArray[] = $tmpArray;
									$countTeam[$teamID]++;
								}

								$i++;
							}

							// Eintragen in MatchTeams
							$ret['debug'] .= p($countTeam,1);
							$ret['debug'] .= p($insertArray,1);
							$ret['debug'] .= p($DB->multiInsert("MatchTeams", $insertArray,1),1);
							$DB->multiInsert("MatchTeams", $insertArray);



							// MatchDetails eintreagen
							$retInsMD = $Match->saveMatchDetails2($matchID);

							$ret['debug'] .= p($retInsMD,1);

							// Ret Werte setzen
							$ret['matchID'] = $matchID;
							$ret['status'] = true;
						}
						else{
							$ret['status'] = "keine 6 Spieler gefunden!";
						}
						break;
					default:
						// Wenn wirklich 10 Spieler gefunden
						if(count($data) == 10){

							// neues Match eintragen mit Matchdetails
							$Match = new Match();
							$matchID = $Match->insertNewFoundMatch2($regionID, $matchTypeID, $matchModeID);
							$matchID = $matchID['matchID'];


							$insertArray = array();

							// Gruppen-Typen vorbereiten
							$groupArray = array();
							foreach ($data as $k => $v){
								if($v['GroupID'] > 0){
									$tmpArray = array();
									$tmpArray['MatchID'] = (int) $matchID;
									$tmpArray['SteamID'] = secureNumber($v['SteamID']);
									$tmpArray['TeamID'] = (int) $teamID;
									$tmpArray['Elo'] = (int) $v['Elo'];
									$tmpArray['dataKey'] = (int) $k;
									$groupArray[$v['GroupID']][] = $tmpArray;
								}
							}
							$ret['debug'] .= p("GRROUUP ARRAAAAAAAAAAY",1);
							$ret['debug'] .= p($groupArray,1);

							$i=0;
							if(is_array($groupArray) && count($groupArray) > 0){
								$countTeam = array();
								foreach($groupArray as $k =>$v){
									if($i%2==0){
										$teamID = 2;
									}
									else{
										$teamID = 1;
									}
									$ret['debug'] .= p("ITERATION:".$i." Team:".$teamID,1);
									if(is_array($v) && count($v) > 0){
										foreach($v as $kk =>$vv){
											$tmpArray = array();
											$tmpArray['MatchID'] = (int) $matchID;
											$tmpArray['SteamID'] = secureNumber($vv['SteamID']);
											$tmpArray['TeamID'] = (int) $teamID;
											$tmpArray['Rank'] = (int) $vv['Elo'];
											$tmpArray['Ready'] = 0;
											$tmpArray['GroupID'] = (int)$k;

											$insertArray[] = $tmpArray;

											// aus Data entfernen
											unset($data[$vv['dataKey']]);
											$countTeam[$teamID]++;
										}
									}
									$i++;
								}
								$data = orderArrayBy($data,'Elo',SORT_DESC);
								$ret['debug'] .= p("�briges DATA ARRRRAAAAYY",1);
								$ret['debug'] .= p($data,1);
								$ret['debug'] .= p("Bereits gef�lltes Insert ARRAAYYY",1);
								$ret['debug'] .= p($insertArray,1);
								$ret['debug'] .= p("COUNT TEAMS",1);
								$ret['debug'] .= p($countTeam,1);
								foreach($data as $k => $v){
									$tmpArray = array();
									$retAve = $this->getAvePointsOfTeam($insertArray);
									
									$teamID = $retAve['data'];
// 									if($i%2==0){
											
// 										$teamID = 2;
											
// 									}
// 									else{
// 										$teamID = 1;
// 									}
										
									if($countTeam[$teamID] < 5){
										$tmpArray['MatchID'] = (int) $matchID;
										$tmpArray['SteamID'] = secureNumber($v['SteamID']);
										$tmpArray['TeamID'] = (int) $teamID;
										$tmpArray['Rank'] = (int) $v['Elo'];
										$tmpArray['Ready'] = 0;
										$tmpArray['GroupID'] = 0;
										$insertArray[] = $tmpArray;
										$countTeam[$teamID]++;
									}
									else{
										if($teamID == 1){
											$teamID = 2;
										}
										else{
											$teamID = 1;
										}
										$tmpArray['MatchID'] = (int) $matchID;
										$tmpArray['SteamID'] = secureNumber($v['SteamID']);
										$tmpArray['TeamID'] = (int) $teamID;
										$tmpArray['Rank'] = (int) $v['Elo'];
										$tmpArray['Ready'] = 0;
										$tmpArray['GroupID'] = 0;
										$insertArray[] = $tmpArray;
										$countTeam[$teamID]++;
									}
										
									$i++;
								}
							}
							else{
								$ret['debug'] .= p("NO GROUPS",1);
								//$ret['debug'] .= p($data,1);
								$Matchmaking = new Matchmaking();
								$retSeparetedArray = $Matchmaking->seperate10PlayersInto2BalancedTeams($data);
								//$ret['debug'] .= p($retSeparetedArray,1);
								$separatedArray = $retSeparetedArray['data'];
									
								if(is_array($separatedArray) && count($separatedArray) > 0){
									$insertArray = array();
									for($team = 1; $team <=2; $team++){
										foreach($separatedArray[$team] as $k =>$v){
											$ret['debug'] .= p($v['SteamID'],1);
											$ret['debug'] .= p(gettype($v['SteamID']),1);
											$ret['debug'] .= p(secureNumber($v['SteamID']),1);
											$tmpArray = array();
											$tmpArray['MatchID'] = (int) $matchID;
											$tmpArray['SteamID'] = $v['SteamID'];
											$tmpArray['TeamID'] = (int) $team;
											$tmpArray['Rank'] = (int) $v['Elo'];
											$tmpArray['Ready'] = 0;
											$tmpArray['GroupID'] = 0;

											$insertArray[] = $tmpArray;
										}
									}

								}
							}


							// Eintragen in MatchTeams
							$ret['debug'] .= p($countTeam,1);
							$ret['debug'] .= p($insertArray,1);

							$ret['debug'] .= p($con,1);
							//$ret['debug'] .= p($DB->multiInsert("MatchTeams", $insertArray,1),1);
								
							$retINs = $DB->multiInsert("MatchTeams", $insertArray);
							$ret['debug'] .= p($retINs,1);

							// MatchDetails eintreagen
							$retInsMD = $Match->saveMatchDetails2($matchID);

							$ret['debug'] .= p($retInsMD,1);

							// Ret Werte setzen
							$ret['matchID'] = $matchID;
							$ret['status'] = true;
						}
						else{
							$ret['status'] = "keine 10 Spieler gefunden!";
						}
				}


			}

			return $ret;
		}
		/*
		 * Copyright 2013 Artur Leinweber
		* Date: 2013-01-01
		*/
		function checkIfPlayerAlreadyInMatchTeams($steamID=0){

			$ret = array();

			$DB = new DB();
			$con = $DB->conDB();
			if($steamID == 0){
				$steamID = $_SESSION['user']['steamID'];
			}


			if($steamID > 0){

				$log = new KLogger ( "log.txt" , KLogger::INFO );
				//$log->LogInfo("Check ob nicht schon in MatchTeams eingetragen");	//Prints to the log file


				$sql = " SELECT SteamID
						FROM MatchTeams
						WHERE SteamID = ".secureNumber($steamID)."
								";
				$count = $DB->countRows($sql);
				//$log->LogInfo("Count:".(int)$count);	//Prints to the log file

				if($count > 0){
					$ret['status'] = true;
				}
				else {
					$ret['status'] = false;
				}
			}

			return $ret;

		}
		/*
		 * Copyright 2013 Artur Leinweber
		* Date: 2013-01-01
		*/
		function cleanMatchTeamsOfPlayer($steamID=0){
			if($steamID == 0){
				$steamID = $_SESSION['user']['steamID'];
			}
			$ret = array();
			$DB = new DB();
			$con = $DB->conDB();
			if($steamID > 0){
				$sql = "DELETE FROM `MatchTeams` WHERE SteamID = ".secureNumber($steamID);
				$DB->delete($sql);
				$ret['debug'] = $sql;
				$ret['status'] = true;
			}
			else{
				$ret['status'] = "SteamID 0";
			}

			return $ret;
		}
		/*
		 * Copyright 2013 Artur Leinweber
		* Date: 2013-01-01
		*/
		function deleteMatchTeamsByMatchID($matchID){
			$ret = array();
			$DB = new DB();
			$con = $DB->conDB();

			if($matchID > 0){
				$sql = "DELETE FROM `MatchTeams`
						WHERE MatchID = ".(int)$matchID."
								";
				$data = $DB->delete($sql);
				$ret['debug'] .= p($sql,1);

				$ret['status'] = true;
			}
			else{
				$ret['status'] = "matchID 0";
			}
			return $ret;
		}
		/*
		 * Copyright 2013 Artur Leinweber
		* Date: 2013-09-05
		*/
		function getAvePointsOfTeam($array){
			$ret = array();
			if(is_array($array) && count($array) > 0){
				//$ret['debug'] .= p($array,1);
				$sum1 = 0; $sum2 = 0; $count1 = 0; $count2 = 0;
				foreach ($array as $k => $v) {
					$teamID = $v['TeamID'];
					$points = $v['Rank'];
					if($teamID == 1){
						$sum1 += $points;
						$count1++;
					}
					else{
						$sum2 += $points;
						$count2++;
					}
				}

				$ave1 = ($count1 > 0 ? $sum1/$count1 : 0);
				$ave2 = ($count2 > 0 ? $sum2/$count2 : 0);
				$ret['debug'] .= p("S:".$sum1." C:".$count1." S2:".$sum2." C2:".$count2." A:".$ave1." A2:".$ave2,1);
				$ret['ave1'] = $ave1;
				$ret['ave2'] = $ave2;
				if($ave1 <= $ave2){
					$ret['data'] = 1;
				}
				else{
					$ret['data'] = 2;
				}
				$ret['status'] = true;
			}
			else {
				$ret['status'] = false;
			}
			return $ret;
		}
	}

	?>