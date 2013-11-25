<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Twitch{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getStreamsData($channelNamesArray){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getStreamsData <br>\n";


		if(is_array($channelNamesArray) && count($channelNamesArray) > 0){
			$comma_separated = implode(",", $channelNamesArray);
				
			$url = "https://api.twitch.tv/kraken/streams?channel=".$comma_separated;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$json = curl_exec($ch);
			curl_close($ch);
			$dataStream = json_decode($json,true);
			$ret['debug'] .= p($dataStream,1);
			
			$streams = $dataStream['streams'];
			$orderedStreams = array();
			if(is_array($streams) && count($streams) > 0){
				$data = array();
			   foreach ($streams as $k => $v) {
			   		$nameTmp = explode("http://www.twitch.tv/", $v['channel']['url']);
			   		$name = $nameTmp[1];
					$tmp = array();
			   		$tmp['viewers'] = $v['viewers'];
			   		$tmp['preview'] = $v['preview'];
			   		$tmp['game'] = $v['channel']['game'];
			   		$tmp['name'] = $v['channel']['name'];
			   		$tmp['url'] = $v['channel']['url'];
			   		$tmp['delay'] = $v['channel']['delay'];
			   		$tmp['status'] = $v['channel']['status'];
			   		$tmp['online'] = true;
			   		
			   		$data[$name] = $tmp;
			  }
			  
			}
			
			
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "array == 0";
		}
		$ret['debug'] .= "End getStreamsData <br>\n";
		return $ret;

	}


}


?>