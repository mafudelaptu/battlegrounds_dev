<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class UserGames{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function isGameLinkedToUser($gameID, $userID){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDbGlobal();
		$ret['debug'] .= "Start isGameLinkedToUser <br>\n";

		if($gameID > 0 && $userID > 0){
			$sql = "SELECT *
							FROM `UserGames` 
							WHERE GameID = ".(int)$gameID." AND UserID = ".(int) $userID."
			";
			$data = $DB->select($sql); 
			$ret['debug'] .= p($sql,1);
			if($data){
				$ret['data'] = $data;
				$ret['status'] = true;
			}
			else{
				$ret['status'] = false;
			}
			
		}
		else{
			$ret['status'] = "gameID == 0 && userID == 0";
		}
		$ret['debug'] .= "End isGameLinkedToUser <br>\n";
		return $ret;
	}

}

?>