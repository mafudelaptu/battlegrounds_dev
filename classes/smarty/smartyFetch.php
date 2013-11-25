<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class SmartyFetch{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function fetchWaitingForOtherPlayersTPL($smarty, $data){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start fetchWaitingForOtherPlayersTPL <br>\n";

		if(is_array($data) && count($data) > 0){
			$smarty->assign("data", $data);
			$fetchedData = $smarty->fetch("find_match/modals/waitingForOtherPlayers.tpl");
			$ret['data'] = $fetchedData;
			$ret['status'] = true;
		}

		else{
			$ret['status'] = "data = 0";
		}


		$ret['debug'] .= "End fetchWaitingForOtherPlayersTPL <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function fetchEditUserPointsTPL($smarty, $data){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start fetchEditUserPointsTPL <br>\n";

		if(is_array($data) && count($data) > 0){
			$smarty->assign("data", $data);
				
			// PointsTypeArr auslesen
			$PointsType = new PointsType();
			$retPT = $PointsType->getAllPointTypesForSelectOptions();
			$smarty->assign("pointsTypeArr", $retPT['data']);
				
			$fetchedData = $smarty->fetch("admin/editMatchResult/editUserPoints.tpl");
			$ret['data'] = $fetchedData;
			$ret['status'] = true;
		}

		else{
			$ret['status'] = "data = 0";
		}


		$ret['debug'] .= "End fetchEditUserPointsTPL <br>\n";

		return $ret;
	}

	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function fetchPointsTypeSelectTPL($smarty, $id=1){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start fetchPointsTypeSelectTPL <br>\n";

		// PointsTypeArr auslesen
		$PointsType = new PointsType();
		$retPT = $PointsType->getAllPointTypesForSelectOptions();
		$smarty->assign("data", $retPT['data']);
		$smarty->assign("id", "mRPT".(int)$id);
		
		$fetchedData = $smarty->fetch("prototypes/selectTag.tpl");
		$ret['data'] = $fetchedData;
		$ret['status'] = true;


		$ret['debug'] .= "End fetchPointsTypeSelectTPL <br>\n";

		return $ret;
	}

	
/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function fetchEditNewsTPL($smarty, $id){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start fetchEditNewsTPL <br>\n";

		// PointsTypeArr auslesen
		$News = new News();
		$retN = $News->getDataOfNews($id);
		$ret['debug'] .= p($retN,1);
		
		$smarty->assign("data", $retN['data']);
		
		$fetchedData = $smarty->fetch("admin/newsPanel/editNews.tpl");
		$ret['data'] = $fetchedData;
		$ret['newsData'] = $retN['data'];
		$ret['status'] = true;


		$ret['debug'] .= "End fetchEditNewsTPL <br>\n";

		return $ret;
	}
	
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function fetchEditNotificationTPL($smarty, $id){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDB();
		$ret['debug'] .= "Start fetchEditNotificationTPL <br>\n";
	
		// PointsTypeArr auslesen
		$GlobalNotification = new GlobalNotification();
		$retGN = $GlobalNotification->getDataOfNotification($id);
		$ret['debug'] .= p($retGN,1);
	
		$NotificationType = new NotificationType();
		$retNT = $NotificationType->getAllNotificationTypes(true);
		
		$smarty->assign("data", $retGN['data']);
		$smarty->assign("notificationTypes", $retNT['data']);
		
		$fetchedData = $smarty->fetch("admin/notificationPanel/editNotification.tpl");
		$ret['data'] = $fetchedData;
		$ret['notificationData'] = $retGN['data'];
		$ret['status'] = true;
	
	
		$ret['debug'] .= "End fetchEditNotificationTPL <br>\n";
	
		return $ret;
	}
}

?>