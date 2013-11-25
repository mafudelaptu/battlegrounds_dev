<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class DotaBuff{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getStats($steamID){
			$dotabuffsite = $this->getDotaBuffSiteOfUser($steamID);
			
			preg_match('#<div id="content-header-secondary">.*</div>#',$dotabuffsite,$treffer);
			$tref = preg_split('#<div id="content-interactive"></div>#', $treffer[0]);
			$rightFrame = $tref[0];
			$tref = preg_split('#<time .*</time></div></dd></dl>#', $rightFrame);
			//p($rightFrame);
			preg_match_all('#[0-9]+#', $tref[1],$res);
			$statsArray = $res[0];
			//p($statsArray);
			$ret['lvl'] = $statsArray[0];
			$wins = (int)$statsArray[1];
			$loses = (int)$statsArray[2];
			$ret["wins"] = $wins;
			$ret['loses'] = $loses;
			if($loses > 0){
				$ret['ratio'] = round($wins/($wins+$loses)*100,2);
			}
			else{
				$ret['ratio'] = 0;
			}
			
			 return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function CurlPost($sURL){
    $ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 '));
    curl_setopt($ch, CURLOPT_URL, $sURL);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $sResult = curl_exec($ch);
    if (curl_errno($ch)){
        // Fehlerausgabe
        print curl_error($ch);
    } else 
    {
        // Kein Fehler, Ergebnis zurückliefern:
        curl_close($ch);
        return $sResult;
    }    
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getDotaBuffSiteOfUser($steamID){
			$URL = "https://dotabuff.com/players/".$steamID;
			//p($URL);
			$redirect =	$this->CurlPost($URL);

			$num = preg_split('/<a href="https:\/\/dotabuff.com\/players\//',$redirect);
			$dotabuffID = (int) $num[1];
			$source =	$this->CurlPost("https://dotabuff.com/players/".$dotabuffID);
			return $source;
	}
}

?>