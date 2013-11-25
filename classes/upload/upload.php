<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Uploads{
	const sourceDirectory = "inc/jquery_fileUpload/server/php/files/";

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function moveScreenshotFile($matchID, $steamID, $fileName){
		$ret = array();

		$sourceFile = Uploads::sourceDirectory.$fileName;
		$destDirectory = "uploads/match/screenshots/".$matchID."/";
		$destFileName = $steamID."_".$fileName;
		$destFile = $destDirectory.$destFileName;
		// Ordner anlegen
		if ( ! is_dir($destDirectory)) {
			mkdir($destDirectory);
		}
		else{
			// alles löschen was user vorher schon upgeloaded hat
			$files = glob($destDirectory."*"); // finds all txt files in the directory
			foreach($files as $file){
				if(strpos($file, $steamID)){
					unlink($file);
				}
			}
		}

		// verschieben (von source wird gelöscht)
		if (!rename($sourceFile, $destFile)) {
			$ret['status'] = "rename schlug fehl...\n";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function moveReplayFile($matchID, $steamID, $fileName){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		
		$sourceFile = Uploads::sourceDirectory.$fileName;
		$destDirectory = Uploads::sourceDirectory.$matchID."/";
		$ReplayParser = new ReplayParser();
		
		$replayInDB = $ReplayParser->replayInDB($matchID, 0);
		$ret['debug'] .= p($replayInDB,1);
		
		if($replayInDB['status']){
			$ret['status'] = "replayInDB";
			unlink($sourceFile);
		}
		else{
			$retCheck = $ReplayParser->checkForContentOfZip($sourceFile);
			
			$ret['debug'] .= p($retCheck,1);
			if($retCheck['status'] === true){
				$zip = new ZipArchive;
				if ($zip->open($sourceFile) === TRUE) {
					
					if ( ! is_dir($destDirectory)) {
						mkdir($destDirectory);
					}
					// alles löschen was user vorher schon upgeloaded hat
					else{
						$this->deleteDir($destDirectory);
					}

					$zip->extractTo($destDirectory);
					$zip->close();
					
					$ret['debug'] .= p("extracted",1);

					$retRP = $ReplayParser->getAllStats($destDirectory);
					$ret['debug'] .= p($retRP,1);
					$replayID = $retRP['replayID'];
					$ret['debug'] .= p("------------------------------------------------",1);
					$replayInDB = $ReplayParser->replayInDB($matchID, $replayID);
					if($replayInDB['status']){
						$ret['status'] = "replayInDB";
						unlink($sourceFile);
						$this->deleteDir($destDirectory);
					}
					else{
						// Replay mit usern in DB hauen
						if(is_array($retRP['generalData']) && count($retRP['generalData']) > 0){
							$insertArray = array();
							$retCheckIn = $ReplayParser->checkAllPlayersIn($matchID, $retRP['generalData']);
							$checkIn = $retCheckIn['status'];
							//p($retCheckIn);
							//p($retRP['debug2']);
							//$checkIn = true;
							$ret['debug'] .= p($retCheckIn,1);
							if($checkIn){
								foreach ($retRP['generalData']as $k => $v) {
									$steamID = $v['steamid'];
									$hero = $v['hero'];
									$tmp['MatchDetailsReplayID'] = secureNumber($replayID);
									$tmp['MatchID'] = (int) $matchID;
									$tmp['SteamID'] = secureNumber($steamID);
									$tmp['Hero'] = $hero;
									$tmp['GameLength'] = (int) $retRP['gameLength'];
									
									// Stats
									$stats = $retRP['data'][$hero];
									$tmp['Kills'] = (int) $stats['kills'];
									$tmp['Deaths'] = (int) $stats['deaths'];
									$tmp['Assists'] = (int) $stats['assists'];
									$tmp['Lvl'] = (int) $stats['lvl'];
									$tmp['TotalGold'] = (int) $stats['totalGold'];
									$tmp['CS'] = (int) $stats['cs'];
									$tmp['Denies'] = (int) $stats['denies'];
									
									// Killstreaks TODO
								
									// FirstBlood
									if($retRP['firstBlood']['killer'] == $hero){
										$tmp['FirstBloodTime'] = (int) $retRP['firstBlood']['time'];
									}
									else{
										$tmp['FirstBloodTime'] = (int) 0; // muss sein wegen row count in insert-statement
									}
								
									$insertArray[] = $tmp;
								}
								$retInsGeneralDebug = $DB->multiInsert("MatchDetailsReplay", $insertArray, 1);
									
								if($insertArray[0]['MatchDetailsReplayID'] > 0){
									$retInsGeneral = $DB->multiInsert("MatchDetailsReplay", $insertArray);
								}
									
								$ret['debug'] .= p($retInsGeneralDebug,1);
								// Best Stats
								if(is_array($retRP['bestStats']) && count($retRP['bestStats']) > 0){
									foreach ($retRP['bestStats'] as $k => $v) {
										switch($k){
											case "mostKills":
												$bestStatsTypeID = 1;
												break;
											case "bestTeamplayer":
												$bestStatsTypeID = 2;
												break;
											case "mostLastHits":
												$bestStatsTypeID = 3;
												break;
											case "mostDenies":
												$bestStatsTypeID = 4;
												break;
											case "mostTotalGold":
												$bestStatsTypeID = 5;
												break;
												// Killstreaks  TODO
										}
								
										$insertArray = array();
										if(is_array($v) && count($v) > 0){
											foreach ($v as $k => $ar) {
												$steamID = $ar['steamid'];
												$value = $ar['value'];
								
												$tmp = array();
												$tmp['MatchDetailsReplayID'] = secureNumber($replayID);
												$tmp['MatchID'] = (int) $matchID;
												$tmp['ReplayBestStatsTypeID'] = (int) $bestStatsTypeID;
												$tmp['SteamID'] = secureNumber($steamID);
												$tmp['StatsValue'] = (int) $value;
												$insertArray[] = $tmp;
											}
											$retInsBestDebug = $DB->multiInsert("MatchDetailsReplayBestStats", $insertArray, 1);
											$retInsBest = $DB->multiInsert("MatchDetailsReplayBestStats", $insertArray);
								
								
										}
									}
									// Chat eintragen
									if(is_array($retRP['chat']) && count($retRP['chat']) > 0){
										unset($insertArray);
										$insertArray = array();
										foreach ($retRP['chat'] as $k => $v) {
											$tmp = array();
											$tmp['MatchDetailsReplayID'] = secureNumber($replayID);
											$tmp['MatchID'] = (int) $matchID;
											$tmp['Player'] = mysql_real_escape_string($v['player']);
											$tmp['Time'] =  $v['time'];
											$tmp['Msg'] = mysql_real_escape_string($v['msg']);
											$insertArray[] = $tmp;
										}
										$retInsChatDebug = $DB->multiInsert("MatchDetailsReplayChat", $insertArray, 1);
										$retInsChat = $DB->multiInsert("MatchDetailsReplayChat", $insertArray);
									}
									$ret['debug'] .= p($retInsGeneral."  - ".$retInsGeneralDebug."\r\n\r\n",1);
									$ret['debug'] .= p($retInsBest." - ".$retInsBestDebug."\r\n\r\n",1);
									$ret['debug'] .= p($retInsChat." - ".$retInsChatDebug."\r\n\r\n",1);
									//if($retInsGeneral && $retInsBest && $retInsChat){
									$this->deleteDir($destDirectory);
									// alles löschen was user vorher schon upgeloaded hat
									unlink($sourceFile);
								
									// Bonus-Pkt geben
									// 									$MatchDetails = new MatchDetails();
									// 									$retPlayer = $MatchDetails->getAllPlayersInMatch($matchID);
									// 									$data = $retPlayer['data'];
									$UserCredits = new UserCredits();
									$retUC = $UserCredits->insertReplayUploadBonus($matchID);
									$ret['status'] = true;
									// 									if(is_array($data) && count($data) > 0){
									// 										$UserPoints = new UserPoints();
									// 										foreach ($data as $k => $v) {
									// 											// 										$UserPoints = new UserPoints();
									// // 										foreach ($data as $k => $v) {
									// // 											$steamID = $v['SteamID'];
									// // 											$retUP = $UserPoints->insertReplayUploadBonus($matchID, $steamID);
									// // 										}
								
									// 										}
									// 										$ret['status'] = true;
									// 									}
									// 									else{
									// 										$ret['status'] = "Player konnten nicht ausgelesen werden";
									// 									}
									// 								}
									// 								else{
									// 									$ret['status'] = "Beim inserts was schiefgelaufen";
									// 								}
								}
							}
							else{
								$ret['status'] = "wrongReplay";
							}
						}
					}

				}
				else {
					
					$ret['status'] = "error beim zip datei oeffnen";
				}
			}
			else{
				$ret['status'] = "fuckYou";
			}
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	public static function deleteDir($dirPath) {
		if (! is_dir($dirPath)) {
			return false;
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}

}

?>