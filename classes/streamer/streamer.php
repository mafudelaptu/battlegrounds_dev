<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Streamer{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getStreamer($limit=5){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getStreamer <br>\n";

		$sql = "SELECT *
				FROM `Streamer` s LEFT JOIN User u ON u.SteamID = s.LinkedToPlayer
				
				";
		$ret['debug'] .= p($sql,1);
		$data = $DB->multiSelect($sql);
		if(is_array($data) && count($data) > 0){
			$streamChannel = array();
			foreach ($data as $k => $v) {
				$steamID = $v['SteamID'];
				if($steamID > 0){
					$Match = new Match();
					$inMatch = $Match->isPlayerInMatch(0, $steamID);
					if($inMatch){
						$streamChannel[] = $v['Channelname'];
					}
					else{
						unset($data[$k]);
					}
				}
				else{
					$streamChannel[] = $v['Channelname'];
				}
				
			}
		}
		$ret['debug'] .= p($data,1);
		$Twitch = new Twitch();
		$retT = $Twitch->getStreamsData($streamChannel);
		$ret['debug'] .= p($retT,1);

		if(is_array($data) && count($data) > 0){
			$data2 = array();
			foreach ($data as $k => $v) {
				$twitchData = $retT['data'][$v['Channelname']];
				
				if(is_array($twitchData) && count($twitchData) > 0){
						if($v['LinkedToPlayer'] > 0){
							$data2['player'][] = array_merge_recursive($data[$k], $twitchData);
						}
						else{
							$data2['featured'][] = array_merge_recursive($data[$k], $twitchData);
						}
				}
				
			}
		}
		
		// sortieren nach viewers
		if(is_array($data2['featured']) && count($data2['featured']) > 0){
			$orderedStreamsFeatured = orderArrayBy($data2['featured'],'viewers',SORT_DESC);
		}
		if(is_array($data2['player']) && count($data2['player']) > 0){
			$orderedStreamsPlayer = orderArrayBy($data2['player'],'viewers',SORT_DESC);
		}
	
// 		if(is_array($data2['normal']) && count($data2['normal']) > 0){
// 			$orderedStreamsNormal = orderArrayBy($data2['normal'],'viewers',SORT_DESC);
// 		}
		

		$retData = array();
// 		$retData['normal'] = array();
		for($i=0; $i<$limit; $i++){
			if($orderedStreamsFeatured[$i]){
				
				$retData['featured'][] = $orderedStreamsFeatured[$i];
			}
			if($orderedStreamsPlayer[$i]){
				
				$retData['player'][] = $orderedStreamsPlayer[$i];
			}
// 			if($orderedStreamsNormal[$i]){
// 				$retData['normal'][] = $orderedStreamsNormal[$i];
// 			}
		}
		$ret['data'] = $retData;
		$ret['status'] = true;

		$ret['debug'] .= "End getStreamer <br>\n";
		return $ret;
	}

}

?>