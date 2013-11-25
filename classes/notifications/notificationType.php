<?php 

class NotificationType{

	function getAllNotificationTypes($forSelect=false){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start getAllNotificationTypes <br>\n";

		$sql = "SELECT *
				FROM `NotificationType`
				WHERE Active = 1
				";
		$data = $DB->multiSelect($sql);
		$ret['debug'] .= p($sql,1);

		if ($forSelect) {
			if(is_array($data) && count($data) > 0){
				$tmp = array();
				foreach ($data as $k => $v) {
					$tmp[$v['NotificationTypeID']] = $v['Name'];
				}
				$ret['data'] = $tmp;
			}
		}
		else{
			$ret['data'] = $data;
		}

		$ret['status'] = true;

		$ret['debug'] .= "End getAllNotificationTypes <br>\n";
		return $ret;
	}

}

?>