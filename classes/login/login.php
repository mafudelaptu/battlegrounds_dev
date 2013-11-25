<?php
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Login{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkLogin(){
		if($_SESSION['user']['steamID']){
			return true;
		}
		else{
			false;
		}
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkLogin2(){
		$Hash = new Hash();
		// wenn Session gesetzt dann bereits eingeloggt
		if(isset($_SESSION['user']) && $_SESSION['user']['steamID'] > 0){

			if($_COOKIE['loginHash2'] != ""){
				$steamID = $Hash->checkHash($_COOKIE['loginHash2']);
				if($steamID){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		// sonst Browser geschlossen ect -> Cookie kontrollieren
		else{
			if(isset($_COOKIE['loginHash2'])){
				$steamID = $Hash->checkHash($_COOKIE['loginHash2']);
					
				// wenn Hash in Datenbank -> dann einloggen
				if($steamID){
					$_SESSION['user']['steamID'] = $steamID;
					$_SESSION['debug'] = p($_SESSION['user'],1);
					return true;
				}
			}
			// Hash Manipuliert -> neu setzen
			else{
				return false;
			}

		}
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkLogin3(){
		$LoginForum = new LoginForum();
		$User = new User();

		
		$retLF = $LoginForum->checkLogin($_COOKIE['member_id'], $_COOKIE['pass_hash']);
			

		if(isset($_COOKIE['member_id']) && isset($_COOKIE['pass_hash'])){
			if($_COOKIE['member_id'] > 0 && $_COOKIE['pass_hash'] !== 0){
				if(isset($_SESSION['user']) && $_SESSION['user']['steamID'] > 0){
					return true;
				}
				else{
					$retLF = $LoginForum->checkLogin($_COOKIE['member_id'], $_COOKIE['pass_hash']);
					//p($retLF);
					if($retLF['status'] == true){
						// checken ob schon in db drin
						$inDB = $User->userAlreadyInDB($_COOKIE['member_id']);
						if(!$inDB){
							$steamID = $_COOKIE['member_id'];
							
							$tmpData = $retLF['data'];
							$userData = array();
							$userData['SteamID'] = $steamID;
							$userData['Name'] = $tmpData['name'];
							$userData['Avatar'] = $tmpData['pp_main_photo'];
							$userData['Hash'] = "";
				
							// refered by a Friend
							$ReferAFriend = new ReferAFriend();
							$retRAF = $ReferAFriend->insertReferedByAFriend($steamID);
				
							$retIns = $User->insertNewUser2($userData);
							$Login = new Login();
							$Login->insertFirstStatsOfUser($steamID);
				
							$retUE = $User->setBasePointsForPlayer($steamID);
				
							$UserSkillBracket = new UserSkillBracket();
							$retUL = $UserSkillBracket->insertFirstSkillBracketForUser($steamID);
				
							$_SESSION['user']['steamID'] = $_COOKIE['member_id'];
							return $retIns;
						}
						else{
							
							$_SESSION['user']['steamID'] = $_COOKIE['member_id'];
							return true;
						}
							
					}
					else{
						return false;
					}
				}
			}
			else{
				return false;
			}
			
			
		}
		else{
			return false;
		}


	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function handleLogin(){
		require DR.HOME_PATH_REL.'/inc/openid.php';
		//echo "<pre>".print_r($_POST,1)."</pre>";
		try {
			# Change 'localhost' to your domain name.
			$openid = new LightOpenID(HOME_PATH);
			if(!$openid->mode) {
				if(isset($_GET['login'])) {
					$openid->identity = 'http://steamcommunity.com/openid';
					header('Location: ' . $openid->authUrl());
				}
			} elseif($openid->mode == 'cancel') {
				echo 'User has canceled authentication!';
			} else {
				if(!$this->checkLogin2()){
					if($openid->validate()) {
						$DB = new DB();
						$User = new User();
						$con = $DB->conDB();
						$id = $openid->identity;
						// identity is something like: http://steamcommunity.com/openid/id/76561197994761333
						// we only care about the unique account ID at the end of the URL.
						$ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
						preg_match($ptn, $id, $matches);
						$steamID = $matches[1];
						//echo $steamID;
							
						//setcookie("login[steamID]", $steamID, time()+36000);
							
						// Check User already in DB
							
						// if first login generate Stats
						$firstLoginModal = "";
						if(!$User->userAlreadyInDB($steamID)){
								
								
								
							// Checken ob er die zulassungsrichtlinien erf�llt
							$check = $this->checkUserFulfillRestrictions($steamID);
							$_SESSION['debug'] = p($check,1);
							if($check['status']){

								// refered by a Friend
								$ReferAFriend = new ReferAFriend();
								$retRAF = $ReferAFriend->insertReferedByAFriend($steamID);
								//p($retRAF);
								// neuen User eintragen und hash cookie setzen
								// 								$User->insertNewUser($steamID);
									
								// 								$this->insertFirstStatsOfUser($steamID);
								$_SESSION['user']['steamID'] = $steamID;
									
								$firstLoginModal = "?checkLogin=1";
								header('Location: ' . HOME_PATH.$firstLoginModal);
							}
							else{

								// User darf dota2-league nicht betreten
								$notAllowedModal = "?notAllowed=1";
								header('Location: ' . HOME_PATH.$notAllowedModal);
							}
						}
						else{
							$ret = $User->updateUserData($steamID);
							$ret = $User->updateUser($steamID);
								
							$_SESSION['debug'] .= $ret['debug'];
							header('Location: ' . HOME_PATH);
								
						}
							
							
					} else {
						echo "User is not logged in.\n";
					}
				}
			}
		} catch(ErrorException $e) {
			echo $e->getMessage();
		}

	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function handleLogout(){
		$ret = array();
		//if($_POST["logout"]){
		//setcookie("login[steamID]", "", time()-3600);
		//unset($_COOKIE["login"]['steamID']);
			
		setcookie("loginHash2", "", time()-3600);
		unset($_COOKIE['loginHash2']);
			
		unset($_SESSION['debug']);
		unset($_SESSION['user']);
			
		$url = "http://login.n-gage.tv";
			
		$ret['status'] = true;
		$ret['url'] = $url;
		//}
		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function handleLogout2(){
		$ret = array();
		//if($_POST["logout"]){
		//setcookie("login[steamID]", "", time()-3600);
		//unset($_COOKIE["login"]['steamID']);
			
		setcookie("loginHash2", "", time()-3600);
		setcookie("member_id", "", time()-3600);
		setcookie("pass_hash", "", time()-3600);
		
		unset($_SESSION['debug']);
		unset($_SESSION['user']);

		$url = HOME_PATH;
			
		$ret['status'] = true;
		$ret['url'] = $url;
		//}
		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertFirstStatsOfUser($steamID){
		$DB = new DB();
		$con = $DB->conDB();
		$DotaBuff = new DotaBuff();
		$UserElo = new UserElo();

		// 		$stats = $DotaBuff->getStats($steamID);
		// 		//p($stats);

		// BaseElo setzen/updaten
		$User = new User();
		$retSetBasePoints = $User->setBasePointsForPlayer($steamID);

		// 		$UserLeague = new UserLeague();
		// 		$retUL = $UserLeague->insertFirstLeagueForUser($steamID);
		$UserSkillBracket = new UserSkillBracket();
		$retUL = $UserSkillBracket->insertFirstSkillBracketForUser($steamID);

		// 		$insertArray["SteamID"] = secureNumber($steamID);
		// 		$insertArray["WinLoseRatio"] = 0;
		// 		$insertArray["Rank"] = START_ELO;
		// 		$insertArray["Lvl"] = $stats['lvl'];
		// 		$insertArray['Wins'] =0;
		// 		$insertArray['Loses'] = 0;
		// 		$DB->insert("UserStats", $insertArray);

		//  alle Elo's für alle MatchModes/MatchTypes eingetragen
		//$status = $UserElo->insertFirstElo($steamID, false);
		//p($status);

		// Umleitung zur tollen Begrüßungspage

	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkUserFulfillRestrictions($steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start checkUserFulfillRestrictions <br>\n";
		if($steamID > 0){
			$ret['debug'] .= p($steamID,1);
			$DomParser = new DomParser();
			$userStatsRet = $DomParser->parseDotaBuffStats2($steamID);
			$userStatsData = $userStatsRet['data'];
			$ret['debug'] .= p("hier",1);
			$ret['debug'] .= p($userStatsRet,1);
			$ret['debug'] .= p("status:".$userStatsRet['status'],1);
			if($userStatsRet['status'] === "dotabuff.com-account missing"){
				$ret['status'] = false;
			}
			else{
				// Check Wins
				if($userStatsData['Wins'] >= WINSBORDER){
					$ret['status'] = true;
				}
				else{
					$ret['status'] = false;
				}
			}
				
				
		}
		else{
			$ret['status'] = "steamID = 0";
		}

		$ret['debug'] .= "End checkUserFulfillRestrictions <br>\n";

		return $ret;
	}
}

?>