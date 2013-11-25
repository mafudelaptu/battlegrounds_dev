<?php 

class ReferAFriend{

	const salt = "3AqKVb6bBt9mRwCL6FrTNnkS";
	const refererCountBorder = 1;
	const refererCoinBonus = 50;

	function generateLink($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start generateLink <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){

			$val = $this->encryptData(secureNumber($steamID));

			$link = "http://".$_SERVER['SERVER_NAME'].HOME_PATH_REL."/index.php?rid=".$val;

			$ret['data'] = $link;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End generateLink <br>\n";
		return $ret;
	}


	function getReferedCount($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getReferedCount <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
				
			$sql = "SELECT raf.*, Count(md.SteamID) as Count
					FROM `ReferAFriend` raf
					LEFT JOIN `MatchDetails` md ON md.SteamID = raf.ReferedSteamID
					LEFT JOIN `Match` m ON m.MatchID = md.MatchID AND m.MatchID != -1 AND m.Canceled = 0 AND m.ManuallyCheck = 0
					WHERE raf.SteamID = ".secureNumber($steamID)."
							GROUP BY md.SteamID
							";
			$data = $DB->multiSelect($sql);
			$ret['debug'] .= p($sql,1);
				
			if(is_array($data) && count($data) > 0){
				$tmpCount = 0;
				$inviteCount = 0;
				foreach ($data as $k => $v) {
					$count = $v['Count'];
					$inviteCount++;
					if($count >= ReferAFriend::refererCountBorder){
						$tmpCount++;
					}
				}
				$ret['data'] = $tmpCount;
				$ret['inviteCount'] = $inviteCount;
				$ret['status'] = true;
			}
			else{
				$ret['data'] = 0;
				$ret['status'] = true;
			}
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getReferedCount <br>\n";
		return $ret;
	}

	function encryptData($value){
		$key = ReferAFriend::salt;
		$text = $value;
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
		return base64_encode($crypttext);
	}

	function decryptData($value){
		$key = ReferAFriend::salt;
		$crypttext = base64_decode($value);
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
		return trim($decrypttext);
	}

	function insertReferedByAFriend($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertReferedByAFriend <br>\n";

		$referedID = $_COOKIE['referedID'];

		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0 && $referedID != ""){
				
			$friendSteamID = $this->decryptData($referedID);
			if($friendSteamID != $steamID){
				$data = array();
				$data['SteamID'] = secureNumber($friendSteamID);
				$data['ReferedSteamID'] = secureNumber($steamID);
				$data['Timestamp'] = time();
				$ret['debug'] .= p($data,1);
				$retINs = $DB->insert("ReferAFriend", $data);
				$ret['status'] = $retINs;

				setcookie("referedID", "", time()-100);
				unset($_COOKIE['referedID']);
			}
			else{
				$ret['status'] = "abuse";
			}
		}
		else{
			$ret['status'] = "steamID == 0 || referedID == ''";
		}
		$ret['debug'] .= "End insertReferedByAFriend <br>\n";
		return $ret;
	}

}


?>