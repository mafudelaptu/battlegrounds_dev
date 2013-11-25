<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class TestLogin{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function handleFakeLogin($steamID=""){
		$DB = new DB();
		$User = new User();
		$Login = new Login();
		$con = $DB->conDB();

		// identity is something like: http://steamcommunity.com/openid/id/76561197994761333
		// we only care about the unique account ID at the end of the URL.
		$TestUser = new TestUser();
		if($steamID == ""){
			$retRand = $TestUser->getRandomSteamID();
			$steamID = $retRand['SteamID'];
			$hash = $retRand['Hash'];
		}
		else{
			$retU = $User->getUserData($steamID);
			$hash = $retU['Hash'];
		}
		
		//echo $steamID;
		//setcookie("login[steamID]", $steamID, time()+36000);

		// Check User already in DB

		// if first login generate Stats
		$firstLoginModal = "";
// 		if(!$User->userAlreadyInDB($steamID)){
// 			// neuen User eintragen und hash cookie setzen
// 			$User->insertNewUser($steamID);
			
// 			$Login->insertFirstStatsOfUser($steamID);
// 			$_SESSION['user']['steamID'] = $steamID;
// 			$firstLoginModal = "?checkLogin=1";
// 			//header('Location: ' . HOME_PATH.$firstLoginModal);
// 			$url = HOME_PATH.$firstLoginModal;
// 		}
// 		else{
// 			$User->updateUser($steamID);
// 			$_SESSION['user']['steamID'] = $steamID;
// 			//header('Location: ' . HOME_PATH);
// 			$url = HOME_PATH."?checkLogin=1";
// 		}

		$_SESSION['user']['steamID'] = $steamID;
		setcookie("loginHash2", $hash, time()+604800);
		$url = HOME_PATH."?firstLogin=1";
		
		$ret['url'] = $url;
		$ret['status'] = true;
		$ret['steamID'] = $steamID;
		return $ret;
	}
}

?>