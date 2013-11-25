<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class Banlist{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function insertBan($steamID, $reasonID=1, $reasonText = ""){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertBan <br>\n";
		$User = new User();
		$isAdmin = $User->isAdmin($_SESSION['user']['steamID']);


		if($isAdmin){
			if($steamID > 0){
					
				$insertArray = array();
				$insertArray['SteamID'] = secureNumber($steamID);
				$insertArray['BannedAt'] = time();
					
				$retTill = $this->calculateBannedTill($steamID);
				$bannedTill = $retTill['data'];
					
				$insertArray['BannedTill'] = (int) $bannedTill;
				$insertArray['BanlistReasonID'] = (int)$reasonID;
				$insertArray['Display'] = (int)1;
				$insertArray['BanReasonText'] = mysql_real_escape_string($reasonText);
				$insertArray['BannedBy'] = secureNumber($_SESSION['user']['steamID']);
					
				$retIns = $DB->insert("Banlist", $insertArray);
				$ret['debug'] .= p($retIns,1);
					
				$ret['status'] = $retIns;
			}
			else{
				$ret['status'] = "SteamID = 0";
			}
		}
		else{
			$ret['debug'] .= "because fuck you";
			$ret['status'] = false;
		}

		$ret['debug'] .= "End insertBan <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/

	function insertBanViaCronjob($steamID, $reasonID=1){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start insertBan <br>\n";
		$User = new User();
		$isAdmin = $User->isAdmin($_SESSION['user']['steamID']);

		if($steamID > 0){

			$insertArray = array();
			$insertArray['SteamID'] = secureNumber($steamID);
			$insertArray['BannedAt'] = time();

			$retTill = $this->calculateBannedTill($steamID);
			$bannedTill = $retTill['data'];

			$insertArray['BannedTill'] = (int) $bannedTill;
			$insertArray['BanlistReasonID'] = (int)$reasonID;
			$insertArray['Display'] = (int)1;
			$insertArray['BanReasonText'] = mysql_real_escape_string($reasonText);
			$insertArray['BannedBy'] = secureNumber($_SESSION['user']['steamID']);

			$retIns = $DB->insert("Banlist", $insertArray);
			$ret['debug'] .= p($retIns,1);

			$ret['status'] = $retIns;
		}
		else{
			$ret['status'] = "SteamID = 0";
		}

		$ret['debug'] .= "End insertBan <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function calculateBannedTill($steamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start calculateBannedTill <br>\n";
		if($steamID > 0){
			// bereits vorhandene BanCount auslesen
			//$retCount = $this->countBans($steamID);
			$retCount = $this->getAllBansOfPlayer($steamID, true);
			$count = $retCount['count'];

			switch($count){
				// Verwarnung
				case "0":
					$bannedTill = time();
					break;
				// Queue Ban
				case "1":
					// 24h ban
					$bannedTill = time() + (24 * 60 * 60);
					break;
				// Queue Ban
				case "2":
					// 3 Tage ban
					$bannedTill = time() + (24 * 60 * 60*3);
					break;
				// Queue Ban
				case "3":
					// 7 Tage ban
					$bannedTill = time() + (24 * 60 * 60*7);
					break;	
				case "4":
				// 21 Tage Ban
					$bannedTill = time() + (24 * 60 * 60*21);
					break;
				// Queue Ban
				case "5":
					// Perma ban
					$bannedTill = 99999999999;
					break;
			}
			$ret['data'] = $bannedTill;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "SteamID = 0";
		}
			
		$ret['debug'] .= "End calculateBannedTill <br>\n";
			
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function countBans($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		$ret['debug'] .= "Start countBans <br>\n";
		if($steamID > 0){
			$sql = "SELECT *
					FROM `Banlist`
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			$count = count($data);
			$ret['data'] = $data;
			$ret['count'] = $count;
			$ret['status'] = true;

		}
		else{
			$ret['status'] = "SteamID = 0";
		}

		$ret['debug'] .= "End countBans <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function checkForBansOfPlayer($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		$ret['debug'] .= "Start checkForBansOfPlayer <br>\n";
		if($steamID > 0){
			//$countBans = $this->countBans($steamID);
			$countBans = $this->getAllBansOfPlayer($steamID, true);
			
			$ret['debug'] .= p($countBans,1);
			// wenn er mindestens einen Ban hat
			// konntrollieren ob er schon alle abgesessen hat
			if($countBans['count'] > 0){
				$sql = "SELECT *
						FROM `Banlist`
						WHERE SteamID = ".secureNumber($steamID)."
								AND Display = 1
								AND BannedTill > ".time()."
										ORDER BY BannedTill DESC
										LIMIT 1
										";
				$ret['debug'] .= p($sql,1);
				$data = $DB->select($sql);
				$ret['debug'] .= p($data,1);
				// Wenn Datensatz vorhanden -> dann hat ban und darf nicht joinen
				if(is_array($data) && count($data) > 0){
					$ret['debug'] .= p("BANNED!",1);
					$ret['banned'] = true;
				}
				else{

					$ret['banned'] = false;
				}
				if($countBans['count'] == 1){
					$ret['display'] = (int)$countBans['data'][0]['Display'];
				}
				$ret['data'] = $data;
				$ret['banCounts'] = (int)$countBans['count'];
			}
			else{
				$ret['banCounts'] = (int)$countBans['count'];
				$ret['banned'] = false;
			}
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End checkForBansOfPlayer <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getCurrentBanDataOfPlayer($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}

		$ret['debug'] .= "Start getCurrentBanDataOfPlayer <br>\n";
		if($steamID > 0){
			$retBans = $this->checkForBansOfPlayer();
			if($retBans['banned']){
				$sql = "SELECT b.*, u.*, br.Name as Reason, u.SteamID as BannedBySteamID
						FROM `Banlist` b JOIN BanlistReasons br ON b.BanlistReasonID = br.BanlistReasonID
						LEFT JOIN User u ON u.SteamID = b.BannedBy
						WHERE b.SteamID = ".secureNumber($steamID)."
								AND b.Display = 1
								AND b.BannedTill > ".time()."
										ORDER BY b.BannedTill DESC
										LIMIT 1
										";
				$ret['debug'] .= p($sql,1);
					
				$data = $DB->select($sql);
				$ret['banned'] = true;
				$ret['data'] = $data;
				$ret['status'] = true;
			}
			else{
				$ret['banned'] = false;
				$ret['status'] = false;
			}

		}
		else{
			$ret['status'] = "MatchID = 0";
		}

		$ret['debug'] .= "End getCurrentBanDataOfPlayer <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function cleanBansOfPlayer($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		$ret['debug'] .= "Start cleanBansOfPlayer <br>\n";
		$User = new User();

		$retC = $this->checkForBansOfPlayer($steamID);
		$banData = $retC['data'];
		$isAdmin = $User->isAdmin($_SESSION['user']['steamID']);
		if($isAdmin || ($banData['BannedTill'] <= time() && $steamID = $_SESSION['user']['steamID'])){
			if($steamID > 0){
				$sql = "UPDATE `Banlist`
						SET Display = 0
						WHERE SteamID = ".secureNumber($steamID)."
								";
				$ret['debug'] .= p($sql,1);
				$data = $DB->update($sql);
				$ret['status'] = $data;
			}
			else{
				$ret['status'] = "MatchID = 0";
			}
		}


		$ret['debug'] .= "End cleanBansOfPlayer <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function removeLastBan($steamID = 0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		if($steamID == 0){
			$steamID = $_SESSION['user']['steamID'];
		}
		$ret['debug'] .= "Start removeLastBan <br>\n";
		$User = new User();
		$isAdmin = $User->isAdmin($_SESSION['user']['steamID']);
		if($isAdmin){

			if($steamID > 0){

				$sql = "DELETE FROM Banlist
						WHERE SteamID = ".secureNumber($steamID)."
								ORDER BY BannedAt DESC
								LIMIT 1
								";
				$ret['debug'] .= p($sql,1);
				$data = $DB->update($sql);
				$ret['status'] = $data;
			}
			else{
				$ret['status'] = "SteamID = 0";
			}
		}
		$ret['debug'] .= "End removeLastBan <br>\n";

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getBannedPlayers($userInfo=false, $smarty=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();

		$join = "";
		$select = "";
		if($userInfo){
			$join = " JOIN User u ON b.SteamID = u.SteamID
					LEFT JOIN User u2 ON b.BannedBy = u2.SteamID";
			$select = ", u.Name as UserName, u2.Name as BannedBy, u2.Avatar as BannedByAvatar, u2.SteamID as BannedBySteamID, u.SteamID as SteamID, u.Avatar as Avatar";

		}

		$ret['debug'] .= "Start getBannedPlayers <br>\n";

		$sql = "SELECT  *, br.Name as ReasonName ".$select."
				FROM Banlist b ".$join."
						LEFT JOIN BanlistReasons br ON b.BanlistReasonID = br.BanlistReasonID
						";
		$ret['debug'] .= p($sql,1);
		$data = $DB->multiSelect($sql);
		$ret['data'] = $data;

		if($smarty){
			$smarty->assign('data', $data);
			$smarty->assign('TableID',"bannedPlayersTable");
			$table = $smarty->fetch("admin/banPanel/tablePrototype.tpl");
			$ret['tableHTML'] = $table;
		}
		$ret['status'] = true;

		$ret['debug'] .= "End getBannedPlayers <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-09-18
	*/
	function getAllBansOfPlayer($steamID = 0, $justActive=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllBansOfPlayer <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){
			$whereActive = "";
			if($justActive){
				$whereActive = " AND b.Display = 1";
			}

			$sql = "SELECT b.*, br.Name as ReasonName, u.SteamID as BannedBySteamID, u.Avatar as BannedByAvatar, u.Name as BannedByName, (b.BannedAt+1728000) as Expires
					FROM `Banlist` b JOIN BanlistReasons br ON b.BanlistReasonID = br.BanlistReasonID
					LEFT JOIN User u ON u.SteamID = b.BannedBy
					WHERE b.SteamID = ".secureNumber($steamID)." ".$whereActive."
							ORDER BY BannedAt DESC	
			";
			$ret['debug'] .= p($sql,1);
			$data = $DB->multiSelect($sql);

			$count = count($data);
			$ret['data'] = $data;
			$ret['count'] = $count;
			$ret['status'] = true;

		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End getAllBansOfPlayer <br>\n";
		return $ret;
	}

	function isUserPermaBanned($steamID=0){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start isUserPermaBanned <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($steamID > 0){

			$sql = "SELECT SteamID
					FROM `PermaBanned`
					WHERE SteamID = ".secureNumber($steamID)."
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);
			if(is_array($data) && count($data) > 0){
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
		}
		else{
			$ret['status'] = "steamID == 0";
		}
		$ret['debug'] .= "End isUserPermaBanned <br>\n";
		return $ret;
	}


	function getAllPermaBannedUser(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllPermaBannedUser <br>\n";

		$sql = "SELECT u.Name as UserName, u.Avatar, pb.SteamID, pb.TimestampBanned, blr.Name as BanlistReason
				FROM `PermaBanned` pb LEFT JOIN User u ON u.SteamID = pb.SteamID
				LEFT JOIN BanlistReasons blr ON blr.BanlistReasonID = pb.BanlistReasonID
				";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);

		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getAllPermaBannedUser <br>\n";
		return $ret;
	}

	function deletePermaBan($banSteamID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start deletePermaBan <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		$User = new User();
		$isAdmin = $User->isAdmin($steamID);
		if($isAdmin){
			if($banSteamID > 0){
				$sql = "DELETE FROM PermaBanned
						WHERE SteamID = ".secureNumber($banSteamID)."
				";
				$data = $DB->delete($sql); 
				$ret['debug'] .= p($sql,1);
				$ret['status'] = $data;
			}
			else{
				$ret['status'] = "banSteamID == 0";
			}
		}
		else{
			$ret['status'] = false;
		}
		
		
		$ret['debug'] .= "End deletePermaBan <br>\n";
		return $ret;
	}
}

?>