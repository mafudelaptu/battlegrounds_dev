<?php
/*
 * Copyright 2013 Artur Leinweber Date: 2013-08-31
 */
class UserNotification {
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-08-31
	*/
	function setUserNotificationAsChecked($id) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start setUserNotificationAsChecked <br>\n";
		
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
		
		if ($id > 0) {
			
			$sql = "UPDATE `UserNotifications`
					SET Checked = 1, TimestampChecked = " . time () . "
							WHERE SteamID = " . secureNumber ( $steamID ) . " AND UserNotificationID = " . ( int ) $id . "
									";
			$data = $DB->update ( $sql );
			$ret ['debug'] .= p ( $sql, 1 );
			
			$ret ['status'] = $data;
		} else {
			$ret ['status'] = "id == 0";
		}
		$ret ['debug'] .= "End setUserNotificationAsChecked <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-08-31
	*/
	function sendNotificationToDuoPartner($groupID, $smarty) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start sendNotificationToDuoPartner <br>\n";
		
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
		if ($groupID > 0) {
			$Group = new Group ();
			$retGG = $Group->getDuoPartnerOfGroupOfUser ( $steamID, $groupID );
			$partnerData = $retGG ['data'];
			
			$insertArray = array ();
			$insertArray ['SteamID'] = secureNumber ( $partnerData ['SteamID'] );
			$insertArray ['TimestampCreated'] = time ();
			$insertArray ['NotificationTypeID'] = 1;
			$insertArray ['Href'] = "";
			$insertArray ['CreatedBy'] = secureNumber ( $steamID );
			
			$retG = $Group->getGroupData ( $groupID );
			$groupData = $retG ['data'];
			
			$User = new User ();
			$userData = $User->getUserData ( $steamID );
			
			$ret ['debug'] .= p ( $userData, 1 );
			$ret ['debug'] .= p ( $groupData, 1 );
			$smarty->assign ( 'userData', $userData );
			$smarty->assign ( 'groupData', $groupData );
			$html = $smarty->fetch ( "notifications/duoQueueInvite.tpl" );
			
			$insertArray ['Text'] = mysql_real_escape_string ( $html );
			
			$retIns = $DB->insert ( "UserNotifications", $insertArray );
			
			$ret ['status'] = $retIns;
		} else {
			$ret ['status'] = "groupID == 0";
		}
		$ret ['debug'] .= "End sendNotificationToDuoPartner <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber Date: 2013-08-31
	*/
	function sendPingNotification($pingSteamID, $matchID) {
		$ret = array ();
		$DB = new DB ();
		$con = $DB->conDB ();
		$ret ['debug'] .= "Start sendPingNotification <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
		if ($steamID > 0 && $matchID > 0 && $pingSteamID > 0) {
			// Checken ob auch in match
			$Match = new Match();
			$inMatch = $Match->isPlayerInMatch($matchID, $pingSteamID);
			
			if($inMatch){
				$sql = "SELECT *
							FROM `UserNotifications` un
							WHERE un.SteamID = ".secureNumber($pingSteamID)." AND NotificationTypeID = 2 AND TimestampCreated > ".(time()-15)." AND Checked = 0
							ORDER BY TimestampCreated DESC
							LIMIT 1
			";
				$count = $DB->countRows($sql);
				$ret['debug'] .= p($sql,1);
				if($count > 0){
					$ret ['status'] = "not pingable";
				}
				else{
					// insert Ping
					$insertArray = array ();
					$insertArray ['SteamID'] = secureNumber ( $pingSteamID );
					$insertArray ['TimestampCreated'] = time ();
					$insertArray ['NotificationTypeID'] = 2;
					$insertArray ['Href'] = "";
					$insertArray ['Text'] = "";
					$insertArray ['CreatedBy'] = secureNumber ( $steamID );
				
					$retIns = $DB->insert("UserNotifications", $insertArray);
				
					$ret ['status'] = $retIns;
				}
			}
			else{
				$ret['status'] = "error";
			}
			
		} else {
			$ret ['status'] = "steamID == 0 || matchID = 0 || pingSteamID = 0";
		}
		$ret ['debug'] .= "End sendPingNotification <br>\n";
		return $ret;
	}
}

?>