<?php 
/*
 * Copyright 2013 Artur Leinweber
* Date: 2013-01-01
*/
class GlobalUser{
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function isEmailUnique($email){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDbGlobal();


		if($email != ""){
			$sql = "SELECT *
					FROM `User`
					WHERE Email = '".mysql_real_escape_string(trim($email))."'
							";

			$count = $DB->countRows($sql);
			//$ret['debug'] .= p($sql,1);
			if($count > 0){
				$ret = false;
			}
			else{
				$ret = true;
			}
		}
		else{
			$ret['status'] = "email == 0";
		}

		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
	function getGlobalUser($email){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDbGlobal();
		$ret['debug'] .= "Start getGlobalUser <br>\n";

		if($email != ""){
			$sql = "SELECT *
					FROM `User`
					WHERE Email = '".mysql_real_escape_string(trim($email))."'
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "email == 0";
		}
		$ret['debug'] .= "End getGlobalUser <br>\n";
		return $ret;
	}
	/*
	 * Copyright 2013 Artur Leinweber
	* Date: 2013-01-01
	*/
function getGlobalUserByID($id){
		$ret = array();
		$DB = new DB();
		$con = $DB->conDbGlobal();
		$ret['debug'] .= "Start getGlobalUserByID <br>\n";

		if((int)$id > 0){
			$sql = "SELECT *
					FROM `User`
					WHERE UserID = '".(int)$id."'
							";
			$data = $DB->select($sql);
			$ret['debug'] .= p($sql,1);
			$ret['data'] = $data;
			$ret['status'] = true;
		}
		else{
			$ret['status'] = "id == 0";
		}
		$ret['debug'] .= "End getGlobalUserByID <br>\n";
		return $ret;
	} 
}

?>