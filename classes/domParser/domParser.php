<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class DomParser{
	//const includeSimpleHtmlDom = "";
	/*
	* Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function parseDotaBuffStats($steamID = 0){



		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start parseDotaBuffStats <br>\n";


		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$wins = 0;
			$loses = 0;
			$totalGames = 0;
			$winrate = 0;
			$url = "http://dotabuff.com/players/".$steamID;
			$ret['debug'] .= p($url,1);
			$url_exists = $this->url_exists($url);
			//$url = "http://dotabuff.com/players/86746327";
			if($url_exists){
				$html = file_get_html($url);
				//$ret['debug'] .= p($html,1);
				if (method_exists($html,"find")) {
					$ret['debug'] .= p("exist find",1);
					// then check if the html element exists to avoid trying to parse non-html
					if ($html->find('html')) {
						$ret['debug'] .= p("find html",1);
						$wins = $html->find('#content-header-secondary',0)->children(1)->find(".won",0)->plaintext;
						$wins = str_replace(",", "", $wins);
						$wins = (int) $wins;

						$ret['debug'] .= p($wins,1);
						$loses = $html->find('#content-header-secondary',0)->children(1)->find(".lost",0)->plaintext;
						$ret['debug'] .= p($loses,1);
						$loses = str_replace(",", "", $loses);
						$loses = (int) $loses;

						$totalGames = $wins+$loses;
						$ret['debug'] .= p($totalGames,1);

						if($totalGames>0){
							$winrate = round($wins/$totalGames*100, 2);
						}

						$ret['debug'] .= p($winrate,1);

						$data['Wins'] = (int) $wins;
						$data['Loses'] = (int) $loses;
						$data['TotalGames'] = (int) $totalGames;
						$data['WinRate'] = $winrate;

						$ret['data']  = $data;
						$ret['status'] = true;
					}
				}
				else{
					$ret['debug'] .= p("no find exists",1);

				}
			}
			else{
				$ret['status'] = "dotabuff.com-account missing";
			}
		}
		else{
			$ret['status'] = "steamID = 0";
		}


		$ret['debug'] .= "End parseDotaBuffStats <br>\n";

		return $ret;

	}

	function parseDotaBuffStats2($steamID = 0){

		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start parseDotaBuffStats <br>\n";


		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		if($steamID > 0){
			$wins = 0;
			$loses = 0;
			$totalGames = 0;
			$winrate = 0;
			$url = "http://dotabuff.com/players/".$steamID;
			$ret['debug'] .= p($url,1);
			$ret['debug'] .= p("test",1);

			$html = $this->get_fcontent($url);
			//$ret['debug'] .= p($html[0],1);
			$doc = phpQuery::newDocument($html[0]);
			$statsContainer = pq("#content-header-secondary");
				
			$ret['debug'] .= p($statsContainer->html(),1);	

			if ($statsContainer->html() != "") {
				$ret['debug'] .= p("exist find",1);
				// then check if the html element exists to avoid trying to parse non-html
				$winsHTML = pq("#content-header-secondary span.won")->html();
				$winsHTML = preg_replace('![^0-9]!', '', $winsHTML); 
				$wins = (int) $winsHTML;
					
				$lostHTML = pq("#content-header-secondary span.lost")->html();
				$lostHTML = preg_replace('![^0-9]!', '', $lostHTML); 
				$lost = (int) $lostHTML;
					
				$totalGames = $wins+$lost;
					
				if($totalGames>0){
					$winrate = round($wins/$totalGames*100, 2);
				}
					
				$data['Wins'] = (int) $wins;
				$data['Loses'] = (int) $lost;
				$data['TotalGames'] = (int) $totalGames;
				$data['WinRate'] = $winrate;

				$ret['data']  = $data;
				$ret['status'] = true;
					
			}
			else{
				$ret['debug'] .= p("dotabuff.com-account missing",1);

			}
		}
		else{
			$ret['status'] = "steamID = 0";
		}


		$ret['debug'] .= "End parseDotaBuffStats <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function parsePrizesFromInventory($raceTypeID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start parsePrizesFromInventory <br>\n";


		$wins = 0;
		$loses = 0;
		$totalGames = 0;
		$winrate = 0;
		$url = "http://dota2.backpack.me/id/76561198092757045";

		$html = file_get_html($url);
		//$ret['debug'] .= p($html,1);
		if (method_exists($html,"find")) {
			$ret['debug'] .= p("find exists",1);
			// then check if the html element exists to avoid trying to parse non-html
			if ($html->find('html')) {
				// zuerst DB leeren
				$sql = "DELETE FROM `RacePrizes`
						WHERE RaceTypeID = ".(int) $raceTypeID."
								";
				//$ret['debug'] .= p($sql,1);
				$retDel = $DB->delete($sql);
				//$ret['debug'] .= p($retDel,1);

				for($z=1; $z<=5; $z++){
					$data = array();
					$rarityType = $z;
					foreach($html->find('li[data-rarity='.$rarityType.']') as $element){
							
						//$itemHtml = $element->innertext;
						//echo $itemHtml . '<br>';
						$i = 1;
						foreach($element->find("td") as $td){
							// bild
							if($i == 1){
								$img = $td->children(0)->src;
							}
							else{
								$name = $td->children(0)->children(0)->plaintext;
								$itemType = $td->children(1)->plaintext;
							}
							$i++;
						}

						$insertArray = array();
						$insertArray['RaceTypeID'] = (int) $raceTypeID;
						$insertArray['Img'] = $img;
						$insertArray['Name'] = secureStrings($name);
						$insertArray['ItemType'] = $itemType;
						$insertArray['RarityType'] = (int) $rarityType;
							
						$data[] = $insertArray;
					}
					//$ret['debug'] .= p($DB->multiInsert("RacePrizes", $data,1),1);
					$retIns = $DB->multiInsert("RacePrizes", $data);
					unset($data);
					clearstatcache();
					$ret['debug'] .= "rarityType ".$rarityType." eingetragen: ".$retIns;
				}
				unset($html);
			}
		}


		$ret['status'] = $retIns;

		$ret['debug'] .= "End parsePrizesFromInventory <br>\n";

		return $ret;

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function url_exists($url){

		$url = "dotabuff.com";
		p($url);
		if ((strpos($url, "http")) === false) $url = "http://" . $url;
		$headers = @get_headers($url);
		p("test");
		p($headers);
		if (is_array($headers)){
			//Check for http error here....should add checks for other errors too...
			if(strpos($headers[0], '404 Not Found'))
				return false;
			else
				return true;
		}
		else
			return false;
	}

	function curl_get_file_contents($URL)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $URL);
		$contents = curl_exec($c);
		curl_close($c);

		if ($contents) return $contents;
		else return FALSE;
	}

	function get_data($url) {
		$ch = curl_init();
		$timeout = 50;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	function get_fcontent( $url,  $javascript_loop = 0, $timeout = 5 ) {
		$url = str_replace( "&amp;", "&", urldecode(trim($url)) );

		$cookie = tempnam ("/tmp", "CURLCOOKIE");
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		$content = curl_exec( $ch );
		$response = curl_getinfo( $ch );
		curl_close ( $ch );

		if ($response['http_code'] == 301 || $response['http_code'] == 302) {
			ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

			if ( $headers = get_headers($response['url']) ) {
				foreach( $headers as $value ) {
					if ( substr( strtolower($value), 0, 9 ) == "location:" )
						return get_url( trim( substr( $value, 9, strlen($value) ) ) );
				}
			}
		}

		if (    ( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) && $javascript_loop < 5) {
			return get_url( $value[1], $javascript_loop+1 );
		} else {
			return array( $content, $response );
		}
	}
}

?>