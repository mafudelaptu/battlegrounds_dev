<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Hash{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function createHash($steamID, $name){
		$ret = array();

		if($steamID > 0){
				
			$md5SteamID = md5($steamID);
			$md5Name = md5($name);
			$tmp = time();
			$tmp = str_split($tmp, round(strlen($tmp)/2,0));
				
			//p($tmp);
				
			$saltPre = $tmp[1];
			$saltEnd = $tmp[0];
				
			$hash = sha1($saltPre.$md5Name.$md5SteamID.$saltEnd);
				
			//p($hash);
				
			$ret = $hash;
		}
		else{
			return false;
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkHash($hash){
		$ret = array();

		if($hash != ""){
			$DB = new DB();
			$con = $DB->conDB();
				
			$sql = "SELECT SteamID
					FROM User
					WHERE Hash = '".secureStringsNumbers($hash)."'";
			$data = $DB->select($sql);
			
			if($data['SteamID'] > 0){
				return $data['SteamID'];
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function updateHash($steamID, $name){
		$hash = $this->createHash($steamID, $name);
		
		if($hash){
			$DB = new DB();
			$con = $DB->conDB();
			
			$sql = "UPDATE User
					SET Hash = '".secureStringsNumbers($hash)."'
					WHERE SteamID = ".secureNumber($steamID)." 
					";
			$DB->update($sql);
			
			setcookie("loginHash2", $hash, time()+604800);
			
		}
		else{
			return false;
		}
	}
}

?>