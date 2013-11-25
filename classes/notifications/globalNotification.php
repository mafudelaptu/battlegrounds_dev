<?php 

class GlobalNotification{

	function createNewNotification($notificationTypeID, $text, $link, $active){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start createNewNotification <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}

		if($notificationTypeID > 0){
			$User = new User();
			$isAdmin = $User->isAdmin($steamID);
			if($isAdmin){
				$data = array();
				$data['NotificationTypeID'] = (int) $notificationTypeID;
				$data['Text'] = mysql_real_escape_string($text);
				$data['Href'] = mysql_real_escape_string($link);
				$data['CreatedBy'] = secureNumber($steamID);
				$data['TimestampCreated'] = time();
				$data['Active'] = (int)$active;
				$ret['debug'] .= p($data,1);
				
				$retIns = $DB->insert("GlobalNotifications", $data);

				$ret['status'] = $retIns;
			}
			else{
				$ret['status'] = "noAdmin";
			}

		}
		else{
			$ret['status'] = "notificationTypeID == 0";
		}
		$ret['debug'] .= "End createNewNotification <br>\n";
		return $ret;
	}

	function getAllGlobalNotifications(){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllGlobalNotifications <br>\n";

		$sql = "SELECT gn.*, nt.Name as TypeName, u.Avatar, u.Name as UserName
				FROM `GlobalNotifications` gn JOIN NotificationType nt ON nt.NotificationTypeID = gn.NotificationTypeID
					LEFT JOIN `User` u ON u.SteamID = gn.CreatedBy 
				ORDER BY GlobalNotificationID DESC
				";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);
		$ret['data'] = $data;
		$ret['status'] = true;

		$ret['debug'] .= "End getAllGlobalNotifications <br>\n";
		return $ret;
	}
	
	function getDataOfNotification($id){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getDataOfNotification <br>\n";
		
		if($id > 0){
			$sql = "SELECT *
					FROM `GlobalNotifications`
					WHERE GlobalNotificationID = ".(int)($id)."
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "id == 0";
		}
		$ret['debug'] .= "End getDataOfNotification <br>\n";
		return $ret;
	}

	function updateNotification($id, $notificationTypeID, $text, $link, $active){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start updateNotification <br>\n";
		
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
		$User = new User();
		$isAdmin = $User->isAdmin($steamID);
		if($isAdmin){
			$sql = "UPDATE `GlobalNotifications`
					SET Text='".mysql_real_escape_string($text)."',
							Href='".mysql_real_escape_string($link)."',
									NotificationTypeID=".(int)($notificationTypeID).",
										Active = ".(int) $active."
						WHERE GlobalNotificationID = ".(int)($id)."
			";
			$data = $DB->update($sql);
			$ret['debug'] .= p($sql,1);
			$ret['status'] = $data;
		}
		else{
			$ret['status'] = "noAdmin";
		}
		
		
		$ret['debug'] .= "End updateNotification <br>\n";
		return $ret;
	}
	function toggleActiveNotification($id, $active){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start toggleActiveNotification <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
	
		if($id > 0){
			$User = new User();
			$isAdmin = $User->isAdmin($steamID);
			if($isAdmin){
				if($active == "1"){
					$setActive = 0;
				}
				else{
					$setActive = 1;
				}
				$sql = "Update `GlobalNotifications`
						SET `Active` = ".(int) $setActive."
								WHERE GlobalNotificationID = ".(int)$id."
										";
				$data = $DB->update($sql);
				$ret['debug'] .= p($sql,1);
				$ret['status'] = $data;
			}
			else{
				$ret['status'] = "noAdmin";
			}
	
		}
		else{
			$ret['status'] = "id == 0";
		}
		$ret['debug'] .= "End toggleActiveNotification <br>\n";
		return $ret;
	}
	
	function deleteNotification($id){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start deleteNotification <br>\n";
		if ($steamID == 0) {
			$steamID = $_SESSION ['user'] ['steamID'];
		}
	
		if($id > 0){
			$User = new User();
			$isAdmin = $User->isAdmin($steamID);
			if($isAdmin){
				$sql = "DELETE FROM `GlobalNotifications`
						WHERE GlobalNotificationID = ".(int)($id)."
								";
				$data = $DB->delete($sql);
				$ret['debug'] .= p($sql,1);
				$ret['status'] = $data;
			}
			else{
				$ret['status'] = "noAdmin";
			}
	
		}
		else{
			$ret['status'] = "id == 0";
		}
		$ret['debug'] .= "End deleteNotification <br>\n";
		return $ret;
	}
}

?>